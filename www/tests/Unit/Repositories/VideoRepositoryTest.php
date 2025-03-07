<?php

namespace Tests\Unit\Repositories;

use App\Models\Video;
use App\Repositories\VideoRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VideoRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_by_id(): void
    {
        $video = Video::factory()->create();
        $this->assertInstanceOf(Video::class, app(VideoRepository::class)->getById($video->id));
    }
}
