<?php
namespace Phpingguo\System\Core;

/**
 * アプリケーションの設定を保持するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Config
{
    // ---------------------------------------------------------------------------------------------
    // class fields
    // ---------------------------------------------------------------------------------------------
    private static $configs = [];

    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * アプリケーションの設定を初期化します。
     *
     * @param Array $list [初期値=array()] 初期設定リスト
     */
    public static function init(array $list = [])
    {
        static::$configs = $list;
    }
    
    /**
     * アプリケーションの設定リストから指定したキーに紐づけられている値を取得します。
     * 
     * @param String $key                        値を取得するキーの名前
     * @param mixed $default_value [初期値=null] 値が存在しなかった場合のデフォルト値
     * 
     * @return mixed アプリケーションの設定リストの中で指定したキーに紐づけられている値
     */
    public static function get($key, $default_value = null)
    {
        $config = static::$configs;
        $result = $default_value;
        $keys   = explode('.', $key);
        
        foreach ($keys as $key_value) {
            if (isset($config[$key_value]) === false) {
                $result = $default_value;
                
                break;
            }
            
            $temp   = $config[$key_value];
            $result = $temp;
            $config = $temp;
        }
        
        return $result;
    }
    
    /**
     * アプリケーションの設定リストにある全てのキーの値を取得します。
     * 
     * @return Array アプリケーションの設定にある全てのキーの値
     */
    public static function getAll()
    {
        return static::$configs ?: [];
    }
    
    /**
     * アプリケーションの設定リストに指定したキーとそれに紐づく値を設定します。
     * 
     * @param String $key  値を設定するキーの名前
     * @param mixed $value キーに紐づけられる値
     */
    public static function set($key, $value)
    {
        $keys = array_reverse(explode('.', $key));
        $list = null;
        
        foreach ($keys as $list_key) {
            $list = is_null($list) ? [ $list_key => $value ] : [ $list_key => $list ];
        }
        
        if (empty($list) === false) {
            static::$configs = array_replace_recursive(static::$configs, $list);
        }
    }
}
