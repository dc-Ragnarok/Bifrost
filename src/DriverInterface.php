<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use React\Promise\ExtendedPromiseInterface;

interface DriverInterface
{
    /**
     * @return ExtendedPromiseInterface<\Ragnarok\Bifrost\Response>
     */
    public function makeRequest(Request $request): ExtendedPromiseInterface;
}
