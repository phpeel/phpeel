<?php
namespace Phpingguo\System\Core;

use Phpingguo\ApricotLib\Common\String as CString;
use Phpingguo\CitronDI\AuraDIWrapper;

/**
 * フレームワークを統括するクラスです。
 *
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Supervisor
{
    // ---------------------------------------------------------------------------------------------
    // const fields
    // ---------------------------------------------------------------------------------------------
    const PATH_CONFIG_DI         = 'di_preset_services';
    const PATH_CONFIG_REGULATION = 'various_regulations';

    // ---------------------------------------------------------------------------------------------
    // public static methods
    // ---------------------------------------------------------------------------------------------
    /**
     * フレームワークで使用可能なDIコンテナのインスタンスを取得します。
     *
     * @param String $service_group_name
     *
     * @return \Aura\Di\Container フレームワークで使用可能なDIコンテナのインスタンス
     */
    public static function getDiContainer($service_group_name)
    {
        return AuraDIWrapper::init(
            $service_group_name,
            static::getConfigPath(static::PATH_CONFIG_DI)
        );
    }

    /**
     * プロジェクトのルートディレクトリのファイルパスを取得します。
     *
     * @return String プロジェクトのルートディレクトリのファイルパス
     */
    public static function getProjectPath()
    {
        return CString::unionDirectoryPath(CString::unionDirectoryPath(__DIR__, '..'), '..');
    }

    /**
     * フレームワークのシステムディレクトリのファイルパスを取得します。
     *
     * @return String フレームワークのシステムディレクトリのファイルパス
     */
    public static function getSystemPath()
    {
        return CString::unionDirectoryPath(static::getProjectPath(), 'phpingguo');
    }

    /**
     * アプリケーションのルートディレクトリのファイルパスを取得します。
     *
     * @return String アプリケーションのルートディレクトリのファイルパス
     */
    public static function getAppPath()
    {
        return CString::unionDirectoryPath(static::getProjectPath(), 'app');
    }

    /**
     * プロジェクトの設定ファイルがあるディレクトリのファイルパスを取得します。
     *
     * @param String $sub_dir_name [初期値=null] 設定ファイルがあるサブディレクトリの名前
     *
     * @return String プロジェクトの設定ファイルがあるディレクトリのファイルパス
     */
    public static function getConfigPath($sub_dir_name = null)
    {
        return CString::unionDirectoryPath(
            CString::unionDirectoryPath(static::getProjectPath(), 'config'),
            $sub_dir_name
        );
    }

    /**
     * プロジェクトのキャッシュファイルがあるディレクトリのファイルパスを取得します。
     *
     * @param String $sub_dir_name [初期値=null] キャッシュファイルがあるサブディレクトリの名前
     *
     * @return String プロジェクトのキャッシュファイルがあるディレクトリのファイルパス
     */
    public static function getCachePath($sub_dir_name = null)
    {
        return CString::unionDirectoryPath(
            CString::unionDirectoryPath(static::getAppPath(), 'cache'),
            $sub_dir_name
        );
    }
}
