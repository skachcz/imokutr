<?php

namespace SkachCz\Imokutr\Exception;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrUnknownImageTypeException extends \RuntimeException
{

    public function __construct(int $type = null, string $path = '', string $message = null)
    {

        $code = 2;

        if (null === $message) {
            if (null === $type) {
                $message = sprintf('Unknown image type. Filename: "%s".', $path);
            } else {
                $message = sprintf('Unknown image type %s. Filename: "%s".', $type, $path);
            }
        }

        return parent::__construct($message, $code);

    }

}
