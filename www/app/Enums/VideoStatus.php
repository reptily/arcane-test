<?php

namespace App\Enums;

enum VideoStatus
{
    case uploading;
    case uploaded;
    case error;
}