<?php

namespace SkachCz\Imokutr\Exception;

/**
 * @package SkachCz\Imokutr\Exception
 * @author Vladimir Skach
 */
class ImokutrGetImageSizeFailedException extends \RuntimeException
{

    public function __construct(string $path = '', string $errorMessage = '', string $message = null)
    {

        $code = 3;

        if (null === $message) {
                $message = sprintf('Function getimagesize() failed (%s), Filename: %s', $errorMessage, $path);
        }

        return parent::__construct($message, $code);

    }

}
