<?php

namespace App\Jobs;

use App\Enums\VideoSize;
use App\Services\VideoConverterService;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class VideoConverterJob implements ShouldQueue
{
    use Queueable, Batchable;

    public function __construct(
        private readonly int $id,
        private VideoSize $size,
    ) {

    }

    public function handle(VideoConverterService $videoConverterService): void
    {
        if (!Storage::exists($this->id . '.video')) {
            return;
        }

        $video = $videoConverterService->open($this->id);
        $videoConverterService->resize($video, $this->size);
        $videoConverterService->buildSegments($video, $this->id, $this->size);
    }
}
