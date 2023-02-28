<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Ragnarok\Bifrost\Multipart\Body;

class RequestContent
{
    public function __construct(
        public readonly array $headers,
        public readonly string $content
    ) {
    }

    public static function from(null|string|array|Body $content)
    {
        if (is_null($content)) {
            return new static([], '');
        }

        if (is_string($content)) {
            return new static([], $content);
        }

        if (is_array($content)) {
            return new static(
                ['Content-Type' => 'application/json'],
                json_encode($content)
            );
        }

        if ($content instanceof Body) {
            return new static($content->getHeaders(), $content->getContent());
        }
    }
}
