<?php
namespace Phpingguo\System\Variable;

use Phpingguo\ApricotLib\Common\Arrays;

/**
 * サーバー情報を保持するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Server
{
    // ---------------------------------------------------------------------------------------------
    // const
    // ---------------------------------------------------------------------------------------------
    const SRV_ENV_NAME    = 'SERVER_ENVIRONMENT_NAME';
    const REQUEST_METHOD  = 'REQUEST_METHOD';
    const REMOTE_ADDR     = 'REMOTE_ADDR';
    const HTTP_HOST       = 'HTTP_HOST';
    const PATH_INFO       = 'PATH_INFO';
    const REQUEST_URI     = 'REQUEST_URI';
    const HTTP_REFERER    = 'HTTP_REFERER';
    const HTTP_USER_AGENT = 'HTTP_USER_AGENT';
    const AUTH_TYPE       = 'AUTH_TYPE';
    const PHP_AUTH_DIGEST = 'PHP_AUTH_DIGEST';
    const PHP_AUTH_USER   = 'PHP_AUTH_USER';
    const PHP_AUTH_PW     = 'PHP_AUTH_PW';
    
    // ---------------------------------------------------------------------------------------------
    // class fields
    // ---------------------------------------------------------------------------------------------
    private static $server_list = [];
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * サーバー情報をキャプチャーします。
     * 
     * @return Boolean サーバー情報をキャプチャできた場合は true。それ以外の場合は false。
     */
    public static function capture()
    {
        $result = Arrays::copyWhen(empty($_SERVER) === false, static::$server_list, $_SERVER);
        Arrays::clear($_SERVER);
        
        return $result;
    }
    
    /**
     * サーバー情報から指定した名前に該当する値を取得します。
     * 
     * @param String $name 取得する情報の名前
     * @param Array $args  値が存在しなかった場合のデフォルト値
     * 
     * @return String サーバー情報から指定した名前に該当する値
     */
    public static function __callStatic($name, $args)
    {
        /** @var Server $class */
        $class = get_called_class();
        
        if (false === ($const = (new \ReflectionClass($class))->getConstant($name))) {
            return null;
        }
        
        return $class::getValue($const, Arrays::isValid($args) ? $args[0] : null);
    }
    
    /**
     * サーバー情報から指定した名前に該当する値を取得します。
     * 
     * @param String $name                 取得する情報の名前
     * @param mixed $default [初期値=null] 値が存在しなかった場合のデフォルト値
     * 
     * @return String サーバー情報から指定した名前に該当する値
     */
    public static function getValue($name, $default = null)
    {
        return Arrays::getValue(static::$server_list, $name, $default);
    }
}
