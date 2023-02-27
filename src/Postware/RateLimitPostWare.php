<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

class RateLimitPostWare implements PostwareInterface
{
    public function handle(Request $request, callable $next)
    {
        $next($request);
    }
}
