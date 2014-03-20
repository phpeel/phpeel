<?php
namespace Phpingguo\System\Module;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\String;
use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Enums\ModuleFilter;
use Phpingguo\System\Request\Request;
use Phpingguo\System\Response\Response;

/**
 * モジュールのベース機能を提供する抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseModule
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $module_data         = null;
    private $exec_input_filters  = [];
    private $skip_input_filters  = [];
    private $exec_post_filters   = [];
    private $skip_post_filters   = [];
    private $exec_output_filters = [];
    private $skip_output_filters = [];
    private $callback_methods    = [];

    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * BaseModule クラスの新しいインスタンスを初期化します。
     * 
     * @param Request $request モジュールで使用するリクエストデータを管理するクラスのインスタンス
     */
    public function __construct(Request $request = null)
    {
        $instance = Supervisor::getDiContainer(null)
            ->newInstance('Phpingguo\\System\\Module\\ModuleData', [ $request ]);
        
        $this->setModuleData($instance);
        $this->getModuleData()->setModuleName($request ? $request->getModuleName() : null);
        $this->getModuleData()->setSceneName($request ? $request->getSceneName() : null);
    }

    /**
     * BaseModule クラスのインスタンスを破棄します。
     * 
     * @final [オーバーライド禁止]
     */
    final public function __destruct()
    {
        if (Arrays::isValid($this->callback_methods)) {
            try {
                Arrays::eachWalk(
                    $this->callback_methods,
                    function ($method) {
                        is_callable($method) && $method();
                    }
                );
            } catch (\Exception $e) {
                
            }
        }
    }

    // ---------------------------------------------------------------------------------------------
    // override magic methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 指定した名前と紐付けられる値を持つ変数をシーンにバインドします。
     * 
     * @final [オーバーライド禁止]
     * @param String $key  シーンにバインドする変数の名前
     * @param mixed $value 変数に紐付けられる値
     */
    final public function __set($key, $value)
    {
        $this->getModuleData()->setVariable($key, $value);
    }

    /**
     * シーンのバインド変数から指定した名前に紐付けられている値を取得します。
     * 
     * @final [オーバーライド禁止]
     * @param String $key 値を取得するシーンにバインドされている変数の名前
     * 
     * @return mixed バインド変数のうち指定した名前に紐付けられている値
     */
    final public function __get($key)
    {
        return $this->getModuleData()->getVariable($key);
    }

    /**
     * 指定した名前のバインド変数が値を持っているかどうかを調べます。
     * 
     * @final [オーバーライド禁止]
     * @param String $key 値を持っているかどうかを調べるバインド変数の名前
     * 
     * @return Boolean バインド変数が存在し、その値が null でない場合は true。それ以外の場合は false。
     */
    final public function __isset($key)
    {
        return Arrays::isExist($this->getModuleData()->getVariables(), $key);
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * モジュールのデータを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return ModuleData モジュールのデータ
     */
    final public function getModuleData()
    {
        return $this->module_data;
    }

    /**
     * リクエストデータを管理するクラスのインスタンスを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return Request リクエストデータを管理するクラスのインスタンス
     */
    final public function getRequest()
    {
        return $this->getModuleData()->getRequest();
    }

    /**
     * レスポンスデータを管理するクラスのインスタンスを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return Response レスポンスデータを管理するクラスのインスタンス
     */
    final public function getResponse()
    {
        return $this->getModuleData()->getResponse();
    }

    /**
     * リクエストデータを管理するクラスのインスタンスを設定します。
     * 
     * @final [オーバーライド禁止]
     * @param Request $request リクエストデータを管理するクラスの新しいインスタンス
     */
    final public function setRequest(Request $request)
    {
        $this->getModuleData()->setRequest($request);
    }

    /**
     * レスポンスデータを管理するクラスのインスタンスを設定します。
     * 
     * @final [オーバーライド禁止]
     * @param Response $response レスポンスデータを管理するクラスの新しいインスタンス
     */
    final public function setResponse(Response $response)
    {
        $this->getModuleData()->setResponse($response);
    }

    /**
     * 指定したフィルタークラスの種類うち、モジュールが認識しているものの一覧を取得します。
     * 
     * @final [オーバーライド禁止]
     * @param String $type 一覧を取得するフィルタークラスの種類
     * 
     * @return Array モジュールが認識しているフィルタークラスの一覧
     */
    final public function getFilters($type)
    {
        return $this->{"{$this->getFilterPrefix($type)}_filters"};
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * モジュールのデータを設定します。
     * 
     * @final [オーバーライド禁止]
     * @param ModuleData $data モジュールの新しいデータ
     */
    final protected function setModuleData(ModuleData $data)
    {
        $this->module_data = $data;
    }

    /**
     * モジュールクラスが破棄される時に実行するコールバックを登録します。
     * 
     * @final [オーバーライド禁止]
     * @param Callable|Array $callback コールバックメソッドまたはそれらからなる配列
     */
    final protected function entryDestructCallback($callback)
    {
        Arrays::mergeWhen(Arrays::isValid($callback), $this->callback_methods, $callback);
        Arrays::addWhen(is_callable($callback), $this->callback_methods, $callback);
    }

    /**
     * 指定したフィルタークラスの種類の一覧を設定します。
     * 
     * @final [オーバーライド禁止]
     * @param String $type   一覧を設定するフィルタークラスの種類
     * @param Array $filters フィルタークラスの新しい一覧
     */
    final protected function setFilters($type, array $filters)
    {
        Arrays::copyWhen(
            Arrays::isValid($filters),
            $this->{"{$this->getFilterPrefix($type)}_filters"},
            $filters
        );
    }

    /**
     * 指定したフィルターの種類に該当する接頭辞を取得します。
     * 
     * @param String $set_value 接頭辞を取得するフィルターの種類
     * 
     * @throws \InvalidArgumentException パラメータ $set_value が文字列型ではなかった場合
     * @return ModuleFilter フィルター名の接頭辞を保持するクラスのインスタンス
     */
    private function getFilterPrefix($set_value)
    {
        $is_not_null_value = (is_null($set_value) === false);
        
        if ($is_not_null_value === true && String::isValid($set_value, true) === false) {
            throw new \InvalidArgumentException('$set_value only accepts string type.');
        }
        
        return ModuleFilter::init(
            $set_value,
            null,
            function () use ($is_not_null_value) {
                return $is_not_null_value;
            }
        );
    }
}
