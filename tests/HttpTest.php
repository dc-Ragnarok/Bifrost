<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Ragnarok\Bifrost\Endpoint;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Http;
use Ragnarok\Bifrost\MiddlewareInterface;
use Ragnarok\Bifrost\Request;
use React\EventLoop\Loop;

use function React\Async\await;

class HttpTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testItRunsMiddlewares()
    {
        $mw1 = Mockery::mock(MiddlewareInterface::class);
        $mw2 = Mockery::mock(MiddlewareInterface::class);

        $http = new Http(
            '::token::',
            Loop::get(),
            new NullLogger(),
            [$mw1, $mw2]
        );

        $mockRequest = Mockery::mock(Request::class);

        $mw1->shouldReceive('handle')->andReturnUsing(function ($request, $next) use ($mockRequest) {
            $next($mockRequest);
        });

        $mw2->shouldReceive('handle')->with(
            $mockRequest,
            Mockery::on(fn ($v) => true)
        )->andReturnUsing(function ($request, $next) {
            $next($request);
        });

        await($http->request(
            RequestTypes::GET,
            Mockery::mock(Endpoint::class),
        ));

        $mw1->shouldHaveReceived('handle');

        $mw2->shouldHaveReceived('handle');
    }
}
