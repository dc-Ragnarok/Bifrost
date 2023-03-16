<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Middleware;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestInterface;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(RequestInterface $request, callable $next)
    {
        $this->logger->log(
            'info',
            sprintf(
                'Handling %s request to %s',
                $request->getMethod(),
                (string) $request->getUri()
            )
        );

        $next($request);
    }
}
