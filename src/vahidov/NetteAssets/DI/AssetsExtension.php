<?php declare(strict_types = 1);

namespace Vahidov\NetteAssets\DI;

use Nette\DI;
use Vahidov\NetteAssets\AssetMacro;

class AssetsExtension extends Nette\DI\CompilerExtension
{

    public $defaults = [
        'appDir' => '%appDir%',
        'wwwDir' => '%wwwDir%',
        'productionMode' => '%productionMode%',
        'manifestFile' => '',
        'manifest-data' => [],
    ];

    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->getConfig($this->defaults);
        $this->defaults['manifest-data'] = AssetMacro::getManifest($config);
        $config = $this->getConfig($this->defaults);

        $builder->getDefinition('latte.latteFactory')
            ->addSetup("?->addProvider(?, ?)", ['@self', AssetMacro::CONFIG_PROVIDER, $config])
            ->addSetup("?->onCompile[] = function(\$engine) { " .
                AssetMacro::class . "::install(\$engine->getCompiler()); }",
                ['@self']
            );
    }
}