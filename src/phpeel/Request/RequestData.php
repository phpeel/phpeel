<?php
namespace Phpeel\System\Request;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\ApricotLib\Common\Number;
use Phpeel\ApricotLib\Common\String;
use Phpeel\System\Core\Config;
use Phpeel\System\Enums\HttpMethod;

/**
 * クライアントからサーバーへ送信されたリクエストデータを表すクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class RequestData
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $method  = null;
    private $version = 0.0;
    private $module  = null;
    private $scene   = null;
    private $params  = [];
    
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * RequestData クラスの新しいインスタンスを初期化します。
     *
     * @param HttpMethod|String $method      クライアントが要求したメソッドの名前
     * @param String $module                 要求したモジュールの名前
     * @param Float $version [初期値=0.0]    要求したバージョン番号
     * @param String $scene [初期値=null]    要求したシーンの名前
     * @param Array $params [初期値=array()] 要求したシーンに渡すパラメータの配列
     */
    public function __construct($method, $module, $version = 0.0, $scene = null, array $params = [])
    {
        $this->setMethod($method);
        $this->setApiVersion($version);
        $this->setModuleName($module);
        $this->setSceneName($scene);
        $this->setParameters($params);
    }
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 要求された HTTP メソッドを取得します。
     * 
     * @return HttpMethod 要求された HTTP メソッド
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * 要求された API のバージョン番号を取得します。
     * 
     * @return Float 要求された API のバージョン番号
     */
    public function getApiVersion()
    {
        return $this->version;
    }
    
    /**
     * 要求されたモジュールの名前を取得します。
     * 
     * @return String 要求されたモジュールの名前
     */
    public function getModuleName()
    {
        return $this->module;
    }
    
    /**
     * 要求されたシーンの名前を取得します。
     * 
     * @return String 要求されたシーンの名前
     */
    public function getSceneName()
    {
        return $this->scene;
    }
    
    /**
     * 要求されたシーンに渡すパラメータの一覧を取得します。
     * 
     * @return Array 要求されたシーンに渡すパラメータの一覧
     */
    public function getParameters()
    {
        return $this->params;
    }
    
    /**
     * 要求されたシーンに渡すパラメータの一覧を設定します。
     * 
     * @param Array $params 要求されたシーンに渡す新しいパラメータの一覧
     */
    public function setParameters(array $params)
    {
        $this->params = empty($params) ? [] : $params;
    }
    
    /**
     * 指定した名前のパラメータが存在するかどうかを調べます。
     * 
     * @param String $name 存在を調べるパラメータの名前
     * 
     * @throws \InvalidArgumentException パラメータ $name が文字列型ではなかった場合
     * 
     * @return Boolean パラメータが存在する場合は true。それ以外の場合は false。
     */
    public function isExistParameter($name)
    {
        if (String::isValid($name) === false) {
            throw new \InvalidArgumentException('$name parameter only accepts string.');
        }
        
        return isset($this->params[$name]);
    }
    
    /**
     * 指定した名前のパラメータの値を取得します。
     * 
     * @param String $name 値を取得するパラメータの名前
     * 
     * @return mixed|null パラメータが存在する場合はその値。それ以外の場合は null。
     * パラメータ $name が文字列型ではなかった場合には InvalidArgumentException の例外がスローされます。
     */
    public function getParameter($name)
    {
        return $this->isExistParameter($name) ? $this->params[$name] : null;
    }
    
    /**
     * 指定した名前のパラメータの値を設定します。
     * 
     * @param String $name 値を設定するパラメータの名前
     * @param mixed $value パラメータの新しい値
     * 
     * @throws \InvalidArgumentException パラメータ $name が文字列（空白以外）または数値以外の値である場合
     */
    public function setParameter($name, $value)
    {
        // 文字列以外はパラメータ配列のキーの値として受け付けない
        if (Arrays::addWhen(String::isValid($name), $this->params, $value, $name) === false) {
            throw new \InvalidArgumentException('$name parameter only accepts scalar.');
        }
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 要求された HTTP メソッドを設定します。
     * 
     * @param HttpMethod|String $method 要求された HTTP メソッドを示す名前
     */
    private function setMethod($method)
    {
        $this->method = HttpMethod::init(
            $method,
            HttpMethod::GET,
            function () use ($method) {
                return String::isValid($method, true);
            }
        );
    }
    
    /**
     * 要求された API のバージョン番号を設定します。
     * 
     * @param Float $version 要求された API のバージョン番号
     */
    private function setApiVersion($version)
    {
        $this->version = (empty($version) === false && Number::isValidUFloat($version)) ? $version :
            floatval(Config::get('sys.versioning.default_num', 1.0));
    }
    
    /**
     * 要求されたモジュールの名前を設定します。
     * 
     * @param String $module 要求されたモジュールの名前
     */
    private function setModuleName($module)
    {
        $this->module = String::isValid($module, true) ? $module : Config::get('sys.default_module_name', 'top');
    }
    
    /**
     * 要求されたシーンの名前を設定します。
     * 
     * @param String $scene 要求されたシーンの名前
     */
    private function setSceneName($scene)
    {
        $this->scene = String::isValid($scene, true) ? $scene : Config::get('sys.default_scene_name', 'index');
    }
}
