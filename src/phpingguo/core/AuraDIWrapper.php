<?php
namespace Phpingguo\System\Core;

use Aura\Di\Config as DiConfig;
use Aura\Di\Container;
use Aura\Di\Forge;
use Phpingguo\System\Enums\Variable;

/**
 * AuraのDIコンテナをラップするクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class AuraDIWrapper
{
    // ---------------------------------------------------------------------------------------------
    // private class methods
    // ---------------------------------------------------------------------------------------------
    private static $aura_di     = null;
    private static $preset_list = [
        'Phpingguo\System\Request\Request',
        'Phpingguo\System\Request\RequestParser',
        Variable::INTEGER, Variable::UNSIGNED_INT,
        Variable::FLOAT, Variable::UNSIGNED_FLOAT,
        Variable::STRING, Variable::TEXT,
        'Phpingguo\System\Validator\Options',
        'Phpingguo\System\Filter\Pre\FilterHost',
        'Phpingguo\System\Filter\Post\FilterHost',
        'Phpingguo\System\Filter\Input\FilterHost',
        'Phpingguo\System\Filter\Output\FilterHost',
    ];
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * AuraDIWrapper クラスのインスタンスを初期化します。
     * 
     * @return \Aura\Di\Container 初期化したクラスが保持する DI コンテナのインスタンスを返します。
     */
    public static function init()
    {
        static::setContainer(
            static::registryServices(
                static::initContainer(static::$aura_di),
                static::getPresetServices()
            )
        );
        
        return static::getContainer();
    }
    
    // ---------------------------------------------------------------------------------------------
    // private class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * DI コンテナのインスタンスを取得します。
     * 
     * @return Container DI コンテナのインスタンスを返します。
     */
    private static function getContainer()
    {
        return static::$aura_di;
    }
    
    /**
     * DI コンテナのインスタンスを設定します。
     * 
     * @param Container $container	新しく設定する DI コンテナのインスタンス
     */
    private static function setContainer(Container $container)
    {
        static::$aura_di = $container;
    }
    
    /**
     * DI コンテナに登録するサービスのプリセットのリストを取得します。
     */
    private static function getPresetServices()
    {
        return static::$preset_list;
    }
    
    /**
     * DI コンテナのインスタンスを初期化します。
     * 
     * @param Container $container [初期値=null] サービスを登録する DI コンテナのインスタンス
     * @param Boolean $is_reset [初期値=false]   DI コンテナのインスタンスを再生成するかどうか
     * 
     * @return Container 初期化した DI コンテナのインスタンスを返します。
     */
    private static function initContainer($container = null, $is_reset = false)
    {
        empty($container) && $container = new Container(new Forge(new DiConfig()));
        
        return $container;
    }
    
    /**
     * DI コンテナにサービスを登録します。
     * 
     * @param Container $container                 サービスを登録する DI コンテナのインスタンス
     * @param Array $service_list [初期値=array()] 登録するサービスのリスト
     * 
     * @return Container サービス登録後の状態の DI コンテナのインスタンスを返します。
     */
    private static function registryServices(Container $container, array $service_list)
    {
        foreach ($service_list as $class) {
            $container->set($class, $container->lazyNew($class));
        }
        
        return $container;
    }
}
