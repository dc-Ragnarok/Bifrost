<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use Mockery;
use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\Multipart;
use Ragnarok\Bifrost\RequestContent;

class RequestContentTest extends TestCase
{
    public function testFromNull()
    {
        $requestContent = RequestContent::from(null);

        $this->assertEquals('', $requestContent->body);
        $this->assertEquals([], $requestContent->headers);
    }

    public function testFromString()
    {
        $requestContent = RequestContent::from('::body::');

        $this->assertEquals('::body::', $requestContent->body);
        $this->assertEquals([], $requestContent->headers);
    }

    public function testFromArray()
    {
        $requestContent = RequestContent::from(['::key::' => '::value::']);

        $this->assertEquals(json_encode(['::key::' => '::value::']), $requestContent->body);
        $this->assertEquals(['Content-Type' => ['application/json']], $requestContent->headers);
    }

    public function testFromMultipart()
    {
        $body = Mockery::mock(Multipart\Body::class);

        $body->shouldReceive([
            'getHeaders' => ['header' => ['value']],
            'getBody' => '::body::',
        ]);

        $requestContent = RequestContent::from($body);

        $this->assertEquals('::body::', $requestContent->body);
        $this->assertEquals(['header' => ['value']], $requestContent->headers);
    }
}
