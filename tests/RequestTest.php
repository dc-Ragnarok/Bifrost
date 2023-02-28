<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use Mockery;
use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\EndpointInterface;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Request;

class RequestTest extends TestCase
{
    private function getRequest(): Request
    {
        return new Request(
            RequestTypes::GET,
            Mockery::mock(EndpointInterface::class),
            '::content::'
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

    public function testContent()
    {
        $request = $this->getRequest();

        $this->assertEquals(
            '::content::',
            $request->getContent()
        );

        $request->setContent('::other content::');

        $this->assertEquals(
            '::other content::',
            $request->getContent()
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
            [['::header::' => '::value::']],
            $request->getHeaders()
        );

        $request->setHeaders([['::other header::' => '::other value::']]);
        $this->assertEquals(
            [['::other header::' => '::other value::']],
            $request->getHeaders()
        );

        $request->addHeader('::different header::', '::yet another value::');
        $request->addHeader('::other header::', '::yet another value::');

        $this->assertEquals(
            [
                ['::other header::' => '::other value::'],
                ['::other header::' => '::yet another value::'],
            ],
            $request->getHeaders('::other header::')
        );
    }
}
