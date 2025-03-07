<?php

namespace App\Events;

use App\Contract;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoUploadedEvent implements VideoUploadInterface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(private int $id, private bool $isSuccess)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel(Contract::CHANNEL_VIDEO),
        ];
    }
}
