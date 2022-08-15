<?php
namespace SkachCz\Imokutr\Nette;

use SkachCz\Imokutr\Imokutr;
use SkachCz\Imokutr\Config;

use SkachCz\Imokutr\Exception\ImokutrNetteMissingExtension;

use SkachCz\Imokutr\Nette\ImokutrFilters;
use SkachCz\Imokutr\Nette\ImokutrMacros;

use Nette\DI\CompilerExtension;
use Nette\PhpGenerator\ClassType;
use Nette\Utils\Validators;

if (!class_exists('Nette\DI\CompilerExtension')) {
    throw new ImokutrNetteMissingExtension();
}

/**
 * Imokutr Nette extension (for Nette 2.4)
 *
 * @package SkachCz\Imokutr\Nette
 * @author Vladimir Skach
 */
final class ImokutrExtension extends CompilerExtension
{

    /** @var Config */
    protected $config;

    /** @var array */
    private $defaults = [
        'originalRootPath' => null,
        'thumbsRootPath' => null,
        'thumbsRootRelativePath' => null,
        'defaultImageRelativePath' => null,
        'qualityJpeg' => 75,
        'qualityPng' => 6,
    ];

    /**
     * @return Config
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return void
     */
    public function loadConfiguration()
    {
        $cfg = $this->validateConfig($this->defaults);
        $builder = $this->getContainerBuilder();

        Validators::assertField($cfg, 'originalRootPath', 'string');
        Validators::assertField($cfg, 'thumbsRootPath', 'string');
        Validators::assertField($cfg, 'thumbsRootRelativePath', 'string');
        Validators::assertField($cfg, 'qualityJpeg', 'int');
        Validators::assertField($cfg, 'qualityPng', 'int');

        $this->config = new Config($cfg['originalRootPath'],
                                    $cfg['thumbsRootPath'],
                                    $cfg['thumbsRootRelativePath'],
                                    $cfg['defaultImageRelativePath'],
                                    $cfg['qualityJpeg'],
                                    $cfg['qualityPng']);

        //imokutr config provider:
        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->config]);
        /*
        $builder->addDefinition($this->prefix('imokutrProvider'))
            ->setFactory(Imokutr::class, [$this->config]);
        */
    }

    /**
     * @return void
     */
    function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        $methodExists = method_exists($builder->getDefinition('latte.latteFactory'), 'getResultDefinition');

        if ($methodExists) {
            /* nette 3.0: */
            $builder->getDefinition('latte.latteFactory')
                ->getResultDefinition()->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);
        } else {
            $builder->getDefinition('latte.latteFactory')
                ->addSetup('addProvider', ['imokutrProvider', $this->prefix('@imokutrProvider')]);
        }

        if ($builder->hasDefinition('nette.latteFactory')) {

            $factory = $builder->getDefinition('nette.latteFactory');

            // filter registration:
            $filters = new ImokutrFilters($this->config);


            if ($methodExists) {
                /* nette 3.0 */
                $factory->getResultDefinition()->addSetup('addFilter', ['imoUrl', [$filters, 'imoUrl']]);
            } else {
                $factory->addSetup('addFilter', ['imoUrl', [$filters, 'imoUrl']]);
            }

            // macro registration:
            $method = '?->onCompile[] = function($engine)  {
                SkachCz\Imokutr\Nette\ImokutrMacros::install($engine->getCompiler());
            }';

            if ($methodExists) {
                /* nette 3.0 */
                $factory->getResultDefinition()->addSetup($method, ['@self']);
            } else {
                $factory->addSetup($method, ['@self']);
            }

        }
    }

}
