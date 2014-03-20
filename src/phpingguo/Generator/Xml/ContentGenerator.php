<?php
namespace Phpingguo\System\Generator\Xml;

use Phpingguo\System\Generator\IGenerator;
use Phpingguo\System\Module\BaseModule;

/**
 * XMLのコンテンツデータを生成するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ContentGenerator implements IGenerator
{
    /**
     * @see IGenerator::build
     */
    public function build(BaseModule $module)
    {
        return __CLASS__;
    }
}
