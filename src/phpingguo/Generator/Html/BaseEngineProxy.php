<?php
namespace Phpingguo\System\Generator\Html;

use Phpingguo\ApricotLib\Common\String;
use Phpingguo\System\Core\Supervisor;

/**
 * テンプレートエンジンを仲介するクラスの共通機能を実装する抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseEngineProxy implements IHtmlGenerator
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $engine = null;

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 指定した名前のテンプレートエンジンアダプターに属するクラスのインスタンスを生成します。
     * 
     * @final [オーバーライド禁止]
     * @param String $class_name             インスタンスを生成するクラスの名前
     * @param Array $params [初期値=array()] クラスを生成する時に渡すパラメータの配列
     * 
     * @return IEngineAdapter テンプレートエンジンアダプターに属するクラスのインスタンス
     */
    final protected function createInstance($class_name, array $params = [])
    {
        return Supervisor::getDiContainer(null)->newInstance(
            String::concat('Phpingguo\\System\\Generator\\Html\\Adapter\\', $class_name),
            $params
        );
    }

    /**
     * テンプレートエンジンアダプタークラスが既に初期化済みかどうかを判定します。
     * 
     * @final [オーバーライド禁止]
     * @return Boolean 初期化済みの場合は true。それ以外の場合は false。
     */
    final protected function isInitialized()
    {
        return $this->engine instanceof IEngineAdapter;
    }

    /**
     * テンプレートエンジンアダプタークラスのインスタンスを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return IEngineAdapter テンプレートエンジンアダプタークラスのインスタンス
     */
    final protected function getEngineInstance()
    {
        return $this->engine;
    }

    /**
     * テンプレートエンジンアダプタークラスのインスタンスを設定します。
     * 
     * @final [オーバーライド禁止]
     * @param IEngineAdapter $instance テンプレートエンジンアダプタークラスのインスタンス
     */
    final protected function setEngineInstance(IEngineAdapter $instance)
    {
        $this->engine = $instance;
    }
}
