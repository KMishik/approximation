<?php

namespace Idiacant\Approximation\Exceptions;

class ApproximationException extends \Exception
{
    public function __construct($message = "Undefined approximation exception", $code = -1)
    {
        parent::__construct($message, $code);
    }
}