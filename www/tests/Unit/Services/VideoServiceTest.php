<?php

namespace Tests\Unit\Services;

use App\Services\VideoService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class VideoServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_set_wait_status_uploaded(): void
    {
        $id = rand(1000, 9999);
        $tag = sprintf('video-uploading:%d', $id);
        Cache::set($tag, true);
        app(VideoService::class)->setWaitStatusUploaded($id);
        $this->assertFalse(Cache::get($tag, false));
    }

    public function test_set_wait_status_uploading(): void
    {
        $id = rand(1000, 9999);
        $tag = sprintf('video-uploading:%d', $id);
        Cache::set($tag, true);
        app(VideoService::class)->setWaitStatusUploading($id);
        $this->assertTrue(Cache::get($tag, false));
    }
}
