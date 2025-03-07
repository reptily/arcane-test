<?php

namespace App\Repositories;

use App\Models\Video;

class VideoRepository
{
    public function getById(int $id): ?Video
    {
        return Video::query()->find($id);
    }
}