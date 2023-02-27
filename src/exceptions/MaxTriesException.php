<?php

namespace statikbe\udb\exceptions;

use Throwable;

class MaxTriesException extends \Exception
{
    public function __construct(string $message = "The maximum of tries of this request has been exeeded.", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct(
            $message,
            $code,
            $previous
        );
    }
}