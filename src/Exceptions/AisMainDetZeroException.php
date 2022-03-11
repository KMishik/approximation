<?php

namespace Interpolate\Exceptions;

class AisMainDetZeroException extends AisMathException
{
    public function __construct($message = "\nMain Determinant is equal 0.\nUse Gauss method instead", $code = -1)
    {
        parent::__construct($message, $code);
    }
}