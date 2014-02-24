<?php
namespace Phpingguo\System\Core;

use Phpingguo\System\Core\Config;
use Phpingguo\System\Enums\HttpMethod;
use Phpingguo\System\Exts\Lib\Common\Arrays;
use Phpingguo\System\Exts\Lib\Common\String as CString;

/**
 * クライアント情報を保持するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Client
{
    // ---------------------------------------------------------------------------------------------
    // class fields
    // ---------------------------------------------------------------------------------------------
    private static $request_vals = [];
    private static $get_vals     = [];
    private static $post_vals    = [];
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアント情報をキャプチャーします。
     * 
     * @return Boolean クライアント情報をキャプチャできた場合は true を、それ以外の場合は false を返します。
     */
    public static function capture()
    {
        $result = static::initValues();
        static::invalidSuperGlovalVariables();
        
        return (bool)$result;
    }
    
    /**
     * クライアントがリクエストした HTTP メソッドに合致するパラメータ配列を取得します。
     * 
     * @param HttpMethod|String $method クライアントがリクエストした HTTP メソッド
     * 
     * @return Array クライアントがリクエストした HTTP メソッドに合致するパラメータ配列を返します。
     */
    public static function getParameters($method)
    {
        $method_value = HttpMethod::init($method)->getValue();
        
        return static::isRequestVariable($method_value) ?
            static::getRequestParams() : static::getGetPostParams($method_value);
    }
    
    // ---------------------------------------------------------------------------------------------
    // private class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアントに関するスーパーグローバル変数のコピーを初期化します。
     * 
     * @return Boolean コピーできた場合は true を、それ以外の場合は false を返します。
     */
    private static function initValues()
    {
        $result = Arrays::copyWhen(Arrays::isValid($_REQUEST), static::$request_vals, $_REQUEST);
        $result |= Arrays::copyWhen(Arrays::isValid($_GET), static::$get_vals, $_GET);
        $result |= Arrays::copyWhen(Arrays::isValid($_POST), static::$post_vals, $_POST);
        
        return $result;
    }
    
    /**
     * クライアントに関するスーパーグローバル変数を無効化します。
     */
    private static function invalidSuperGlovalVariables()
    {
        Arrays::clear($_REQUEST);
        Arrays::clear($_GET);
        Arrays::clear($_POST);
    }
    
    /**
     * REQUEST のパラメータ配列を使用するかどうかを調べます。
     * 
     * @param String $method_value クライアントがリクエストした HTTP メソッドの名前
     * 
     * @return REQUEST のパラメータ配列を使用する場合は true を、それ以外の場合は false を返します。
     */
    private static function isRequestVariable($method_value)
    {
        return (Config::get('sys.security.use_requests', true) ||
            CString::isContains($method_value, [ HttpMethod::GET, HttpMethod::POST ]) === false);
    }
    
    /**
     * REQUEST のパラメータ配列を取得します。
     * 
     * @return Array REQUEST のパラメータ配列を返します。
     */
    private static function getRequestParams()
    {
        return static::$request_vals ?: [];
    }
    
    /**
     * GET または POST のパラメータ配列を取得します。
     * 
     * @param String $method_value クライアントがリクエストした HTTP メソッドの名前
     * 
     * @return Array GET または POST のパラメータ配列を返します。
     */
    private static function getGetPostParams($method_value)
    {
        return (($method_value === HttpMethod::GET) ? static::$get_vals : static::$post_vals) ?: [];
    }
}
