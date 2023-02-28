<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

interface EndpointInterface
{
    public function getCompleteEndpoint(): string;
    public function getBucketId(): string;
}
