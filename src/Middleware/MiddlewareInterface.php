<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

interface MiddlewareInterface
{
    public function handle(Request $request, callable $next);
}
