<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost;

use Ragnarok\Bifrost\Const\DiscordEndpoints;
use Ragnarok\Bifrost\Exceptions\MissingParamException;
use Spatie\Regex\MatchResult;
use Spatie\Regex\Regex;

class Endpoint extends DiscordEndpoints implements EndpointInterface
{
    public const BASE_URL = 'https://discord.com/api/v10';

    public const RATE_LIMIT_PARAMS = [':channel_id:', ':guild_id:', ':webhook_id:', ':thread_id:'];

    /**
     * @throws MissingParamException
     */
    public static function bind(string $uri, string ...$params): static
    {
        $requiredParams = self::getParams($uri);

        if (count($requiredParams) !== count($params)) {
            throw new MissingParamException($requiredParams);
        }

        $combined = array_combine($requiredParams, $params);

        return new static($uri, $combined);
    }

    private static function getParams(string $uri): array
    {
        return array_values(array_unique(array_map(
            fn (MatchResult $result) => $result->result(),
            Regex::matchAll('(:\w+:)', $uri)->results()
        )));
    }

    public function __construct(
        private string $uri,
        private array $params
    ) {
    }

    public function getCompleteEndpoint(): string
    {
        return self::BASE_URL . str_replace(
            array_keys($this->params),
            array_values($this->params),
            $this->uri
        );
    }

    public function getBucketId(): string
    {
        $requiredParams = [];

        foreach ($this->params as $key => $value) {
            if (in_array($key, self::RATELIMIT_PARAMS)) {
                $requiredParams[$key] = $value;
            }
        }

        return str_replace(
            array_keys($requiredParams),
            array_values($requiredParams),
            $this->uri
        );
    }
}
