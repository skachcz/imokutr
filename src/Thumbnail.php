<?php
namespace SkachCz\Imokutr;

use SkachCz\Imokutr\Config;
use SkachCz\Imokutr\Image;
use SkachCz\Imokutr\ImageTools;
use SkachCz\Imokutr\Exception\ImokutrUnknownImageTypeException;

use Tracy\Debugger;

/**
 * Thumbnail class
 * 
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
class Thumbnail {

    /** @var SkachCz\Imokutr\Config */
    public $config;

    /** @var Skachcz\Imokutr\Image */
    public $image;

    /** @var bool */
    public $isAvailable;

    /** @var int */
    public $width;

    /** @var int */
    public $height;

    /** @var int */
    public $targetWidth;

    /** @var int */
    public $targetHeight;

    /** @var int */
    public $fixedDimension;    

    /** @var int */
    public $cropType;    

    public function __construct(Config $config, Image $image)
	{
        $this->config = $config;
        $this->image = $image;
        $this->isAvailable = false;
    }
    
    /**
    * @return array|false Returns thumbnail data or false
    */    
    public function getThumbnailData() {

        if ($this->isAvailable) {

            return(
                [
                    "url" => $this->getThumbnailUrl(),
                    "width" => $this->width,
                    "height" => $this->height,
                ]
            );

        } else {
            return false;
        }

    }

    /**
    * @return string Returns thumbnail url
    */    
    public function getThumbnailUrl() {
        
        return $this->config->thumbsRootRelativePath . '/' . trim($this->image->relpath, '/') . '/' . $this->getThumbnalFilename();
    }

    /**
    * @return void
    */    
    public function setResize(int $width, int $height, int $fixedDimension = Image::DIM_WIDTH, int $cropType = Image::CROP_CENTER) {

        $this->width = $width;
        $this->height = $height;
        $this->targetWidth = $width;
        $this->targetHeight = $height;        
        $this->fixedDimension = $fixedDimension;
        $this->cropType = $cropType;

    }

    /**
     * Processes image and returns thumbnail data
     * @return array 
     */    
    public function processImage(bool $force = false) {

        $targetPath = $this->config->thumbsRootPath . '/' . $this->image->relpath;
        $targetFile = $targetPath . '/' . $this->getThumbnalFilename();

        if ($force || (!file_exists($targetFile))) {
            
            if(!is_dir($targetPath)) {
                mkdir($targetPath, 0775, TRUE);
            }
            
            $this->createThumbnail($targetFile , $this->targetWidth, $this->targetHeight);
            $this->isAvailable = TRUE;

        } else {
            // we will get dimensions from already existing file
            list($this->width, $this->height) = getimagesize($targetFile);
            $this->isAvailable = TRUE;
        }

        return $this->getThumbnailData();
    }

    /**
    * @return string Returns thumbnail filename
    */    
    public function getThumbnalFilename() {
        
        return ltrim($this->image->filebase, '/') . "-" . $this->targetWidth . "x" . $this->targetHeight . "-" . $this->fixedDimension
        . "-" . $this->cropType . ".". $this->image->fileext;
    }

    /**
    * @return string
    */    
    public function createThumbnail(string $targetPath, int $width, int $height) {

        return $this->resizeImage($targetPath, $width, $height, $this->image->type, $this->cropType);

    }

    /**
     * Creates thumbnail image and saves it to disk
     * 
     * @return string
     */    
    private function resizeImage(string $targetPath, int $width, int $height, int $type = null, int $cropType = Image::CROP_CENTER) {

        $origWidth = $this->image->width;
        $origHeight = $this->image->height;

        $src = $this->createImageFrom($this->image->fullpath, $type);

        // Cropping image, if needed.
        if($this->fixedDimension == Image::DIM_CROP) {
            $cropedSize = ImageTools::cropSize($origWidth, $origHeight, $width, $height, $cropType);
            $src = imagecrop($src, $cropedSize);

            $origWidth = $cropedSize["width"];
            $origHeight = $cropedSize["height"];
        }

        list($newWidth, $newHeight) = ImageTools::resizeRatio($origWidth, $origHeight, $width, $height, $this->fixedDimension);        

        $img = \imagecreatetruecolor($newWidth, $newHeight); 

        switch($type) {

            case IMAGETYPE_JPEG:

                \imagecopyresampled($img, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight); 
                \imagejpeg($img, $targetPath, $this->config->qualityJpeg);
            break;

            case IMAGETYPE_PNG:

                \imagealphablending($img, false );
                \imagesavealpha($img, true );
                \imagecopyresampled($img, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight); 
                
                \imagepng($img, $targetPath, $this->config->qualityPng);
            break;

            case IMAGETYPE_GIF:
                
                // check transparency
                $tIndex = imagecolortransparent($src);

                Debugger::barDump($tIndex, "tIndex");

                if ($tIndex >= 0) {
                    $tColor  = \imagecolorsforindex($src, $tIndex);

                    Debugger::barDump($tColor, "tIndex");

                    $transparency = \imagecolorallocate($img, $tColor['red'], $tColor['green'], $tColor['blue']);
                    \imagefill($img, 0, 0, $transparency);
                    \imagecolortransparent($img, $transparency);
                } else {
                    \imagealphablending($img, false);
                    \imagesavealpha($img, true );
                } 

                \imagecopyresampled($img, $src, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight); 
                
                \imagegif($img, $targetPath);
            break;

            default:
                throw new ImokutrUnknownImageTypeException($type, $targetPath);
        }

        \imagedestroy($src);
        \imagedestroy($img);

        $this->width = $newWidth;
        $this->height = $newHeight;

        return $targetPath;
    }
    

    /**
     * Creates new image resource
     * 
     * @return object
     */
    public function createImageFrom(string $path, int $imageType = null) {

        switch($imageType) {

            case IMAGETYPE_JPEG:
                return \imagecreatefromjpeg($path);  
            break;

            case IMAGETYPE_PNG:
                return \imagecreatefrompng($path);  
            break;

            case IMAGETYPE_GIF:
                return \imagecreatefromgif($path);  
            break;

            default:
                throw new ImokutrUnknownImageTypeException($imageType, $path);
        }

    }


}
