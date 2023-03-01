<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\TestCase;

use Mockery;
use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\DriverInterface;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Request;

use function React\Async\await;

abstract class DriverInterfaceTestCase extends TestCase
{
    abstract protected function getDriver(): DriverInterface;

    private function getRequest(
        string $method,
        string $url,
        string $content = '',
        array $headers = []
    ): Request {
        $request = Mockery::mock(Request::class);

        $endpoint = Mockery::mock(Endpoint::class);
        $endpoint->shouldReceive('getCompleteEndpoint')->andReturn($url);

        $request->shouldReceive([
            'getMethod' => RequestTypes::from($method),
            'getEndpoint' => $endpoint,
            'getBody' => $content,
            'getHeaders' => $headers,
        ]);

        return $request;
    }

    /**
     * @dataProvider requestProvider
     */
    public function testRequest(string $method, string $url, array $content = [], array $verify = [])
    {
        $driver = $this->getDriver();
        $request = $this->getRequest(
            $method,
            $url,
            $content === [] ? '' : json_encode($content),
            empty($content) ? [] : ['Content-Type' => 'application/json']
        );

        /** @var ResponseInterface */
        $response = await($driver->makeRequest($request));

        $this->assertNotEquals('', $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());

        $jsonDecodedBody = json_decode($response->getBody(), true);

        $verify['method'] = strtoupper($method);

        foreach ($verify as $field => $expectedValue) {
            $this->assertEquals(
                $expectedValue,
                $jsonDecodedBody[$field]
            );
        }
    }

    public static function requestProvider(): array
    {
        $content = ['something' => 'value'];
        return [
            'Plain get' => [
                'method' => 'get',
                'url' => 'http://127.0.0.1:8888',
            ],
            'Get with params' => [
                'method' => 'get',
                'url' => 'http://127.0.0.1:8888?something=value',
                'verify' => [
                    'args' => ['something' => 'value'],
                ],
            ],

            'Plain post' => [
                'method' => 'post',
                'url' => 'http://127.0.0.1:8888',
            ],
            'Post with content' => [
                'method' => 'post',
                'url' => 'http://127.0.0.1:8888',
                'content' => $content,
                'verify' => [
                    'json' => $content,
                ],
            ],

            'Plain put' => [
                'method' => 'put',
                'url' => 'http://127.0.0.1:8888',
            ],
            'Put with content' => [
                'method' => 'put',
                'url' => 'http://127.0.0.1:8888',
                'content' => $content,
                'verify' => [
                    'json' => $content,
                ],
            ],

            'Plain patch' => [
                'method' => 'patch',
                'url' => 'http://127.0.0.1:8888',
            ],
            'Patch with content' => [
                'method' => 'patch',
                'url' => 'http://127.0.0.1:8888',
                'content' => $content,
                'verify' => [
                    'json' => $content,
                ],
            ],

            'Plain delete' => [
                'method' => 'delete',
                'url' => 'http://127.0.0.1:8888',
            ],
            'Delete with content' => [
                'method' => 'delete',
                'url' => 'http://127.0.0.1:8888',
                'content' => $content,
                'verify' => [
                    'json' => $content,
                ],
            ],
        ];
    }
}
