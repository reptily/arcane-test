<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoCreateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->resource['id'],
            'title'         => $this->resource['title'],
            'playlist_url'  => sprintf('/hls/%d/root.m3u8', $this->resource['id']),
        ];
    }
}
