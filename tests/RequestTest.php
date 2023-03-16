<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use Mockery;
use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\EndpointInterface;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Psr\Http\Message\RequestInterface;

class RequestTest extends TestCase
{
    private function getRequest(): RequestInterface
    {
        // TODO change to use RequestInterface implementation
        return new Request(
            RequestTypes::GET,
            Mockery::mock(EndpointInterface::class)
        );
    }

    public function testMethod()
    {
        $request = $this->getRequest();

        $this->assertEquals(RequestTypes::GET, $request->getMethod());

        $request->setMethod(RequestTypes::POST);

        $this->assertEquals(RequestTypes::POST, $request->getMethod());
    }

    public function testEndpoint()
    {
        $endpointMock = Mockery::mock(EndpointInterface::class);
        $request = $this->getRequest()->setEndpoint($endpointMock);

        $this->assertEquals(
            $endpointMock,
            $request->getEndpoint()
        );
    }

    public function testBody()
    {
        $request = $this->getRequest();

        $this->assertEquals(
            '',
            $request->getBody()
        );

        $request->setBody('::other body::');

        $this->assertEquals(
            '::other body::',
            $request->getBody()
        );
    }

    public function testHeaders()
    {
        $request = $this->getRequest();

        $this->assertEquals(
            [],
            $request->getHeaders()
        );

        $request->addHeader('::header::', '::value::');
        $this->assertEquals(
            ['::header::' => ['::value::']],
            $request->getHeaders()
        );

        $request->addHeader('::header::', '::second value::');
        $this->assertEquals(
            ['::header::' => ['::value::', '::second value::']],
            $request->getHeaders()
        );

        $request->setHeaders(['::other header::' => ['::other value::']]);
        $this->assertEquals(
            ['::other header::' => ['::other value::']],
            $request->getHeaders()
        );

        $this->assertEquals(
            [],
            $request->getHeader('::not set header::')
        );

        $request->addHeader('::yet anohter header::', '::its value::');
        $this->assertEquals(
            ['::its value::'],
            $request->getHeader('::yet anohter header::')
        );
    }
}
