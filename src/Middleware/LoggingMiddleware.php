<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Middleware;

use Psr\Log\LoggerInterface;
use Ragnarok\Bifrost\Request;

class LoggingMiddleware implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function handle(Request $request, callable $next)
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
