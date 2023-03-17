<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use HttpSoft\Message\RequestTrait;
use HttpSoft\Message\StreamFactory;
use Psr\Http\Message\RequestInterface;
use Ragnarok\Bifrost\Enums\RequestTypes;

class Request implements RequestInterface
{
    use RequestTrait;

    private static StreamFactory $streamFactory;

    public function __construct(
        RequestTypes $requestType,
        EndpointInterface $endpoint,
        ?string $body = null,
        array $headers = []
    ) {
        if (!isset(self::$streamFactory)) {
            self::$streamFactory = new StreamFactory();
        }

        $this->init(
            $requestType->value,
            $endpoint->getCompleteEndpoint(),
            $headers,
            is_null($body) ? null : self::$streamFactory->createStream($body)
        );
    }
}
