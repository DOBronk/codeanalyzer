<?php

namespace Tests\Feature\GitResources;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Tests\TestCase;

class AjaxResourcesTest extends TestCase
{
    use RefreshDatabase;
    private User $user;
    private $fakeData;
    private const REPO = [
        'owner' => 'dobronk',
        'repository' => 'codeanalyzer',
        'branch' => 'master'
    ];

    public function test_if_branches_are_sent_and_parsed()
    {
        $params = $this->getParams(2);
        $route = route('ajax.getbranches', $params);
        $response = $this->actingAs($this->user)->get($route);

        $response->assertStatus(200)
            ->assertJson($this->fakeData['branches']['response']);
    }
    public function test_if_branches_request_redirects_when_no_login()
    {
        $params = $this->getParams(2);
        $route = route('ajax.getbranches', $params);
        $response = $this->get($route);

        $response->assertStatus(302);
    }

    public function test_if_repositories_are_sent_and_parsed()
    {
        $route = route('ajax.getrepositories');
        $params =  $this->getParams(1);
        $response = $this->actingAs($this->user)->post($route, $params);

        $response->assertStatus(200)
            ->assertJson($this->fakeData['repo']['response']);
    }

    public function test_if_repositories_fail_with_wrong_data()
    {
        $route = route('ajax.getrepositories');
        $params =  ['owner2' => "\"\"\""];
        $response = $this->actingAs($this->user)->post($route, $params);

        $response->assertStatus(200);
    }

    public function test_if_trees_are_sent_and_parsed()
    {
        $route = route('ajax.gettree');
        $params = $this->getParams();
        $response = $this->actingAs($this->user)->post($route, $params);

        $response->assertStatus(200)
            ->assertJson($this->fakeData['tree']['response']);
    }

    private function createFakeData(): array
    {
        // Tree, make sure to 1: Add paths with folder, since folders will generate a seperate item in the response.
        // 2: Make sure to add multiple files in a folder, as these will be in a different array depth.
        // 3: Make sure to add nested folder, to see if children will be nested properly 
        // 4: Make sure not to include folder key attributes to the response validation, since these will be unique

        $request = ['tree' => [
            ['path' => "somepath/folder/firstfile.php", 'size' => '1250', 'sha' => '141fffdb3c3e696e25522147b5a482c4fe202338', 'type' => 'blob'],
            ['path' => "somepath/firstfile.php", 'size' => '244', 'sha' => '766f1f7e6504c4857b1bed1daf078fe1f6b36903', 'type' => 'blob'],
            ['path' => "somepath/secondfile.php", 'size' => '133', 'sha' => '3c820b3da45dd6a21835387e43289aecf8954347', 'type' => 'blob'],
            ['path' => "index.php", 'size' => '500', 'sha' => 'b4cc975f2e4a4cdf5d37d48e4a604d01faa043ac', 'type' => 'blob'],
        ]];

        $response = [
            [
                'data' => ['name' => 'somepath', 'type' => 'Folder', 'size' => '-'],
                'children' => [
                    [
                        'data' => ['name' => 'folder', 'type' => 'Folder', 'size' => '-'],
                        'children' => [
                            [
                                'key' => 'somepath/folder/firstfile.php:141fffdb3c3e696e25522147b5a482c4fe202338',
                                'data' => ['name' => 'firstfile.php', 'type' => 'File', 'size' => '1250']
                            ]
                        ]
                    ],
                    [
                        'key' => 'somepath/firstfile.php:766f1f7e6504c4857b1bed1daf078fe1f6b36903',
                        'data' => ['name' => 'firstfile.php', 'type' => 'File', 'size' => '244']
                    ],
                    [
                        'key' => 'somepath/secondfile.php:3c820b3da45dd6a21835387e43289aecf8954347',
                        'data' => ['name' => 'secondfile.php', 'type' => 'File', 'size' => '133']
                    ]
                ]
            ],
            [
                'key' => 'index.php:b4cc975f2e4a4cdf5d37d48e4a604d01faa043ac',
                'data' => ['name' => 'index.php', 'type' => 'File', 'size' => '500']
            ]
        ];
        $ret['tree'] = compact(['request', 'response']);
        //Repository
        $response  = [['testrepo1', 'testrepo2'], ['main', 'main']];
        $request = [['name' => 'testrepo1', 'default_branch' => 'main'], ['name' => 'testrepo2', 'default_branch' => 'main']];
        $ret['repo'] = compact(['request', 'response']);
        //Branches
        $response = ['testbranch1', 'testbranch2'];
        $request = [['name' => 'testbranch1'], ['name' => 'testbranch2']];
        $ret['branches'] = compact(['request', 'response']);

        return $ret;
    }

    private function getParams(int $count = 3): array
    {
        if ($count === 0 || $count > 3) {
            throw new InvalidArgumentException("Parameter count must be a minimum of 1 and maximum of 3, $count given.");
        }

        return $count === 3 ? self::REPO : array_slice(self::REPO, 0, $count);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeData ??= $this->createFakeData();
        $this->user = User::factory()->create();
        $this->user->settings->gh_api_key = "ghp_apikey";

        extract([...self::REPO, ...$this->fakeData]);

        Http::fake([
            "api.github.com/repos/$owner/$repository/git/trees/*" => Http::response((array)$tree['request']),
            "api.github.com/repos/$owner/$repository/*" => Http::response((array)$branches['request']),
            "api.github.com/users/$owner/*" => Http::response((array)$repo['request'])
        ]);
    }
}
