<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Queue;
use App\Jobs\SendBrokerQueueJob;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_job_will_be_dispatched(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        $user->settings->gh_api_key = "ghp_apikey";

        $response = $this->actingAs($user)->post(route('codeanalyzer.createjob.post'), [
            'selections' => [['path' => '/Pad/naar/bestand.php', 'sha' => "abcedadfadfggsfq34"]],
            'owner' => 'DOBronk',
            'repository' => 'codeanalyzer',
            'branch' => 'lang'
        ]);

        Queue::assertPushed(SendBrokerQueueJob::class);
        $response->assertRedirect();
    }
}
