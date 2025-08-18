<?php

namespace Tests\Feature\GitResources;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class AjaxResourcesTest extends AjaxResources
{
    use RefreshDatabase;
    public function test_if_branches_are_sent()
    {
        $reply = ['testbranch1', 'testbranch2'];
        Http::fake([
            'api.github.com/repos/dobronk/codeanalyzer/branches' => [['name' => 'testbranch1'], ['name' => 'testbranch2']]
        ]);
        $user = User::factory()->create();
        $user->settings->gh_api_key = "apikey";
        $route = route('ajax.getbranches');
        $params = ['owner' => 'dobronk', 'repository' => 'codeanalyzer'];
        $response = $this->actingAs($user)->post($route, $params);

        $response->assertStatus(200);
        $response->assertContent(json_encode($reply));
    }

    public function test_if_repositories_are_sent()
    {
        $reply = [['testrepo1', 'testrepo2'], ['main', 'main']];

        Http::fake([
            'api.github.com/users/dobronk/repos' => [['name' => 'testrepo1', 'default_branch' => 'main'], ['name' => 'testrepo2', 'default_branch' => 'main']]
        ]);
        $user = User::factory()->create();
        $user->settings->gh_api_key = "apikey";
        $route = route('ajax.getrepositories');
        $params = ['owner' => 'dobronk'];
        $response = $this->actingAs($user)->post($route, $params);

        $response->assertStatus(200);
        $response->assertContent(json_encode($reply));
    }

    public function test_if_trees_are_sent()
    {
        $rep = ['tree' => [['path' => "somepath.php", 'size' => '244', 'sha' => '23fkljaour8we894357hjhkasf', 'type' => 'blob']]];
        $reply = [['key' => "somepath.php:23fkljaour8we894357hjhkasf", 'data' => ['name' => "somepath.php", 'type' => 'File', 'size' => '244']]];

        Http::fake([
            'api.github.com/repos/dobronk/codeanalyzer/git/trees/*' => $rep
        ]);

        $user = User::factory()->create();
        $user->settings->gh_api_key = "apikey";
        $route = route('ajax.gettree');
        $params = ['owner' => 'dobronk', 'repository' => 'codeanalyzer', 'branch' => 'main'];
        $response = $this->actingAs($user)->post($route, $params);

        $response->assertStatus(200);
        $response->assertContent(json_encode($reply));
    }
}
