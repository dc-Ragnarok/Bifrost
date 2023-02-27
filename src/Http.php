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
        private array $middlewares = [],
        private array $postwares = []
    ) {
    }

    public function withRateLimiting(): self
    {
        $bucket = new Bucket();

        $this->middlewares[] = new RateLimitMiddleware($bucket);
        $this->postwares[] = new RateLimitPostWare($bucket);

        return $this;
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
        null|string|array|Body $content = null,
        array $headers = []
    ): ExtendedPromiseInterface {
        return new Promise(function ($resolve) {
            $request = new Request();

            $this->runMiddlewares($request, $this->middlewares)->then(function () use ($resolve) {
                $resolve(1);
            });
        });
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     */
    private function runMiddlewares(Request $request, array $middlewares)
    {
        return new Promise(function ($resolve) use ($request, $middlewares) {
            if (count($middlewares) === 0) {
                $resolve($request);
            }

            $toRun = array_shift($middlewares);

            $toRun->handle($request, function ($request) use ($middlewares, $resolve) {
                $this->runMiddlewares($request, $middlewares)->then(
                    fn ($request) => $resolve($request)
                );
            });
        });
    }
}
