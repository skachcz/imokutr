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

    /** @var int */
    public $qualityJpeg;        

    /** @var int */
    public $qualityPng;        

    
    public function __construct(string $originalRootPath, string $thumbsRootPath, string $thumbsRootRelativePath,
                    int $qualityJpeg = 75, int $qualityPng = 6) {

        $this->originalRootPath = $originalRootPath;
        $this->thumbsRootPath = $thumbsRootPath;
        $this->thumbsRootRelativePath = $thumbsRootRelativePath;
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
            'qualityJpeg' => $this->qualityJpeg,
            'qualityPng' => $this->qualityPng,
        ];
    }
    
}