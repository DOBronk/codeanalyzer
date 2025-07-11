<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobRequest;
use App\Jobs\SendBrokerQueueJob;
use Illuminate\Support\Facades\DB;
use App\Models\Jobs;

class JobStep2Controller extends Controller
{
    public function __invoke(CreateJobRequest $request)
    {
        if (!isset($request->selectedItems)) {
            return back()->withError("Geen bestanden geselecteerd!");
        }

        DB::transaction(function () use ($request) {
            $job = Jobs::create(['user_id' => $request->user()->id, ...$request->validated()]);

            $items = array_map(function ($item) {
                [$blobSha, $path] = explode('|', $item);
                return [
                    'blob_sha' => $blobSha,
                    'path' => $path,
                ];
            }, $request->selectedItems);

            $job->items()->createMany($items);

            dispatch(new SendBrokerQueueJob($job));
        });

        return redirect()->route("codeanalyzer.index");
    }
}
