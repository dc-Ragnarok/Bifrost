<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

interface PostwareInterface
{
    public function handle(Request $request, callable $next);
}
