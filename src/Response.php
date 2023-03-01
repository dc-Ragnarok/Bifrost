<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

class Response
{
    public function __construct(
        private int $status,
        private mixed $body,
        private array $headers
    ) {
    }
}
