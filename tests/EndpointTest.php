<?php

declare(strict_types=1);

namespace Tests\Ragnarok\Bifrost;

use PHPUnit\Framework\TestCase;
use Ragnarok\Bifrost\Endpoint;
use Ragnarok\Bifrost\Exceptions\MissingParamException;

class EndpointTest extends TestCase
{
    public function testItReplacesParams()
    {
        $endpoint = new Endpoint('some/:endpoint:/to/:replace:', [
            ':endpoint:' => 'something funny',
            ':replace:' => 'something unfunny',
        ]);

        $this->assertEquals(
            Endpoint::BASE_URL . 'some/something funny/to/something unfunny',
            $endpoint->getCompleteEndpoint()
        );
    }

    public function testItGetsABucketId()
    {
        $endpoint = new Endpoint('channels/:channel_id:/messages/:message_id:', [
            ':channel_id:' => 'a_channel_woo',
            ':message_id:' => 'this_is_a_message',
        ]);

        $this->assertEquals(
            'channels/a_channel_woo/messages/:message_id:',
            $endpoint->getBucketId(),
        );
    }

    /**
     * @dataProvider bindProvider
     */
    public function testBind(string $uri, array $replacements, string $expected)
    {
        $this->assertEquals(
            Endpoint::BASE_URL . $expected,
            Endpoint::bind($uri, ...$replacements)->getCompleteEndpoint()
        );
    }

    public static function bindProvider(): array
    {
        return [
            'Several major params' => [
                'uri' => 'something/:guild_id:/:channel_id:/:webhook_id:',
                'replacements' => ['::guild id::', '::channel id::', '::webhook id::'],
                'expected' => 'something/::guild id::/::channel id::/::webhook id::',
            ],
            'Single major param' => [
                'uri' => 'something/:guild_id:',
                'replacements' => ['::guild id::'],
                'expected' => 'something/::guild id::',
            ],
            'Single major param, some minor params' => [
                'uri' => 'something/:guild_id:/:some_param:/:something_else:',
                'replacements' => ['::guild id::', '::some param::', '::something else::'],
                'expected' => 'something/::guild id::/::some param::/::something else::',
            ],
            'Only minor params' => [
                'uri' => 'something/:something:/:some_param:/:other:',
                'replacements' => ['::something::', '::some param::', '::something else::'],
                'expected' => 'something/::something::/::some param::/::something else::',
            ],
            'Minor and major params in weird order' => [
                'uri' => 'something/:something:/:guild_id:/:other:/:channel_id:',
                'replacements' => ['::something::', '::guild id::', '::something else::', '::channel id::'],
                'expected' => 'something/::something::/::guild id::/::something else::/::channel id::',
            ],
            'Params with same prefix, short first' => [
                'uri' => 'something/:thing:/:thing_other:',
                'replacements' => ['::thing::', '::thing other::'],
                'expected' => 'something/::thing::/::thing other::',
            ],
            'Params with same prefix, short first' => [
                'uri' => 'something/:thing_other:/:thing:',
                'replacements' => ['::thing other::', '::thing::'],
                'expected' => 'something/::thing other::/::thing::',
            ],
        ];
    }

    public function testItThrowsAnErrorIfWrongAmountOfParamsAreProvided()
    {
        $this->expectException(MissingParamException::class);

        Endpoint::bind(
            ':one:/:two:/:three:',
            'one', 'two'
        );
    }
}
