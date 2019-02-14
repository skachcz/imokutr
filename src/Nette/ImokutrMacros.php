<?php
namespace SkachCz\Imokutr\Nette;

use SkachCz\Imokutr\Config;
use SkachCz\Imokutr\Image;
use SkachCz\Imokutr\Thumbnail;

/**
 * @package SkachCz\Imokutr\Nette
 * @author Vladimir Skach
 */
class ImokutrMacros extends \Latte\Macros\MacroSet {

    /**
     * Install macro
     *
     */
    public static function install(\Latte\Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('imoTag', [$set, 'imoTag'], [$set, 'imoTag'] );
    
        return $set;
    }

    /**
     * Imokutr macro
     * 
     * {imoTag $path, $width, $height, [$resizeType] [, $cropType]]}
     * <img src="%url%" width="%width%" height="%height%" title="titulek">
     * {/imoTag}
     * 
     * @return string
     */
    public function imoTag(\Latte\MacroNode $node, \Latte\PhpWriter $writer)
    {
        
        if ($node->closing) {

            $code = '
            $args = %node.array;

            $imk_width = isset($args[0]) ? $args[0] : 0;
            $imk_height = isset($args[1]) ? $args[1] : 0;
            $imk_fixed = isset($args[2]) ? $args[2] : "w";
            $imk_crop = array_key_exists(3, $args) ? $args[3] : 0;

            $imk_th = $this->global->imokutrProvider->macroThumbInterface(%node.word, $imk_width, $imk_height, $imk_fixed, $imk_crop);
            
            $imk_content = ob_get_clean();
            $imk_content = str_replace("%width%", $imk_th["width"], $imk_content);
            $imk_content = str_replace("%height%", $imk_th["height"], $imk_content);
            $imk_content = str_replace("%url%", $imk_th["url"], $imk_content);
            echo $imk_content;
            ';

        } else {
            $code = "ob_start();";
        }

        return $writer->write(
            $code
        );

    }

}
