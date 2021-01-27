<?php
namespace SkachCz\Imokutr;

use SkachCz\Imokutr\Config;
use SkachCz\Imokutr\Image;
use SkachCz\Imokutr\Thumbnail;

use SkachCz\Imokutr\Exception\ImokutrWrongMacroParameterException;

/**
 * Main class
 *
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
 class Imokutr {

    /** @var Config */
    public $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    /**
    * @return Imokutr/Config.php
    */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Returns thumbnail url
     * @return string
     */
    public function getThumbnailUrl(string $path, int $width, int $height, int $fixed = Image::DIM_WIDTH, int $cropType = Image::CROP_CENTER, bool $force = false) {

        $image = new Image($this->config->originalRootPath, $path, $this->config->defaultImageRelativePath);
        $thumbnail = new Thumbnail($this->config, $image);
        $thumbnail->setResize($width, $height, $fixed, $cropType);
        $thumbnail->processImage($force);

        return $thumbnail->getThumbnailUrl();

    }

    /**
     * Returns thumbnail array
     * @return array
    */
    public function getThumbnail(string $path, int $width, int $height, string $fixedPar = 'w', int $cropType = Image::CROP_CENTER, bool $force = false) {

        switch($fixedPar) {

            case 'c':
                $fixed = Image::DIM_CROP;
                break;

            case 'h':
                $fixed = Image::DIM_HEIGHT;
                break;

            default:
                $fixed = Image::DIM_WIDTH;

        }

        $image = new Image($this->config->originalRootPath, $path, $this->config->defaultImageRelativePath);
        $thumbnail = new Thumbnail($this->config, $image);
        $thumbnail->setResize($width, $height, $fixed, $cropType);
        $thumbnail->processImage($force);

        return $thumbnail->getThumbnailData();

    }

    /**
     * helper for Nette macro
     */
    public function macroThumbInterface($path = null, $width = null, $height = null, $fixedPar = 'w', $cropType = Image::CROP_CENTER, bool $force = false) {

        if (!$this->config->defaultImageRelativePath && null === $path) {
            throw new ImokutrWrongMacroParameterException("1 (path)", "valid relative path to the image");
        }

        if ((!is_integer($width)) || ($width <= 0)) {
            throw new ImokutrWrongMacroParameterException("2 (width)", "integer higher than 0");
        }

        if ((!is_integer($height)) || ($height <= 0)) {
            throw new ImokutrWrongMacroParameterException("2 (height)", "integer higher than 0");
        }

        if ( !(in_array($fixedPar, ['w','h','c']) )) {
            throw new ImokutrWrongMacroParameterException("3 (resize type)", '"w", "h" or "c"');
        }

        if (!is_integer($cropType)) {
            throw new ImokutrWrongMacroParameterException("4 (crop type)", 'integer between 0 - 8');
        }

        return $this->getThumbnail(strval($path), intval($width), intval($height), strval($fixedPar), intval($cropType), $force);

    }


}
