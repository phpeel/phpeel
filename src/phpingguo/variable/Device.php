<?php
namespace Phpingguo\System\Variable;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\General;
use Phpingguo\System\Core\Supervisor;
use Symfony\Component\Yaml\Parser;

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
        // proxy server の ip address を取り出す（配列の上にある変数名ほど優先順位高）
        $proxy_client_ip = Arrays::getValue(array_values(array_filter([
            Server::getValue('HTTP_CLIENT_IP', false),
            Server::getValue('HTTP_X_FORWARDED_FOR', false),
            Server::getValue('HTTP_CLIENTADDRESS', false),
            Server::getValue('HTTP_X_REAL_IP', false),
            Server::getValue('HTTP_X_REAL_FORWARDED_FOR', false)
        ])), 0, false);
        
        // デフォルトでは、proxy server の ip address は使えないようにしている
        if ($ignore_proxy === false &&
            filter_var($proxy_client_ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6) !== false
        ) {
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
        $ua_patterns = static::getAllowUserAgents();
        
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
    
    // ---------------------------------------------------------------------------------------------
    // private class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * アプリケーションで許可するユーザーエージェントの一覧を取得します。
     * 
     * @return Array アプリケーションで許可されているユーザーエージェントの一覧
     */
    private static function getAllowUserAgents()
    {
        if (empty(static::$allow_user_agents) === true) {
            static::$allow_user_agents = static::loadAllowUserAgents();
        }
        
        return static::$allow_user_agents;
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
