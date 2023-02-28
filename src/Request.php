<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Ragnarok\Bifrost\Enums\RequestTypes;

class Request
{
    public function __construct(
        private RequestTypes $method,
        private EndpointInterface $endpoint,
        private string $content,
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

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $header, string $value)
    {
        $this->headers[] = [$header => $value];

        return $this;
    }

    public function getHeaders(?string $headerName = null): array
    {
        return is_null($headerName)
            ? $this->headers
            : array_values(array_filter(
                $this->headers,
                fn ($header) => array_keys($header) === [$headerName]
            ));
    }
}
