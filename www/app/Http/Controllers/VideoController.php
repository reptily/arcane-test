<?php

namespace App\Http\Controllers;

use App\Attributes\LongPolling;
use App\Http\Requests\VideoCreateRequest;
use App\Http\Resources\VideoCreateResource;
use App\Http\Resources\VideoShowResource;
use App\Repositories\VideoRepository;
use App\Services\VideoService;
use Symfony\Component\HttpKernel\Attribute\Cache;

final class VideoController extends Controller
{
    #[Cache(maxage: 3600)]
    public function show($id, VideoRepository $videoRepository)
    {
        $video = $videoRepository->getById((int)$id);
        if($video === null) {
            abort(404);
        }

        return new VideoShowResource([
            'id'            => $video->id,
            'title'         => $video->title,
        ]);
    }

    #[LongPolling(maxwait:3600)]
    public function create(
        VideoCreateRequest $request,
        VideoService $videoService,
        VideoRepository $videoRepository,
    ) {
        $id = $videoService->appendUploadSheet($request->url, $request->title);
        $resource = $videoService->waitUploading($id);

        $video = $videoRepository->getById($id);

        return $resource || $video === null
            ? new VideoCreateResource([
                'id'    => $video->id,
                'title' => $video->title,
            ])
            : response(['status' => 'upload_error'], 500);
    }
}
