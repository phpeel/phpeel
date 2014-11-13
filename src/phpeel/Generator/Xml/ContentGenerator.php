<?php
namespace Phpeel\System\Generator\Xml;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\System\Generator\IGenerator;
use Phpeel\System\Module\BaseModule;
use Phpeel\UzukiXml\UzukiXml;

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
