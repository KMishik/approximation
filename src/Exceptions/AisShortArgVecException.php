<?php

namespace Interpolate\Exceptions;

class AisShortArgVecException extends AisMathException
{
    public function __construct($message = "\nArgument Vector must have 2 or more values.\nHave received fewer", $code = -1)
    {
        parent::__construct($message, $code);
    }
}