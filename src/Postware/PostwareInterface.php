<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Postware;

use Ragnarok\Bifrost\Response;

interface PostwareInterface
{
    public function handle(Response $response, callable $next);
}
