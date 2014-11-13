<?php
namespace Phpeel\System\Variable;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\ApricotLib\Common\General;
use Phpeel\System\Core\Supervisor;

/**
 * クライアントのデバイス情報を管理するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Device
{
    // ---------------------------------------------------------------------------------------------
    // class fields
    // ---------------------------------------------------------------------------------------------
    private static $allow_user_agents = [];
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアントのデバイスのIPアドレスを取得します。
     *
     * @param Boolean $ignore_proxy [初期値=true] プロキシサーバー経由のIPアドレスを無視するかどうか
     * 
     * @return String|Boolean 取得成功時はクライアントのデバイスのIPアドレス。取得失敗時は false。
     */
    public static function getClientIp($ignore_proxy = true)
    {
        // 書式が正しい proxy server の ip address を取り出す（配列の上にある変数名ほど優先順位高）
        $proxy_client_ip = filter_var(
            Arrays::getValue(
                Arrays::filter(
                    [
                        Server::getValue('HTTP_CLIENT_IP', false),
                        Server::getValue('HTTP_X_FORWARDED_FOR', false),
                        Server::getValue('HTTP_CLIENTADDRESS', false),
                        Server::getValue('HTTP_X_REAL_IP', false),
                        Server::getValue('HTTP_X_REAL_FORWARDED_FOR', false)
                    ],
                    null,
                    true
                ),
                0,
                false
            ),
            FILTER_VALIDATE_IP,
            FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6
        );
        
        // デフォルトでは、proxy server の ip address は使えないようにしている
        if ($ignore_proxy === false && $proxy_client_ip !== false) {
            return $proxy_client_ip;
        }
        
        return Server::getValue(Server::REMOTE_ADDR, false);
    }

    /**
     * クライアントのデバイスの種類を取得します。
     * 
     * @return String クライアントのデバイスの種類
     */
    public static function getClientType()
    {
        $client_type = 'Other';
        $user_agent  = Server::getValue(Server::HTTP_USER_AGENT, '');
        $ua_patterns = static::getAllowUserAgents('Type');
        
        Arrays::eachWalk(
            $ua_patterns,
            function ($value, $key, $result) use ($user_agent, &$client_type) {
                if ($result !== 1 && 1 === ($result = preg_match($value, $user_agent))) {
                    $client_type = $key;
                }
                
                return $result;
            }
        );
        
        return $client_type;
    }

    /**
     * クライアントのデバイスのカテゴリを取得します。
     * 
     * @return String デバイスが属するカテゴリの名前。カテゴリ名が未定義の場合は Undefined の文字列。
     */
    public static function getClientCategory()
    {
        return Arrays::getValue(static::getAllowUserAgents('Category'), static::getClientType(), 'Undefined');
    }

    /**
     * クライアントのデバイスのバージョンを取得します。
     * 
     * @return String|Boolean|null 取得成功時はバージョンに関する文字列。
     * バージョンに関する情報を発見できなかった時は false。それ以外の時は null。
     */
    public static function getClientVersion()
    {
        $user_agent  = Server::getValue(Server::HTTP_USER_AGENT, '');
        $ver_pattern = Arrays::getValue(static::getAllowUserAgents('Version'), static::getClientType(), null);
        
        if (is_null($ver_pattern) || preg_match($ver_pattern, $user_agent, $matches) !== 1) {
            return null;
        }
        
        return Arrays::getValue($matches, 3, false);
    }
    
    // ---------------------------------------------------------------------------------------------
    // private class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 指定したカテゴリに該当する許可済みのユーザーエージェントの一覧を取得します。
     * 
     * @param String $category [初期値='Type'] 取得するリスト内容のカテゴリ名
     * 
     * @return Array 指定したカテゴリが存在する場合は該当する一覧の配列。それ以外は空配列。
     */
    private static function getAllowUserAgents($category)
    {
        if (empty(static::$allow_user_agents) === true) {
            static::$allow_user_agents = static::loadAllowUserAgents();
        }
        
        return Arrays::getValue(static::$allow_user_agents, $category, []);
    }

    /**
     * アプリケーションで許可するユーザーエージェント一覧の設定ファイルを読み込みます。
     * 
     * @return Array 読み込み成功時は許容するユーザーエージェントの配列。読み込み失敗時は空配列。 
     */
    private static function loadAllowUserAgents()
    {
        $value = General::getParsedYamlFile(
            Supervisor::getConfigPath(Supervisor::PATH_CONFIG_REGULATION),
            'allow_user_agents'
        );
        
        return is_array($value) ? $value : [];
    }
}
