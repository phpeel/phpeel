<?php
namespace Phpingguo\System\Generator;

use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Enums\ContentType;
use Phpingguo\System\Module\BaseModule;

/**
 * レスポンスデータのコンテンツを生成するクラスを仲介するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class GeneratorProxy
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TGeneratorList;

    // ---------------------------------------------------------------------------------------------
    // public static methods
    // ---------------------------------------------------------------------------------------------
    /**
     * GeneratorProxy クラスのインスタンスを取得します。
     * 
     * @param Boolean $reanalyze [初期値=false] コンテンツ生成クラス定義リストの再解析を行うかどうか
     * 
     * @return GeneratorProxy 初回呼び出し時は新しいインスタンス。それ以降の時は既に生成済みのインスタンス。
     */
    public static function getInstance($reanalyze = false)
    {
        /** @var GeneratorProxy $instance */
        $instance = Supervisor::getDiContainer(Supervisor::DIS_SYSTEM)->get(__CLASS__);
        
        if (empty($instance->generator_list) || $reanalyze === true) {
            $instance->initGeneratorList('content_generator_list');
        }
        
        return $instance;
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * レスポンスの内容データに使用するコンテンツをビルドします。
     * 
     * @param BaseModule $module ビルドに使用する情報を保持するモジュールのインスタンス
     * 
     * @return String ビルドにより生成されたコンテンツデータ
     */
    public function buildContent(BaseModule $module)
    {
        $enum    = $module->getResponse()->getContentType();
        $default = new ContentType(ContentType::HTML);
        
        /** @var IGenerator $generator */
        $generator = $this->getGeneratorInstance($enum, $default);
        $options   = $this->getGeneratorOptions($enum, $default);
        
        return $generator->build($module, $options);
    }
}
