<?php
namespace Phpingguo\System\Core;

use Phpingguo\ApricotLib\Common\String as CString;

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
    const PATH_CONFIG_DI = 'di_preset_services';
    
    // ---------------------------------------------------------------------------------------------
    // public static methods
    // ---------------------------------------------------------------------------------------------
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
            CString::unionDirectoryPath(static::getAppPath(), 'config'),
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
