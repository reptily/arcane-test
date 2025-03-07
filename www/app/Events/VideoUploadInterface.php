<?php

namespace App\Events;

interface VideoUploadInterface
{
    public function getId(): int;
    public function isSuccess(): bool;
}