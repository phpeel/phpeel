<?php
namespace Phpeel\System\Generator\Html;

use Phpeel\ApricotLib\Common\String;
use Phpeel\System\Core\Supervisor;
use Phpeel\System\Module\BaseModule;

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
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IHtmlGenerator::render
     */
    public function render(BaseModule $module, array $options)
    {
        $this->isInitialized() || $this->initEngineInstance($options);
        
        return $this->getEngineInstance()->rendering($module);
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * テンプレートエンジンを操作するクラスのインスタンスを初期化します。
     * 
     * @codeCoverageIgnore
     * @param Array $options 初期化オプション
     */
    abstract protected function initEngineInstance(array $options);

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
            String::concat('Phpeel\\System\\Generator\\Html\\Adapter\\', $class_name),
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
