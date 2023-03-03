<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Middleware;

use Ragnarok\Bifrost\Request;

class RateLimitMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next)
    {
        $next($request);
    }
}
