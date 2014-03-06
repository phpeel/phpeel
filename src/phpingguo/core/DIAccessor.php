<?php
namespace Phpingguo\System\Core;

use Phpingguo\CitronDI\AuraDIWrapper;

/**
 * フレームワークからDIコンテナへ共通にアクセスできるようにするためのクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class DIAccessor
{
    /**
     * フレームワークで使用可能なDIコンテナのインスタンスを取得します。
     * 
     * @param String $service_group_name 
     * 
     * @return \Aura\Di\Container フレームワークで使用可能なDIコンテナのインスタンス
     */
    public static function getContainer($service_group_name)
    {
        return AuraDIWrapper::init(
            $service_group_name,
            Supervisor::getConfigPath(Supervisor::PATH_CONFIG_DI)
        );
    }
}
