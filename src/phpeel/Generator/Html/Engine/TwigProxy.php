<?php
namespace Phpeel\System\Generator\Html\Engine;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\ApricotLib\Enums\Charset;
use Phpeel\System\Core\Supervisor;
use Phpeel\System\Generator\Html\BaseEngineProxy;

/**
 * Twig のテンプレートエンジンを仲介するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class TwigProxy extends BaseEngineProxy
{
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * Twigのテンプレートエンジンを操作するクラスのインスタンスを初期化します。
     * 
     * @param Array $options 初期化オプション
     */
    protected function initEngineInstance(array $options)
    {
        $twig_loader  = new \Twig_Loader_Filesystem([ Supervisor::getViewPath() ]);
        $twig_options = [
            'debug'       => Arrays::getValue($options, 'DebugMode', true),
            'charset'     => Arrays::getValue($options, 'Charset', Charset::UTF8),
            'cache'       => Supervisor::getCachePath(Supervisor::PATH_CACHE_TWIG),
            'auto_reload' => Arrays::getValue($options, 'Recompile', true),
            'autoescape'  => Arrays::getValue($options, 'AutoEscape', true),
        ];
        
        $this->setEngineInstance($this->createInstance('TwigAdapter', [ $twig_loader, $twig_options ]));
    }
}
