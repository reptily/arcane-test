<?php

namespace Tests\Feature;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseTransactions;

    public function test_show(): void
    {
        $video = Video::factory()->create();
        $response = $this->get('/api/videos/'. $video->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'playlist_url',
            ]
        ]);
    }
}
