<?php
namespace Phpingguo\System\Generator\Json;

use Phpingguo\System\Generator\IGenerator;
use Phpingguo\System\Module\BaseModule;

/**
 * JSONのコンテンツデータを生成するクラスです。
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
