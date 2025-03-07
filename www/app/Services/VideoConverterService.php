<?php

namespace App\Services;

use App\Enums\VideoSize;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Reptily\ArrayList\ArrayListInteger;
use Reptily\ArrayList\ArrayListString;

class VideoConverterService
{
    /**
     * [width, height, badwidth]
     */
    private const SIZES = [
        144 => [256, 144, 400000],
        240 => [426, 240, 800000],
        360 => [480, 360, 1200000],
    ];

    public function open(int $id): Video
    {
        $ffmpeg = FFMpeg::create();
        return $ffmpeg->open(storage_path(sprintf('app/%d.video', $id)));
    }

    public function resize(Video &$video, VideoSize $size): Video
    {
        $video
            ->filters()
            ->resize(new Dimension(self::SIZES[$size->value][0], self::SIZES[$size->value][1]))
            ->synchronize();

        return $video;
    }

    public function buildSegments(Video $video, int $id, VideoSize $size): void
    {
        $path = sprintf('%d/%s', $id, $size->value);
        Storage::createDirectory('videos/' . $path);
        $video->save(
            (new X264())->setAdditionalParameters([
                '-f', 'segment',
                '-segment_time', config('video.segment_time'),
                '-reset_timestamps', '1'
            ]),
            storage_path(sprintf('app/videos/%s/%%03d-x264.ts', $path))
        );
    }

    public function makePlayList(int $id): void
    {
        $directories = Storage::directories('videos/' . $id);

        if (empty($directories)) {
            Log::warning('Directories app/videos/' . $id . ' not found');
            return;
        }

        $resolutions = [];
        foreach ($directories as $directory) {
            $array = explode('/', $directory);
            $resolutions[] = (int) end($array);
        }

        Storage::makeDirectory('hls/' . $id);
        $this->makeRootM3U($id, new ArrayListInteger($resolutions));

        foreach ($resolutions as $resolution) {
            $segments = Storage::directories('app/videos/' . $id . '/' . $resolution);
            if (empty($segments)) {
                Log::warning('Directories app/videos/' . $id . '/' . $resolution . ' not found');
                continue;
            }

            $this->makeResolutionM3U($id, $resolution, new ArrayListString($segments));
        }
    }

    private function makeRootM3U(int $id, ArrayListInteger $resolutions): void
    {
        $m3u = [
            '#EXTM3U',
            '#EXT-X-VERSION:3'
        ];

        foreach ($resolutions as $resolution) {
            if (!array_key_exists($resolution, self::SIZES)) {
                continue;
            }

            $m3u[] = sprintf(
                '#EXT-X-STREAM-INF:BANDWIDTH=%d,RESOLUTION=%dx%d',
                self::SIZES[$resolution][2],
                self::SIZES[$resolution][0],
                self::SIZES[$resolution][1],
            );
            $m3u[] = sprintf(
                '%dp.m3u8',
                self::SIZES[$resolution][1],
            );
        }

        Storage::put('app/hls/' . $id . '/root.m3u8', implode(PHP_EOL, $m3u));
    }

    private function makeResolutionM3U(int $id, int $resolution, ArrayListString $segments): void
    {
        $m3u = [
            '#EXTM3U',
            '#EXT-X-VERSION:3',
            '#EXT-X-TARGETDURATION:' . config('video.segment_time'),
            '#EXT-X-MEDIA-SEQUENCE:0',
        ];

        foreach ($segments as $segment) {
            $m3u[] = '#EXTINF:' . config('video.segment_time') . '.0,';
            $m3u[] = $segment;
        }

        Storage::put('app/hls/' . $id . '/' . $resolution . '.m3u8', implode(PHP_EOL, $m3u));
    }
}