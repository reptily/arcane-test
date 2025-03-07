<?php

namespace App\Services;

use App\Contract;
use Illuminate\Support\Facades\Log;
use Junges\Kafka\Facades\Kafka;
use Throwable;

class KafkaBrokerService implements BrokerInterface
{
    private array $headers = [];
    private string $topic;
    private array $message = [];

    public function withHeaders(array $message): BrokerInterface
    {
        $this->headers = $message;

        return $this;
    }

    public function topic(string $topicName): BrokerInterface
    {
        $this->topic = $topicName;

        return $this;
    }

    public function send(): bool
    {
        if ($this->topic === null) {
            return false;
        }

        try {
            return Kafka::publish()
                ->onTopic($this->topic)
                ->withHeaders($this->headers)
                ->withBodyKey(Contract::FILED_BROKER_MESSAGE, $this->message)
                ->send();
        } catch (Throwable $exception) {
            Log::error('Broker error', [
                'topic'     => $this->topic,
                'headers'   => $this->headers,
                'message'   => $this->message,
                'error'     => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return false;
        }
    }

    public function addMessage(array $message): BrokerInterface
    {
        $this->message = $message;

        return $this;
    }
}