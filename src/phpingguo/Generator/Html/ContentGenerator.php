<?php
namespace Phpingguo\System\Generator\Html;

use Phpingguo\System\Enums\TemplateEngine;
use Phpingguo\System\Generator\IGenerator;
use Phpingguo\System\Generator\TGeneratorList;
use Phpingguo\System\Module\BaseModule;

/**
 * HTML/XMLのコンテンツデータを生成するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ContentGenerator implements IGenerator
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TGeneratorList;

    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * ContentGenerator クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        $this->initGeneratorList('html_generator_list');
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IGenerator::build
     */
    public function build(BaseModule $module)
    {
        /** @var IHtmlGenerator $builder */
        $builder = $this->getGeneratorInstance(
            $module->getModuleData()->getEngine(),
            new TemplateEngine(TemplateEngine::TWIG)
        );
        
        return $builder->render($module);
    }
}
