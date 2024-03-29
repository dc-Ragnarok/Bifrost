<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Ragnarok\Bifrost\DriverInterface;
use Ragnarok\Bifrost\Endpoint;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Http;
use Ragnarok\Bifrost\Middleware\MiddlewareInterface;
use Ragnarok\Bifrost\Postware\PostwareInterface;
use Ragnarok\Bifrost\Request;
use Ragnarok\Bifrost\Response;
use React\Promise\Promise;

use function React\Async\await;

class HttpTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    private function getMockDriver()
    {
        $driver = Mockery::mock(DriverInterface::class);
        $driver
            ->shouldReceive('makeRequest')
            ->andReturnUsing(function () {
                return new Promise(
                    fn ($resolve) => $resolve(Mockery::mock(ResponseInterface::class))
                );
            });

        return $driver;
    }

    public function testItMakesARequest()
    {
        $driver = $this->getMockDriver();
        $http = new Http(
            $driver
        );

        await($http->request(
            RequestTypes::GET,
            Endpoint::bind('nothing')
        ));

        $driver->shouldHaveReceived('makeRequest');
    }

    public function testItRunsMiddlewares()
    {
        $mw1 = Mockery::mock(MiddlewareInterface::class);
        $mw2 = Mockery::mock(MiddlewareInterface::class);

        $http = new Http(
            $this->getMockDriver(),
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
            Endpoint::bind('nothing')
        ));

        $mw1->shouldHaveReceived('handle');

        $mw2->shouldHaveReceived('handle');
    }

    public function testItCanAddMiddleware()
    {
        $mw1 = Mockery::mock(MiddlewareInterface::class);
        $mw2 = Mockery::mock(MiddlewareInterface::class);

        $http = new Http(
            $this->getMockDriver(),
            [$mw1]
        );

        $http->withMiddleware($mw2);

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
            Endpoint::bind('nothing')
        ));

        $mw1->shouldHaveReceived('handle');

        $mw2->shouldHaveReceived('handle');
    }

    public function testItRunsPostwares()
    {
        $pw1 = Mockery::mock(PostwareInterface::class);
        $pw2 = Mockery::mock(PostwareInterface::class);

        $http = new Http(
            $this->getMockDriver(),
            postwares: [$pw1, $pw2]
        );

        $mockResponse = Mockery::mock(ResponseInterface::class);

        $pw1->shouldReceive('handle')->andReturnUsing(function ($response, $next) use ($mockResponse) {
            $next($mockResponse);
        });

        $pw2->shouldReceive('handle')->with(
            $mockResponse,
            Mockery::on(fn ($v) => true)
        )->andReturnUsing(function ($response, $next) {
            $next($response);
        });

        await($http->request(
            RequestTypes::GET,
            Endpoint::bind('nothing')
        ));

        $pw1->shouldHaveReceived('handle');

        $pw2->shouldHaveReceived('handle');
    }

    public function testItCanAddPostware()
    {
        $pw1 = Mockery::mock(PostwareInterface::class);
        $pw2 = Mockery::mock(PostwareInterface::class);

        $http = new Http(
            $this->getMockDriver(),
            postwares: [$pw1]
        );

        $http->withPostware($pw2);

        $mockResponse = Mockery::mock(ResponseInterface::class);

        $pw1->shouldReceive('handle')->andReturnUsing(function ($response, $next) use ($mockResponse) {
            $next($mockResponse);
        });

        $pw2->shouldReceive('handle')->with(
            $mockResponse,
            Mockery::on(fn ($v) => true)
        )->andReturnUsing(function ($response, $next) {
            $next($response);
        });

        await($http->request(
            RequestTypes::GET,
            Endpoint::bind('nothing')
        ));

        $pw1->shouldHaveReceived('handle');

        $pw2->shouldHaveReceived('handle');
    }

    /**
     * @dataProvider requestsOfTypeProvider
     */
    public function testItMakesRequestOfType(string $method, RequestTypes $type)
    {
        $driver = $this->getMockDriver();
        $http = new Http(
            $driver
        );

        call_user_func(
            [$http, $method],
            Endpoint::bind('nothing')
        );

        $driver->shouldHaveReceived('makeRequest')->with(Mockery::on(
            function (RequestInterface $request) use ($type) {
                return $request->getMethod() === $type->value;
            }
        ));
    }

    public static function requestsOfTypeProvider(): array
    {
        return [
            'GET' => [
                'method' => 'get',
                'type' => RequestTypes::GET
            ],
            'POST' => [
                'method' => 'post',
                'type' => RequestTypes::POST
            ],
            'PUT' => [
                'method' => 'put',
                'type' => RequestTypes::PUT
            ],
            'PATCH' => [
                'method' => 'patch',
                'type' => RequestTypes::PATCH
            ],
            'DELETE' => [
                'method' => 'delete',
                'type' => RequestTypes::DELETE
            ],
        ];
    }
}
