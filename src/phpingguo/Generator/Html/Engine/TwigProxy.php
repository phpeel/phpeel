<?php
namespace Phpingguo\System\Generator\Html\Engine;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Enums\Charset;
use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Generator\Html\BaseEngineProxy;

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
