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

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function setBody(mixed $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
