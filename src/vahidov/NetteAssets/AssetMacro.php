<?php
declare(strict_types = 1);

namespace Vahidov\NetteAssets;

use Nette;
use Latte;


class AssetMacro extends Latte\Macros\MacroSet
{
    use Nette\SmartObject;

    const CONFIG_PROVIDER = 'assetMacroConfig';


    public static function install(Latte\Compiler $compiler)
    {
        $me = new self($compiler);
        $me->addMacro('asset', [$me, 'macroAsset']);
        $me->addMacro('livereloadscript', [$me, 'macroLivereloadScript']);
    }

    public function macroAsset(Latte\MacroNode $node, Latte\PhpWriter $writer): string
    {
        if ($node->modifiers && $node->modifiers!='|noescape') {
            throw new Latte\CompileException('Only \'noescape\' modifier is allowed in ' . $node->getNotation());
        }
        // Validate arguments count
        $args = trim($node->args);
        $argsCount = $args==='' ? 0 : (substr_count($args, ',') + 1);
        if ($argsCount===0) {
            throw new Latte\CompileException("Assets macro requires at least one argument.");
        }

        return $writer->write(
            'echo ' . ($node->modifiers!=='|noescape' ? '%escape' : '') .
            '(' . self::class . '::getOutputAsset(' .
            '%node.word, ' .
            '%node.array, ' .
            '$this->global->' . self::CONFIG_PROVIDER . '))');
    }

    public static function getOutputAsset($asset, array $args, $config): string
    {
        if ($config['productionMode']!=true) {
            return ($config['scripthost'] ? $config['scripthost'] : '//' . $_SERVER['HTTP_HOST']) . ($config['scriptport'] ? ':' . $config['scriptport'] : '') . '/' . trim($asset, '/');
        } else {
            $asset = trim($asset, '/');
            if (isset($config['manifest-data'][$asset])) {
                return '/' . $config['manifest-data'][$asset];
            }
            return '/' . $asset;
        }
    }


    public function macroLivereloadScript(Latte\MacroNode $node, Latte\PhpWriter $writer): string
    {
        if ($node->modifiers && $node->modifiers!='|noescape') {
            throw new Latte\CompileException('Only \'noescape\' modifier is allowed in ' . $node->getNotation());
        }
        return $writer->write(
            'echo ' .
            '(' . self::class . '::getOutputLivereloadScript(' . '$this->global->' . self::CONFIG_PROVIDER . '))');
    }

    public static function getOutputLivereloadScript($config): string
    {
        if ($config['productionMode']==true) {
            return '';
        }
        return '<script type="text/javascript" src="' . ($config['livereloadhost'] ? $config['livereloadhost'] : '//' . $_SERVER['HTTP_HOST']) . ($config['livereloadport'] ? ':' . $config['livereloadport'] : '') . '/livereload.js' . '"></script>';
    }


    public static function getManifest(array $config): array
    {
        $path = rtrim($config['wwwDir'], '/') . '/../../' . $config['manifestFile'];
        if (!is_file($path)) {
            return [];
        }
        return Nette\Utils\Json::decode(file_get_contents($path), Nette\Utils\Json::FORCE_ARRAY);
    }

}
