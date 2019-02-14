<?php

namespace SkachCz\Imokutr\Exception;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrWrongMacroParameterException extends \RuntimeException
{

    public function __construct(string $parameter = null, string $limitText = null)
    {

        $code = 4;

        $message = sprintf('Macro parameter %s must be %s.', $parameter, $limitText);

        return parent::__construct($message, $code);

    }

}

