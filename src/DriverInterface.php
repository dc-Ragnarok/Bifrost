<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Psr\Http\Message\RequestInterface;
use React\Promise\ExtendedPromiseInterface;

interface DriverInterface
{
    /**
     * @return ExtendedPromiseInterface<\Psr\Http\Message\ResponseInterface>
     */
    public function makeRequest(RequestInterface $request): ExtendedPromiseInterface;
}
