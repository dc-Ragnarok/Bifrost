<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Ragnarok\Bifrost\Enums\RequestTypes;

class Request
{
    public function __construct(
        private RequestTypes $method,
        private EndpointInterface $endpoint,
        private string $body = '',
        private array $headers = []
    ) {
    }

    public function setMethod(RequestTypes $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod(): RequestTypes
    {
        return $this->method;
    }

    public function setEndpoint(EndpointInterface $endpoint): static
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    public function getEndpoint(): EndpointInterface
    {
        return $this->endpoint;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $header, string $value)
    {
        if (!isset($this->headers[$header])) {
            $this->headers[$header] = [];
        }

        $this->headers[$header][] = $value;

        return $this;
    }

    public function getHeader(string $header): array
    {
        return isset($this->headers[$header])
            ? $this->headers[$header]
            : [];
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
