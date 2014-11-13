<?php
namespace Phpeel\System\Generator\Html\Adapter;

use Phpeel\System\Generator\Html\IEngineAdapter;
use Phpeel\System\Generator\Html\TRenderAdapter;
use Phpeel\System\Module\BaseModule;

/**
 * Smartyのテンプレートエンジンをアプリケーションの機能として統合させるクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class SmartyAdapter extends \Smarty implements IEngineAdapter
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TRenderAdapter;

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IEngineAdapter::rendering
     */
    public function rendering(BaseModule $module)
    {
        $this->assign($this->getParameters($module));
        
        return $this->fetch($this->getFileName($module));
    }
}
