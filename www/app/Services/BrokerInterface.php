<?php

namespace App\Services;

interface BrokerInterface
{
    public function withHeaders(array $message): self;
    public function addMessage(array $message): self;
    public function topic(string $topicName): self;
    public function send(): bool;
}