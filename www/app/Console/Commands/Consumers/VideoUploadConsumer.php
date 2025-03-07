<?php

namespace App\Console\Commands\Consumers;

use App\Contract;
use App\Enums\VideoSize;
use App\Events\VideoUploadedEvent;
use App\Jobs\VideoConverterJob;
use App\Jobs\VideoDownloadJob;
use App\Jobs\VideoPlayListCreateJob;
use App\Jobs\VideoUploadGarbageCollectorJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Contracts\MessageConsumer;use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\ConsumerMessage;
use Throwable;

class VideoUploadConsumer extends Command
{
    protected $signature = "consume:video-upload";
    protected $description = "Consume Kafka messages from 'video-upload'";

    public function handle()
    {
        $consumer = Kafka::consumer([config('broker.topics.video_upload')])
            ->withAutoCommit()
            ->withHandler(function(ConsumerMessage $message, MessageConsumer $consumer) {
                $data = $message->getBody()[Contract::FILED_BROKER_MESSAGE];

                Bus::batch([
                    new VideoConverterJob(
                        $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID],
                        VideoSize::x144,
                    ),
                    new VideoConverterJob(
                        $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID],
                        VideoSize::x240,
                    ),
                    new VideoConverterJob(
                        $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID],
                        VideoSize::x360,
                    ),
                ])->before(function () use ($data) {
                    dispatch(new VideoDownloadJob(
                        $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID],
                        $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_URL],
                    ));
                })->finally(function () use ($data) {
                    Bus::chain([
                        new VideoPlayListCreateJob($data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID]),
                        fn() => event(new VideoUploadedEvent($data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID], true)),
                        new VideoUploadGarbageCollectorJob($data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID]),
                    ])->dispatch();
                })->catch(function (Throwable $exception) use ($message, $data) {
                    Log::error('Broker error', [
                        'video_id'  => $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID],
                        'url'       => $data[Contract::FILED_VIDEO_UPLOAD_TOPIC_URL],
                        'key'       => $message->getKey(),
                        'timestamp' => $message->getTimestamp(),
                        'topic'     => $message->getTopicName(),
                        'error'     => $exception->getMessage(),
                        'trace'     => $exception->getTraceAsString(),
                    ]);
                    dispatch(new VideoUploadGarbageCollectorJob($data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID]));
                    event(new VideoUploadedEvent($data[Contract::FILED_VIDEO_UPLOAD_TOPIC_ID], false));
                })->dispatch();
            })
            ->build();

        $consumer->consume();
    }
}