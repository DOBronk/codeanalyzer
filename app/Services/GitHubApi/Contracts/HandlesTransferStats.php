<?php

namespace App\Services\GitHubApi\Contracts;

use GuzzleHttp\TransferStats;

interface HandlesTransferStats
{
    public function handleTransferStats(TransferStats $stats);
}
