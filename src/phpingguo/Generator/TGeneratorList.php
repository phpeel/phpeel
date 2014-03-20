<?php
namespace Phpingguo\System\Generator;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\General;
use Phpingguo\ApricotLib\Type\Enum\Enum;
use Phpingguo\System\Core\Supervisor;

/**
 * コンテンツを生成するクラスを保持するリストを管理するトレイトです。
 * 
 * @author hiroki sugawara
 */
trait TGeneratorList
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $generator_list = [];
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * コンテンツを生成するクラスの一覧を初期化します。
     * 
     * @final [オーバーライド禁止]
     * @param String $yaml_filename クラス一覧データが記述されたYamlファイルの名前
     */
    final protected function initGeneratorList($yaml_filename)
    {
        $parsed_data = General::getParsedYamlFile(
            Supervisor::getConfigPath('content_generators'),
            $yaml_filename
        );
        
        Arrays::isValid($parsed_data) && $this->generator_list = $parsed_data;
    }

    /**
     * コンテンツを生成するクラスのインスタンスを取得します。
     * 
     * @final [オーバーライド禁止]
     * @param Enum $enum    コンテンツを生成するクラスを示す列挙型
     * @param Enum $default クラスを示す値が見つからなかった場合の代用値
     * 
     * @return Object コンテンツを生成するクラスのインスタンス
     */
    final protected function getGeneratorInstance(Enum $enum, Enum $default)
    {
        return Supervisor::getDiContainer(null)
            ->newInstance(Arrays::getValue($this->generator_list, $enum->getName(), $default->getName()));
    }
}
