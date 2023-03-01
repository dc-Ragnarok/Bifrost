<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Ragnarok\Bifrost\Multipart;

class RequestContent
{
    public function __construct(
        public readonly array $headers,
        public readonly string $body
    ) {
    }

    public static function from(null|string|array|Multipart\Body $body)
    {
        if (is_null($body)) {
            return new static([], '');
        }

        if (is_string($body)) {
            return new static([], $body);
        }

        if (is_array($body)) {
            return new static(
                ['Content-Type' => ['application/json']],
                json_encode($body)
            );
        }

        if ($body instanceof Multipart\Body) {
            return new static($body->getHeaders(), $body->getBody());
        }
    }
}
