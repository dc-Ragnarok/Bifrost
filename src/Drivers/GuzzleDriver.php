<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Drivers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Psr\Http\Message\ResponseInterface;
use Ragnarok\Bifrost\DriverInterface;
use Ragnarok\Bifrost\Request;
use React\Promise\ExtendedPromiseInterface;
use React\Promise\Promise;

class GuzzleDriver implements DriverInterface
{
    public function __construct(private Client $client = new Client())
    {
    }

    public function makeRequest(Request $request): ExtendedPromiseInterface
    {
        $psr7Request = new Psr7Request(
            $request->getMethod()->value,
            $request->getEndpoint()->getCompleteEndpoint(),
            $request->getHeaders(),
            $request->getContent(),
        );

        return new Promise(function ($resolve, $reject) use ($psr7Request) {
            $promise = $this->client->sendAsync($psr7Request)->then(function (ResponseInterface $response) use ($resolve) {
                $resolve($response);
            }, function (RequestException $e) use ($reject) {
                $reject($e);
            });

            $promise->wait();
        });
    }
}
