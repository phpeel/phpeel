<?php
namespace Phpingguo\System\Generator\Html;

use Phpingguo\System\Enums\TemplateEngine;
use Phpingguo\System\Generator\IGenerator;
use Phpingguo\System\Generator\TGeneratorList;
use Phpingguo\System\Module\BaseModule;

/**
 * HTMLのコンテンツデータを生成するクラスです。
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
     * 
     * @param Array $generator_list コンテンツデータを生成するクラスのインスタンス
     */
    public function __construct(array $generator_list)
    {
        $this->setGeneratorList($generator_list);
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IGenerator::build
     */
    public function build(BaseModule $module, array $options)
    {
        $enum    = $module->getModuleData()->getEngine();
        $default = new TemplateEngine(TemplateEngine::TWIG);
        
        /** @var IHtmlGenerator $builder */
        $builder       = $this->getGeneratorInstance($enum, $default);
        $build_options = $this->getGeneratorOptions($enum, $default);
        
        return $builder->render($module, $build_options);
    }
}
