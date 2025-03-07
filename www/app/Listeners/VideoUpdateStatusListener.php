<?php

namespace App\Listeners;

use App\Enums\VideoStatus;
use App\Events\VideoUploadInterface;
use App\Models\Video;
use App\Services\VideoService;

class VideoUpdateStatusListener
{
    public function __construct(private readonly VideoService $videoService)
    {
    }

    public function handle(VideoUploadInterface $event): void
    {
        Video::query()
            ->where('id', $event->getId())
            ->update([
                'status' => $event->isSuccess() ? VideoStatus::uploaded : VideoStatus::error
            ]);

        $this->videoService->setWaitStatusUploaded($event->getId());
    }
}
