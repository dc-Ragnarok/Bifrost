<?php

declare(strict_types=1);

namespace Ragnarok\Bifrost\Exceptions;

use Exception;

class MissingParamException extends Exception
{
    /**
     * @param string[] $requiredParams
     */
    public function __construct(public readonly array $requiredParams)
    {
        parent::__construct(
            sprintf('Incorrect params, required: %s', implode(', ', $requiredParams))
        );
    }
}
