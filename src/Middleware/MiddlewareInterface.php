<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Middleware;

use Psr\Http\Message\RequestInterface;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, callable $next);
}
