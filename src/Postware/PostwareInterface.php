<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Postware;

use Psr\Http\Message\ResponseInterface;

interface PostwareInterface
{
    public function handle(ResponseInterface $response, callable $next);
}
