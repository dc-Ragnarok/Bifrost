<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class ResponseTest extends TestCase
{
    public function testGetStatusCode()
    {
        // TODO use ResponseInterface implementation
        $response = new Response(200, null, []);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetBody()
    {
        // TODO use ResponseInterface implementation
        $response = new Response(200, '::body::', []);

        $this->assertEquals('::body::', $response->getBody());

        $response->setBody(['key' => 'value']);

        $this->assertEquals(['key' => 'value'], $response->getBody());
    }

    public function testGetHeaders()
    {
        // TODO use ResponseInterface implementation
        $response = new Response(200, null, ['key' => ['value']]);

        $this->assertEquals(['key' => ['value']], $response->getHeaders());
    }
}
