<?php

namespace SkachCz\Imokutr\Exception;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrFileNotFoundException extends \RuntimeException
{

    public function __construct(string $path = null, string $message = null)
    {

        $code = 1;

        if (null === $message) {
            if (null === $path) {
                $message = 'Image file could not be found.';
            } else {
                $message = sprintf('Image file "%s" could not be found.', $path);
            }
        }

        return parent::__construct($message, $code);

    }

}
