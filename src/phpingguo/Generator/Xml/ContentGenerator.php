<?php
namespace Phpingguo\System\Generator\Xml;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\System\Generator\IGenerator;
use Phpingguo\System\Module\BaseModule;
use Phpingguo\UzukiXml\UzukiXml;

/**
 * XMLのコンテンツデータを生成するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ContentGenerator implements IGenerator
{
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IGenerator::build
     */
    public function build(BaseModule $module, array $options)
    {
        $variables = $module->getModuleData()->getVariables();
        Arrays::removeWhen(true, $variables, 'response');

        $uzuki = new UzukiXml([
            UzukiXml::OPTION_VERSION => '1.0',
            UzukiXml::OPTION_CHARSET => $module->getResponse()->getCharset()->getValue()
        ]);

        return $uzuki->render($variables);
    }
}
