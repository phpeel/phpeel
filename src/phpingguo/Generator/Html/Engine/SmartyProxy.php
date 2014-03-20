<?php
namespace Phpingguo\System\Generator\Html\Engine;

use Phpingguo\System\Generator\Html\IHtmlGenerator;
use Phpingguo\System\Module\BaseModule;

/**
 * Smarty のテンプレートエンジンを仲介するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class SmartyProxy implements IHtmlGenerator
{
    /**
     * @see IHtmlGenerator::render
     */
    public function render(BaseModule $module)
    {
        return __CLASS__;
    }
}
