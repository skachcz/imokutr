<?php

namespace SkachCz\Imokutr;

use SkachCz\Imokutr\Image;

/**
 * Image transformation arithmetics
 * 
 * @package SkachCz\Imokutr
 * @author Vladimir Skach
 */
class ImageTools {


    /**
    * @return array
    */    
    public static function resizeRatio(int $width, int $height, int $newWidth, int $newHeight, int $fixedDimension) {

        switch($fixedDimension) {

            case Image::DIM_WIDTH:
                return ImageTools::resizeImageToWidth($width, $height, $newWidth, $newHeight);
                break;

            case Image::DIM_HEIGHT:
                return ImageTools::resizeImageToHeight($width, $height, $newWidth, $newHeight);
                break;

            case Image::DIM_CROP:
                return ImageTools::resizeImageToCrop($width, $height, $newWidth, $newHeight);
                break;                

            default:
                return [$newWidth, $newHeight];

        }

    }

    /**
     * Returns cropped dimensions
     * 
     * @eturn array
     */
    public static function cropSize(int $width, int $height, int $targetWidth, int $targetHeight, int $cropType = Image::CROP_CENTER) {
        
        // the image is landscape or square
        if ($width >= $height) {
            
            if ($targetWidth > $targetHeight) {
                // the cut is landscape
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = intval($targetHeight * $ratio);
            }
            
            if ($targetWidth < $targetHeight) {
                // the cut is portrait
                $ratio = $height / $targetHeight;
                $cropWidth = intval($targetWidth * $ratio);
                $cropHeight = $height;
            }

            if ($targetWidth == $targetHeight) {
                //the cut is square
                $ratio = $height / $targetHeight;
                $cropWidth = intval($targetWidth * $ratio);
                $cropHeight = $height;
            }

        } else {
        // image is portrait

            if ($targetWidth > $targetHeight) {
                // the cut is landscape
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = intval($targetHeight * $ratio);
            }
            
            if ($targetWidth < $targetHeight) {
                // the cut is portrait
                $ratio = $height / $targetHeight;
                $cropWidth = intval($targetWidth * $ratio);
                $cropHeight = $height;
            }

            if ($targetWidth == $targetHeight) {
                // the cut is square
                $ratio = $width / $targetWidth;
                $cropWidth = $width;
                $cropHeight = intval($targetHeight * $ratio);
                
            }
        }

        // computes top left point:
        $centerX = intval (($width - $cropWidth) /2);  
        $centerY = intval (($height - $cropHeight) /2);  
        
        switch($cropType) {

            case Image::CROP_LEFT_TOP:
                $cx = 0; 
                $cy = 0;
            break;

            case Image::CROP_CENTER_TOP:
                $cx = $centerX;; 
                $cy = 0;
            break;

            case Image::CROP_RIGHT_TOP:
                $cx = $width - $cropWidth;
                $cy = 0;
            break;

            case Image::CROP_RIGHT_CENTER:
                $cx = $width - $cropWidth;    
                $cy = $centerY;
            break;

            case Image::CROP_RIGHT_BOTTOM:
                $cx = $width - $cropWidth;    
                $cy = $height - $cropHeight;
            break;            

            case Image::CROP_CENTER_BOTTOM:
                $cx = $centerX;    
                $cy = $height - $cropHeight;
            break;                        

            case Image::CROP_LEFT_BOTTOM:
                $cx = 0;    
                $cy = $height - $cropHeight;
            break;                                    

            case Image::CROP_LEFT_CENTER:
                $cx = 0;    
                $cy = $centerY;
            break;                                                


            case Image::CROP_CENTER:
            default:
                $cx = $centerX; 
                $cy = $centerY; 
        }

        return ["x" => $cx, "y" => $cy, "width" => $cropWidth, "height" => $cropHeight];
    }

    /**
     * Computes new dimensions for thumbnail based on original width
     * 
     * @return array [newWidth, newHeight]
     */    
    public static function resizeImageToWidth(int $width, int $height, int $newWidth, int $newHeight) {
        
        $wRatio = $newWidth / $width;
        $newHeight = intval ($height * $wRatio);
        
        return [$newWidth, $newHeight];
    }

    /**
     * Computes new dimensions for thumbnail based on original height
     * @return array [newWidth, newHeight]
     */    
    public static function resizeImageToHeight(int $width, int $height, int $newWidth, int $newHeight) {
        
        $hRatio = $newHeight / $height;
        $newWidth = intval ($width * $hRatio);
        
        return [$newWidth, $newHeight];
    }

    /**
     * Computes new dimensions for thumbnail based on original ratio
     * @return array [new_width, new_height]
     */    
    public static function resizeImageToCrop(int $width, int $height, int $newWidth, int $newHeight) {
  
        if ($width > $height) {
            list($newW, $newH) = Imagetools::resizeImageToHeight($width, $height, $newWidth, $newHeight);
        } else {
            list($newW, $newH) = Imagetools::resizeImageToWidth($width, $height, $newWidth, $newHeight);
        }

        return [$newW, $newH];
    }


}
