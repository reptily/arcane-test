<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class VideoUploadGarbageCollectorJob implements ShouldQueue
{
    use Queueable, Batchable;

    public function __construct(
        private readonly int $id,
    ) {
    }

    public function handle(): void
    {
        if (Storage::exists('app/video/' . $this->id . '.video')) {
            Storage::delete('app/video/' . $this->id . '.video');
        }
    }
}
