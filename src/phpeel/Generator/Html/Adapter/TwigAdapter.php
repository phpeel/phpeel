<?php
namespace Phpeel\System\Generator\Html\Adapter;

use Phpeel\System\Generator\Html\IEngineAdapter;
use Phpeel\System\Generator\Html\TRenderAdapter;
use Phpeel\System\Module\BaseModule;

/**
 * Twigのテンプレートエンジンをアプリケーションの機能として統合させるクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class TwigAdapter extends \Twig_Environment implements IEngineAdapter
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
        return $this->render($this->getFileName($module), $this->getParameters($module));
    }
}
