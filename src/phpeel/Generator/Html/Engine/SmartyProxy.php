<?php
namespace Phpeel\System\Generator\Html\Engine;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\System\Core\Supervisor;
use Phpeel\System\Generator\Html\Adapter\SmartyAdapter;
use Phpeel\System\Generator\Html\BaseEngineProxy;

/**
 * Smarty のテンプレートエンジンを仲介するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class SmartyProxy extends BaseEngineProxy
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
        /** @var SmartyAdapter $smarty_loader */
        $smarty_loader = $this->createInstance('SmartyAdapter', []);
        
        $smarty_loader->setTemplateDir(Supervisor::getViewPath());
        $smarty_loader->setCacheDir(Supervisor::getCachePath(Supervisor::PATH_CACHE_SMARTY));
        $smarty_loader->setCompileDir(Supervisor::getCachePath(Supervisor::PATH_CACHE_SMARTY));
        
        $smarty_loader->caching         = true;
        $smarty_loader->cache_lifetime  = -1;
        $smarty_loader->force_cache     = Arrays::getValue($options, 'DebugMode', true);
        $smarty_loader->force_compile   = Arrays::getValue($options, 'DebugMode', true);
        $smarty_loader->left_delimiter  = Arrays::getValue($options, 'LeftDelimiter', '{{');
        $smarty_loader->right_delimiter = Arrays::getValue($options, 'RightDelimiter', '}}');
        $smarty_loader->escape_html     = true;
        
        $this->setEngineInstance($smarty_loader);
    }
}
