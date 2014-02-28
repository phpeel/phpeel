<?php
namespace Phpingguo\System\Exts\Lib\DIWrapper;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Exception\ParseException;

/**
 * DIラッパークラスが使用するプリセットサービスの一覧を管理するクラスです。
 * 
 * @final [継承禁止クラス]
 * @package Phpingguo\System\Core
 * @author hiroki sugawara
 */
final class DIPresetServices
{
    // ---------------------------------------------------------------------------------------------
    // class fields
    // ---------------------------------------------------------------------------------------------
    public static $preset_services = [];
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * DIコンテナラッパーが使用するプリセットサービスの一覧を取得します。
     * 
     * @param Boolean $is_force_reset [初期値=false] 強制的に初期化するかどうか
     * 
     * @return Array DIコンテナラッパーが使用するプリセットサービスの一覧
     */
    public static function get($is_force_reset = false)
    {
        return static::getInitializedList($is_force_reset);
    }
    
    // ---------------------------------------------------------------------------------------------
    // private class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 初期化済みのDIコンテナラッパーが使用するプリセットサービスの一覧を取得します。
     * 
     * @param Boolean $is_force_reset 強制的に初期化するかどうか
     * 
     * @return Array DIコンテナラッパーが使用するプリセットサービスの一覧
     */
    private static function getInitializedList($is_force_reset)
    {
        if (static::isInitialized($is_force_reset)) {
            static::setPresetServices(static::getParsedServices());
        }
        
        return static::getPresetServices();
    }

    /**
     * プリセットサービスの一覧を初期化するかどうかを判定します。
     * 
     * @param Boolean $is_force_reset 強制的に初期化するかどうか
     * 
     * @return Boolean 初期化処理を実行する場合は true。それ以外の場合は false。
     */
    private static function isInitialized($is_force_reset)
    {
        return (empty(static::$preset_services) || $is_force_reset === true);
    }

    /**
     * プリセットサービスの一覧を設定します。
     * 
     * @param Array $service_list プリセットサービスとして登録するリスト
     */
    private static function setPresetServices(array $service_list)
    {
        static::$preset_services = $service_list;
    }

    /**
     * プリセットサービスの一覧を取得します。
     * 
     * @return Array プリセットサービスの一覧
     */
    private static function getPresetServices()
    {
        return static::$preset_services;
    }

    /**
     * プリセットサービスの一覧を定義しているファイルの解析結果を配列として取得します。
     * 
     * @return Array|null 解析成功時はプリセットサービスの配列。解析失敗時は null。
     */
    private static function getParsedServices()
    {
        $path  = realpath(__DIR__) . DIRECTORY_SEPARATOR . 'preset_services.yml';
        $value = (new Parser())->parse(file_get_contents($path));
        
        return is_array($value) ? $value : null;
    }
}
