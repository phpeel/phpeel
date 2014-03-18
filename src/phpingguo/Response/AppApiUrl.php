<?php
namespace Phpingguo\System\Response;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\Number;
use Phpingguo\ApricotLib\Common\String;
use Phpingguo\System\Core\Config;

/**
 * アプリケーションのAPIとなるURLアドレスを生成します。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class AppApiUrl
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $api_version = 0.0;
    private $module_name = null;
    private $scene_name  = null;
    private $parameters  = [];

    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * AppApiUrl クラスの新しいインスタンスを初期化します。
     * 
     * @param String $module_name [初期値=null]  モジュールの名前
     * @param Float $api_version [初期値=null]   モジュールのAPIバージョン
     * @param String $scene_name [初期値=null]   シーンの名前
     * @param Array $parameters [初期値=array()] パラメータ配列
     */
    public function __construct($module_name = null, $api_version = null, $scene_name = null, array $parameters = [])
    {
        isset($module_name) && $this->setModule($module_name, $api_version);
        isset($scene_name) && $this->setScene($scene_name);
        $this->setParameters($parameters);
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * URLアドレスに含まれるモジュールの名前を設定します。
     * 
     * @param String $module_name              モジュールの名前
     * @param Float $api_version [初期値=null] モジュールのバージョン番号
     * 
     * @return AppApiUrl メソッド実行後の状態のインスタンス
     */
    public function setModule($module_name, $api_version = null)
    {
        $this->setStringValue($this->module_name, $module_name);
        $this->setApiVersion($api_version);
        
        return $this;
    }

    /**
     * URLアドレスに含まれるシーンの名前を設定します。
     * 
     * @param String $scene_name シーンの名前
     * 
     * @return AppApiUrl メソッド実行後の状態のインスタンス
     */
    public function setScene($scene_name)
    {
        $this->setStringValue($this->scene_name, $scene_name);
        
        return $this;
    }

    /**
     * URLアドレスに含まれるクエリパラメータの配列を設定します。
     * 
     * @param Array $params クエリパラメータ配列
     * 
     * @return AppApiUrl メソッド実行後の状態のインスタンス
     */
    public function setParameters(array $params)
    {
        Arrays::copyWhen(Arrays::isValid($params), $this->parameters, $params);
        
        return $this;
    }

    /**
     * URLアドレスを生成します。
     * 
     * @return String 生成したURLアドレス
     */
    public function createUrl()
    {
        $base_api_url = implode(
            '/',
            Arrays::filter(
                [
                    trim(Config::get('app.setting.url'), '/'),
                    Config::get('app.setting.front_end'),
                    $this->getModuleFullName(),
                    $this->getSceneName()
                ],
                'strlen',
                true
            )
        );
        
        return "{$base_api_url}{$this->getParametersString()}";
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 入力変数に新しい値を設定します。
     * 
     * @param String $variable 値を新しく設定する変数
     * @param mixed $new_value 変数に設定する値
     * 
     * @throws \InvalidArgumentException パラメータ $new_value が文字列型ではなかった場合
     */
    private function setStringValue(&$variable, $new_value)
    {
        if (String::isValid($new_value, true) === false) {
            throw new \InvalidArgumentException('Parameter only accepts string type.');
        }
        
        $variable = $new_value;
    }

    /**
     * APIバージョンを設定します。
     * 
     * @param Float $version APIバージョン番号
     * 
     * @throws \InvalidArgumentException パラメータ $version が符号なし小数点数ではなかった場合
     */
    private function setApiVersion($version)
    {
        $set_version = is_null($version) ? Config::get('sys.versioning.default_num', 1.0) : $version;
        
        if (Number::isValidUFloat($set_version) === false) {
            throw new \InvalidArgumentException('$version only accepts unsigned float type.');
        }
        
        $this->api_version = $set_version;
    }

    /**
     * モジュールのバージョン番号付きの完全修飾名を取得します。
     * 
     * @return String モジュールのバージョン番号付きの完全修飾名
     */
    private function getModuleFullName()
    {
        if (Config::get('sys.versioning.enabled', false)) {
            $api_version = str_replace(
                '.',
                Config::get('sys.versioning.separator', '.'),
                sprintf('%.1F', $this->api_version)
            );
            
            return "v{$api_version}/{$this->getModuleName()}";
        }
        
        return $this->getModuleName();
    }

    /**
     * モジュール名を取得します。
     * 
     * @return String モジュール名
     */
    private function getModuleName()
    {
        return isset($this->module_name) ? $this->module_name : Config::get('sys.default_module_name', 'top');
    }

    /**
     * シーン名を取得します。
     * 
     * @return String シーン名
     */
    private function getSceneName()
    {
        return isset($this->scene_name) ? $this->scene_name : Config::get('sys.default_scene_name', 'index');
    }

    /**
     * 文字列に変換したクエリパラメータ配列を取得します。
     * 
     * @return String 文字列に変換したクエリパラメータ配列
     */
    private function getParametersString()
    {
        $str_params = implode('&', $this->parseParameters());
        
        return empty($str_params) ? '' : "?{$str_params}";
    }

    /**
     * クエリパラメータ配列を解析します。
     * 
     * @return Array 解析した後のクエリパラメータ配列
     */
    private function parseParameters()
    {
        $params = [];
        
        Arrays::eachWalk(
            $this->parameters,
            function ($value, $key) use (&$params) {
                if (is_array($value)) {
                    $this->parseArrayParameter($params, $key, $value);
                } else {
                    Arrays::addWhen(true, $params, urlencode($key) . '=' . urlencode($value));
                }
            }
        );
        
        return $params;
    }

    /**
     * クエリパラメータに含まれる値のうち配列型パラメータを解析します。
     *
     * @param Array $list         解析中のクエリパラメータ配列
     * @param String|Integer $key 配列型パラメータのキー
     * @param Array $value        配列型パラメータの値
     */
    private function parseArrayParameter(&$list, $key, array $value)
    {
        Arrays::eachWalk(
            $value,
            function ($sub_value, $sub_key) use ($key, &$list) {
                Arrays::addWhen(
                    true,
                    $list,
                    urlencode($key) . '[' . urlencode($sub_key) . ']=' . urlencode($sub_value)
                );
            }
        );
    }
}
