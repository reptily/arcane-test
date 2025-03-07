<?php

namespace App\Services;

use App\Contract;
use App\Enums\VideoStatus;
use App\Models\Video;
use Illuminate\Support\Facades\Cache;

class VideoService
{
    public function __construct(private readonly BrokerInterface $brokerService)
    {
    }

    public function appendUploadSheet(string $url, string $title): int
    {
        $video = Video::create([
            'title'     => $title,
            'status'    => VideoStatus::uploading,
        ]);

        $this->brokerService
            ->topic(config('broker.topics.video_upload'))
            ->addMessage([
                Contract::FILED_VIDEO_UPLOAD_TOPIC_ID  => $video->id,
                Contract::FILED_VIDEO_UPLOAD_TOPIC_URL => $url,
            ])
            ->send();

        $this->setWaitStatusUploading($video->id);

        return $video->id;
    }

    public function waitUploading(int $id): bool
    {
        do {
            usleep(100);
        } while ($this->getWaitStatus($id) === true);

        return !$this->getWaitStatus($id);
    }

    public function setWaitStatusUploaded(int $id): void
    {
        Cache::forget($this->getWaitCacheTag($id));
    }

    public function setWaitStatusUploading(int $id): void
    {
        Cache::set($this->getWaitCacheTag($id), true);
    }

    public function getWaitStatus(int $id): bool
    {
        return Cache::get($this->getWaitCacheTag($id), false);
    }

    private function getWaitCacheTag(int $id): string
    {
        return sprintf('video-uploading:%d', $id);
    }
}