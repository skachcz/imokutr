<?php

namespace SkachCz\Imokutr;

use SkachCz\Imokutr\Exception\ImokutrFileNotFoundException;
use SkachCz\Imokutr\Exception\ImokutrGetImageSizeFailedException;

/**
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
class Image {

    const DIM_WIDTH = 1;
    const DIM_HEIGHT = 2;
    const DIM_CROP = 3;

    /*
     * Crop type constants:
     * 
     *   ↖    ↑   ↗
     *     1  2  3
     *  ←  8  0  4  →
     *     7  6  5
     *   ↙    ↓   ↘
     */
    const CROP_CENTER = 0;
    const CROP_LEFT_TOP = 1;
    const CROP_CENTER_TOP = 2;
    const CROP_RIGHT_TOP = 3;
    const CROP_RIGHT_CENTER = 4;
    const CROP_RIGHT_BOTTOM = 5;
    const CROP_CENTER_BOTTOM = 6;
    const CROP_LEFT_BOTTOM = 7;
    const CROP_LEFT_CENTER = 8;

    /** @var string */
    public $relpath;

    /** @var string */
    public $imagepath;

    /** @var string */
    public $fullpath;

    /** @var string */
    public $filepath;

    /** @var string */
    public $filename;

    /** @var string */
    public $filebase;

    /** @var string */
    public $fileext;

    /** @var int */
    public $width;

    /** @var int */
    public $height;

    /** @var string */
    public $type;    

    
    public function __construct(string $rootPath, string $imagePath)
	{
        $fullpath = rtrim($rootPath, '/') . '/' . ltrim($imagePath, '/');
        
        // check if file exists
        if (!file_exists($fullpath)) {
            throw new ImokutrFileNotFoundException($fullpath);
        }
        
        $this->imagepath = $imagePath;
        $this->fullpath = $fullpath;

        $this->setImageInfo();
		
    }
    
    /**
     * Sets basic image properties
     * @return void
     */
    private function setImageInfo() {

        list($width, $height, $type) = @getimagesize($this->fullpath);
        
        if (strpos(error_get_last()["message"], 'getimagesize(') === 0) {
           throw new ImokutrGetImageSizeFailedException($this->fullpath, error_get_last()["message"]);
            // It starts with 'http'
         }

        $this->width = $width;
        $this->height = $height;
        $this->type = $type;

        $parts = pathinfo($this->fullpath);

        $this->filepath = (isset($parts['dirname']) ? $parts['dirname'] : null);
        $this->filebase = (isset($parts['filename']) ? $parts['filename'] : null);
        $this->fileext = (isset($parts['extension']) ? $parts['extension'] : null);
        $this->filename = (isset($parts['basename']) ? $parts['basename'] : null);

        $rpath = pathinfo($this->imagepath);
        $this->relpath = $rpath['dirname'];

    }

}
