<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

interface EndpointInterface
{
    /**
     * @return string Should not incluse leading /
     */
    public function getCompleteEndpoint(): string;
    public function getBucketId(): string;
}
