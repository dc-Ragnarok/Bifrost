<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost\Middleware;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Log\LoggerInterface;
use Ragnarok\Bifrost\Endpoint;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Middleware\LoggingMiddleware;
use Psr\Http\Message\RequestInterface;

class RateLimitMiddlewareTest extends MockeryTestCase
{
    public function testItLogsCertainData()
    {
        $logger = Mockery::mock(LoggerInterface::class);

        $logger->shouldReceive('log')->with('info', 'Handling get request to ::url::')->once();

        $middleware = new LoggingMiddleware($logger);

        $endpoint = Mockery::mock(Endpoint::class);
        $endpoint->shouldReceive('getCompleteEndpoint')->andReturn('::url::');

        // TODO change to use RequestInterface implementation
        $request = new Request(
            RequestTypes::GET,
            $endpoint
        );

        $hasCalled = false;
        $callsNextCheck = function ($postMiddlewareRequest) use (&$hasCalled, $request) {
            $this->assertEquals($request, $postMiddlewareRequest);
            $hasCalled = true;
        };

        $middleware->handle($request, $callsNextCheck);

        $this->assertTrue($hasCalled, 'Middleware did not call next function');
    }
}
