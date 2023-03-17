<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use HttpSoft\Message\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Middleware\MiddlewareInterface;
use Ragnarok\Bifrost\Middleware\RateLimitMiddleware;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;
use Ragnarok\Bifrost\Postware\PostwareInterface;
use Ragnarok\Bifrost\Postware\RateLimitPostWare;

class Http
{
    /**
     * @param MiddlewareInterface[] $middlewares
     * @param PostwareInterface[] $postwares
     */
    public function __construct(
        private DriverInterface $driver,
        private array $middlewares = [],
        private array $postwares = [],
    ) {
    }

    public function withRateLimiting(): self
    {
        $bucket = new Bucket();

        $this->middlewares[] = new RateLimitMiddleware($bucket);
        $this->postwares[] = new RateLimitPostWare($bucket);

        return $this;
    }

    public function withMiddleware(MiddlewareInterface $middlewareInterface)
    {
        $this->middlewares[] = $middlewareInterface;
    }

    public function withPostware(PostwareInterface $postwareInterface)
    {
        $this->postwares[] = $postwareInterface;
    }

    public function get(
        EndpointInterface $endpoint,
        null|string|array $body = null,
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
        EndpointInterface $endpoint,
        null|string|array $body = null,
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
        EndpointInterface $endpoint,
        null|string|array $body = null,
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
        EndpointInterface $endpoint,
        null|string|array $body = null,
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
        EndpointInterface $endpoint,
        null|string|array $body = null,
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
        EndpointInterface $endpoint,
        null|string|array $body = null,
        array $headers = []
    ): ExtendedPromiseInterface {
        $request = new Request(
            $requestType->value,
            $endpoint->getCompleteEndpoint(),
            $headers,
            $body
        );

        return $this
            ->runMiddlewares($request, $this->middlewares)
            ->then(function () use ($request) {
                return $this->driver->makeRequest(
                    $request
                );
            })
            ->then(fn($response) => $this->runPostwares($response, $this->postwares));
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     */
    private function runMiddlewares(RequestInterface $request, array $middlewares): ExtendedPromiseInterface
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

    /**
     * @param PostwareInterface[] $postwares
     */
    private function runPostwares(ResponseInterface $response, array $postwares): ExtendedPromiseInterface
    {
        return new Promise(function ($resolve) use ($response, $postwares) {
            if (count($postwares) === 0) {
                $resolve($response);
            }

            $toRun = array_shift($postwares);

            $toRun->handle($response, function ($response) use ($postwares, $resolve) {
                $this->runPostwares($response, $postwares)->then(
                    fn ($response) => $resolve($response)
                );
            });
        });
    }
}
