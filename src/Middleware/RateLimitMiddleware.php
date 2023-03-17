<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Middleware;

use Psr\Http\Message\RequestInterface;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(RequestInterface $request, callable $next)
    {
        $next($request);
    }
}
