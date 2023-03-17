<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost\Driver;

use HttpSoft\Message\Request;
use HttpSoft\Message\Stream;
use HttpSoft\Message\StreamFactory;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\DriverInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

use function React\Async\await;

abstract class DriverInterfaceTestCase extends TestCase
{
    protected StreamFactory $streamFactory;
    abstract protected function getDriver(): DriverInterface;

    protected function setUp(): void
    {
        parent::setUp();

        $this->streamFactory = new StreamFactory();
    }

    private function getRequest(
        string $method,
        string $url,
        string $content = '',
        array $headers = []
    ): RequestInterface {
        return new Request(
            $method,
            $url,
            $headers,
            $this->streamFactory->createStream($content)
        );
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

        $jsonDecodedBody = json_decode((string) $response->getBody(), true);

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
