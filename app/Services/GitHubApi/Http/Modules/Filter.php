<?php

namespace App\Services\GitHubApi\Http\Modules;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use App\Services\GitHubApi\Abstracts\HttpModule;
use App\Services\GitHubApi\Contracts\HandlesResponse;

/**
 * Filter module to apply filters on responses. Can reduce memory footprint when processing asynchronous requests 
 * that receive many unused JSON variables without options to query columns specifically (see /users/repos in this project, where switching from rest API
 * to GraphQL does offer this, but only offers pagination by cursor that will force you to synchronously request all pages). 
 */

class Filter extends HttpModule implements HandlesResponse
{
    public function handleResponse(ResponseInterface $response, $params = null): ResponseInterface
    {
        $body = $response->getBody()->__tostring();

        if ($params && key_exists('filters', $params)) {
            foreach ($params['filters'] as $filter_type => $filter) {
                $newbody = match ($filter_type) {
                    'columns' => $this->columnFilter($body, $filter),
                    default => false
                };
                $body = $newbody ?: $body;
            }
            $response = $response->withBody(Utils::streamFor($body));
        }

        return $response;
    }

    private function columnFilter(string $body, array $columns): string|bool
    {
        $arr = json_decode($body, true);

        if ($arr === null) {
            Log::error('Filter: Error decoding JSON. Error: ' . json_last_error_msg() . "\nBody: $body");
            return false;
        }

        $tmp = [];

        for ($i = 0; $i < count($arr); $i++) {
            foreach ($columns as $column) {
                $tmp[$i][$column] = $arr[$i][$column];
            }
        }

        unset($arr);
        Log::debug("Filter: Column filter applied");
        return json_encode($tmp);
    }
}
