<?php

namespace App\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_FUNCTION)]
final class LongPolling
{
    public function __construct(
        public int $maxwait = 3600,
    ) {
    }
}