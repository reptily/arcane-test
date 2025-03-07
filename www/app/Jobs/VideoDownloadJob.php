<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class VideoDownloadJob implements ShouldQueue
{
    use Queueable, Batchable;

    public function __construct(
        private readonly int $id,
        private readonly string $url,
    ) {
    }

    public function handle(Client $client): void
    {
        $video = $client->get($this->url);
        Storage::put(sprintf('%d.video', $this->id), $video->getBody()->getContents(), 'public');
    }
}
