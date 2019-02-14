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
 class Html {

    public static function img(array $img, string $alt = "", string $title = "", array $attributes = null) {

        $attText = "";

        if (($attributes != null) && is_array($attributes)) {
            
            foreach($attributes as $att => $val) {

                $attText .= sprintf('%s = "%s" ', $att, str_replace('"', '&quot;', $val));

            }
        }
        
        $tag = sprintf('<img src="%s" width="%d" height="%d" alt="%s" title="%s" %s>', 
            $img['url'], $img['width'], $img['height'], $alt, $title, $attText);

        return $tag;
    }


}
