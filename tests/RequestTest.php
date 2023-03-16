<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\Endpoint;
use Ragnarok\Bifrost\Enums\RequestTypes;
use Ragnarok\Bifrost\Request;

class RequestTest extends TestCase
{
    public function testConstruction()
    {
        $endpoint = Endpoint::bind(
            Endpoint::CHANNEL,
            '::channel_id::'
        );

        $request = new Request(
            RequestTypes::GET,
            $endpoint,
            '::body::',
            ['header_name' => '::value::']
        );


        $this->assertEquals(
            RequestTypes::GET->value,
            $request->getMethod()
        );

        $this->assertEquals(
            $endpoint->getCompleteEndpoint(),
            (string) $request->getUri(),
        );

        $this->assertEquals(
            '::body::',
            (string) $request->getBody()
        );

        $this->assertTrue($request->hasHeader('header_name'));

        $this->assertEquals(['::value::'], $request->getHeader('header_name'));
    }
}
