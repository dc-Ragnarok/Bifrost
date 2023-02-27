<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Psr\Log\LoggerInterface;
use Ragnarok\Bifrost\Endpoint;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Multipart\Body;
use React\EventLoop\LoopInterface;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;

class Http
{
    public function __construct(
        private string $token,
        private LoopInterface $loop,
        private LoggerInterface $logger,
    ) {
    }

    public function get(
        Endpoint $endpoint,
        string|array|Body $body,
        array $headers = []
    ): ExtendedPromiseInterface {
        return $this->request(
            RequestTypes::GET,
            $endpoint,
            $body,
            $headers
        );
    }

    public function post(
        Endpoint $endpoint,
        string|array|Body $body,
        array $headers = []
    ): ExtendedPromiseInterface {
        return $this->request(
            RequestTypes::POST,
            $endpoint,
            $body,
            $headers
        );
    }

    public function put(
        Endpoint $endpoint,
        string|array|Body $body,
        array $headers = []
    ): ExtendedPromiseInterface {
        return $this->request(
            RequestTypes::PUT,
            $endpoint,
            $body,
            $headers
        );
    }

    public function patch(
        Endpoint $endpoint,
        string|array|Body $body,
        array $headers = []
    ): ExtendedPromiseInterface {
        return $this->request(
            RequestTypes::PATCH,
            $endpoint,
            $body,
            $headers
        );
    }

    public function delete(
        Endpoint $endpoint,
        string|array|Body $body,
        array $headers = []
    ): ExtendedPromiseInterface {
        return $this->request(
            RequestTypes::DELETE,
            $endpoint,
            $body,
            $headers
        );
    }

    public function request(
        RequestTypes $requestType,
        Endpoint $endpoint,
        string|array|Body $content,
        array $headers = []
    ): ExtendedPromiseInterface {
        return new Promise(fn () => 1);
    }
}
