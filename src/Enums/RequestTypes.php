<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Enums;

enum RequestTypes: string
{
    case GET = 'get';
    case POST = 'post';
    case PUT = 'put';
    case PATCH = 'patch';
    case DELETE = 'delete';
}
