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
        $origin = General::getParsedYamlFile(Supervisor::getServerEnvPath(null), $yaml_filename);
        $parsed = General::getParsedYamlFile(Supervisor::getServerEnvPath(), $yaml_filename);
        
        if (Arrays::mergeWhen(Arrays::isValid($origin) && Arrays::isValid($parsed), $origin, $parsed)) {
            $this->generator_list = $origin;
        }
    }

    /**
     * コンテンツを生成するクラスの一覧から指定したキーの値を取得します。
     * 
     * @param String $key                   値を取得するキーの名前
     * @param String $default [初期値=null] 該当する値がない場合に参照するキーの名前
     * @param mixed $nothing [初期値=null]  値が取得できなかった場合に使用する値
     *
     * @return mixed コンテンツを生成するクラスの一覧から指定したキーに該当する値
     */
    final protected function getGeneratorList($key, $default = null, $nothing = null)
    {
        return Arrays::findValue(
            $this->generator_list,
            $key,
            Arrays::findValue($this->generator_list, $default, $nothing)
        );
    }

    /**
     * コンテンツを生成するクラスの一覧を設定します。
     * 
     * @final [オーバーライド禁止]
     * @param Array $new_list 新しいクラス一覧
     */
    final protected function setGeneratorList(array $new_list)
    {
        Arrays::isValid($new_list) && $this->generator_list = $new_list;
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
        return Supervisor::getDiContainer(null)->newInstance(
            $this->getGeneratorList("{$enum->getName()}=>class", "{$default->getName()}=>class"),
            [ $this->getGeneratorList("{$enum->getName()}=>wrapper", "{$default->getName()}=>wrapper") ]
        );
    }

    /**
     * コンテンツを生成するクラスのインスタンスが使用するオプション設定データを取得します。
     * 
     * @final [オーバーライド禁止]
     * @param Enum $enum    コンテンツを生成するクラスを示す列挙型
     * @param Enum $default クラスを示す値が見つからなかった場合の代用値
     *
     * @return Array コンテンツを生成するクラスのインスタンスが使用するオプション設定データ
     */
    final protected function getGeneratorOptions(Enum $enum, Enum $default)
    {
        return $this->getGeneratorList("{$enum->getName()}=>options", "{$default->getName()}=>options", []);
    }
}
