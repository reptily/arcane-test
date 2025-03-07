<?php

namespace App\Jobs;

use App\Services\VideoConverterService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class VideoPlayListCreateJob implements ShouldQueue
{
    use Queueable, Batchable;

    public function __construct(
        private readonly int $id,
    ) {
    }

    public function handle(VideoConverterService $videoConverterService): void
    {
        $videoConverterService->makePlayList($this->id);
    }
}
