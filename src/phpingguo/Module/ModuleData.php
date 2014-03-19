<?php
namespace Phpingguo\System\Module;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\String;
use Phpingguo\ApricotLib\Type\Enum\Enum;
use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Enums\TemplateEngine;
use Phpingguo\System\Request\Request;
use Phpingguo\System\Response\Response;

/**
 * フレームワークが管理するモジュールのデータを表すクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ModuleData
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $module_name  = null;
    private $scene_name   = null;
    private $variables    = [];
    private $obj_request  = null;
    private $obj_response = null;
    private $engine_name  = null;

    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * ModuleData クラスの新しいインスタンスを初期化します。
     * 
     * @param Request $request [初期値=null]              リクエストデータを管理するクラスのインスタンス
     * @param TemplateEngine|String $engine [初期値=null] レスポンスデータの内容を生成するエンジンの名前
     */
    public function __construct(Request $request = null, $engine = null)
    {
        $this->setRequest($request);
        $this->setResponse(new Response());
        $this->setVariable('response', $this->getResponse());
        $this->setEngine($engine);
    }

     // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアントへ出力するモジュールの名前を取得します。
     * 
     * @return String クライアントへ出力するモジュールの名前
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * クライアントへ出力するシーンの名前を取得します。
     * 
     * @return String クライアントへ出力するシーンの名前
     */
    public function getSceneName()
    {
        return $this->scene_name;
    }

    /**
     * シーンのバインド変数の一覧を取得します。
     * 
     * @return Array シーンのバインド変数の一覧
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * シーンのバインド変数のうち指定したキーに該当する値を取得します。
     * 
     * @param Integer|String $key 値を取得するキー名またはインデックス番号
     * 
     * @return mixed 取得成功時は指定したキーに該当する値。それ以外の時は null。
     */
    public function getVariable($key)
    {
        return Arrays::getValue($this->variables, $key);
    }

    /**
     * リクエストデータを管理するクラスのインスタンスを取得します。
     * 
     * @return Request リクエストデータを管理するクラスのインスタンス
     */
    public function getRequest()
    {
        return $this->obj_request;
    }

    /**
     * レスポンスデータを管理するクラスのインスタンスを取得します。
     * 
     * @return Response レスポンスデータを管理するクラスのインスタンス
     */
    public function getResponse()
    {
        return $this->obj_response;
    }

    /**
     * レスポンスデータの内容を生成するエンジンの名前を取得します。
     * 
     * @return TemplateEngine レスポンスデータの内容を生成するエンジンの名前
     */
    public function getEngine()
    {
        return $this->engine_name;
    }

    /**
     * クライアントへ出力するモジュールの名前を設定します。
     * 
     * @param String $module_name クライアントへ出力するモジュールの新しい名前
     */
    public function setModuleName($module_name)
    {
        $this->setStringValue($this->module_name, $module_name);
    }

    /**
     * クライアントへ出力するシーンの名前を設定します。
     * 
     * @param String $scene_name クライアントへ出力するシーンの新しい名前
     */
    public function setSceneName($scene_name)
    {
        $this->setStringValue($this->scene_name, $scene_name);
    }

    /**
     * シーンのバインド変数の一覧を設定します。
     * 
     * @param Array $value シーンのバインド変数の新しい一覧
     */
    public function setVariables(array $value)
    {
        Arrays::copyWhen(Arrays::isValid($value), $this->variables, $value);
    }

    /**
     * シーンのバインド変数の一覧に指定したキーとそれに紐付く値を設定します。
     * 
     * @param String $key  新しく追加するキーの名前
     * @param mixed $value キーに紐づく値
     */
    public function setVariable($key, $value)
    {
        Arrays::addWhen(String::isValid($key, true), $this->variables, $value, $key);
    }

    /**
     * リクエストデータを管理するクラスのインスタンスを設定します。
     * 
     * @param Request $request リクエストデータを管理するクラスの新しいインスタンス
     */
    public function setRequest(Request $request = null)
    {
        (is_null($request) === false) && $this->obj_request = $request;
    }

    /**
     * レスポンスデータを管理するクラスのインスタンスを設定します。
     * 
     * @param Response $response レスポンスデータを管理するクラスの新しいインスタンス
     */
    public function setResponse(Response $response = null)
    {
        (is_null($response) === false) && $this->obj_response = $response;
    }

    /**
     * レスポンスデータの内容を生成するエンジンの名前を設定します。
     * 
     * @param TemplateEngine|String $engine レスポンスデータの内容を生成するエンジンの新しい名前
     */
    public function setEngine($engine)
    {
        $this->setEnumValue(
            $this->engine_name,
            Supervisor::getEnumFullName(Supervisor::ENUM_TEMPLATE_ENGINE),
            $engine,
            TemplateEngine::TWIG
        );
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 文字列型の変数へ新しい値を設定します。
     * 
     * @param String $variable  新しい値を設定する文字列型の変数
     * @param String $new_value 新しく設定する値
     * 
     * @throws \InvalidArgumentException パラメータ $new_value が文字列型ではなかった場合
     */
    private function setStringValue(&$variable, $new_value)
    {
        $is_not_null_value = (is_null($new_value) === false);
        
        if ($is_not_null_value === true && String::isValid($new_value, true) === false) {
            throw new \InvalidArgumentException('$new_value only accepts string type.');
        }
        
        ($is_not_null_value === true) && $variable = $new_value;
    }

    /**
     * 列挙型の変数へ新しい値を設定します。
     * 
     * @param Enum $variable    新しい値を設定する列挙型の変数
     * @param String $enum_name 列挙型変数の完全装飾クラス名
     * @param String $set_value 新しく設定する値
     * @param String $default   新しく設定する値が存在しない場合の代用値
     * 
     * @throws \InvalidArgumentException パラメータ $set_value が文字列型ではなかった場合
     */
    private function setEnumValue(&$variable, $enum_name, $set_value, $default)
    {
        $is_not_null_value = (is_null($set_value) === false);
        
        if ($is_not_null_value === true && String::isValid($set_value, true) === false) {
            throw new \InvalidArgumentException('$set_value only accepts string type.');
        }
        
        /** @var Enum $enum_name */
        $variable = $enum_name::init(
            $set_value,
            $default,
            function () use ($is_not_null_value) {
                return $is_not_null_value;
            }
        );
    }
}
