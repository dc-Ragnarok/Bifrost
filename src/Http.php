<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Multipart\Body;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;

class Http
{
    /**
     * @param MiddlewareInterace[] $middlewares
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

    public function get(
        EndpointInterface $endpoint,
        null|string|array|Body $body = null,
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
        null|string|array|Body $body = null,
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
        null|string|array|Body $body = null,
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
        null|string|array|Body $body = null,
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
        null|string|array|Body $body = null,
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
        null|string|array|Body $content = null,
        array $headers = []
    ): ExtendedPromiseInterface {
        $requestContent = RequestContent::from($content);

        $request = new Request(
            $requestType,
            $endpoint,
            $requestContent->content,
            array_merge(
                $requestContent->headers,
                $headers
            )
        );

        return $this
            ->runMiddlewares($request, $this->middlewares)
            ->then(function () use ($request) {
                return $this->driver->makeRequest(
                    $request
                );
            });
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     */
    private function runMiddlewares(Request $request, array $middlewares): ExtendedPromiseInterface
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
