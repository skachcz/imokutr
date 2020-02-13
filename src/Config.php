<?php
namespace SkachCz\Imokutr;

/**
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
class Config {

    /** @var string */
    public $originalRootPath;

    /** @var string */
    public $thumbsRootPath;

    /** @var string */
    public $thumbsRootRelativePath;

    /** @var string */
    public $defaultImageRelativePath;

    /** @var int */
    public $qualityJpeg;

    /** @var int */
    public $qualityPng;


    public function __construct(string $originalRootPath, string $thumbsRootPath, string $thumbsRootRelativePath,
                    string $defaultImageRelativePath = null, int $qualityJpeg = 75, int $qualityPng = 6) {

        $this->originalRootPath = $this->replaceAppRoot($originalRootPath);
        $this->thumbsRootPath = $this->replaceAppRoot($thumbsRootPath);
        $this->thumbsRootRelativePath = $thumbsRootRelativePath;
        $this->defaultImageRelativePath = $defaultImageRelativePath;
        $this->qualityJpeg = $qualityJpeg;
        $this->qualityPng = $qualityPng;

    }

    /**
    * @return array
    */
    public function getConfigArray() {
        return [
            'originalRootPath' => $this->originalRootPath,
            'thumbsRootPath'  => $this->thumbsRootPath,
            'thumbsRootRelativePath' => $this->thumbsRootRelativePath,
            'defaultImageRelativePath' => $this->defaultImageRelativePath,
            'qualityJpeg' => $this->qualityJpeg,
            'qualityPng' => $this->qualityPng,
        ];
    }

    public function replaceAppRoot($path) {

        if (defined('WWW_DIR')) {
            return str_replace("~", WWW_DIR, $path);
        } else {
            return $path;
        }
    }

}