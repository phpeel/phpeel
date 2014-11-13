<?php
namespace Phpeel\System\Response;

use Phpeel\ApricotLib\Common\String;
use Phpeel\ApricotLib\Enums\Charset;
use Phpeel\ApricotLib\LibSupervisor;
use Phpeel\ApricotLib\Type\Enum\Enum;
use Phpeel\System\Core\Config;
use Phpeel\System\Core\Supervisor;
use Phpeel\System\Enums\ContentType;
use Phpeel\System\Enums\ResponseCode;
use Phpeel\System\Enums\XFrameOptions;

/**
 * サーバーからクライアントへ送信するレスポンスデータを表すクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ResponseData
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $response_code    = null;
    private $content_type     = null;
    private $response_charset = null;
    private $frame_option     = null;
    private $response_content = null;
    private $is_redirect      = false;
    private $redirect_url     = '';
    private $is_error         = false;

    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * ResponseData クラスの新しいインスタンスを初期化します。
     * 
     * @param ResponseCode|String $code [初期値=null] レスポンスデータの応答コード
     * @param ContentType|String $type [初期値=null]  レスポンスデータ内容の種類
     * @param Charset|String $charset [初期値=null]   レスポンスデータ内容の文字コード
     */
    public function __construct($code = null, $type = null, $charset = null)
    {
        $this->setResponseCode($code);
        $this->setContentType($type);
        $this->setCharset($charset);
        $this->setFrameOption(Config::get('sys.security.default_x_frame_options'));
    }

     // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * レスポンスデータの応答コードを取得します。
     * 
     * @return ResponseCode レスポンスデータの応答コード
     */
    public function getResponseCode()
    {
        return $this->response_code;
    }

    /**
     * レスポンスデータ内容の種類を取得します。
     * 
     * @return ContentType レスポンスデータ内容の種類
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * レスポンスデータ内容の文字コードを取得します。
     * 
     * @return Charset レスポンスデータ内容の文字コード
     */
    public function getCharset()
    {
        return $this->response_charset;
    }

    /**
     * レスポンスデータのインラインフレームオプションを取得します。
     * 
     * @return XFrameOptions レスポンスデータのインラインフレームオプション
     */
    public function getFrameOption()
    {
        return $this->frame_option;
    }

    /**
     * レスポンスデータの内容を取得します。
     * 
     * @return String レスポンスデータの内容
     */
    public function getContent()
    {
        return $this->response_content;
    }

    /**
     * リダイレクト先のURLアドレス文字列を取得します。
     * 
     * @throws \RuntimeException リダイレクト先のURLアドレスが設定されていない場合
     * 
     * @return String リダイレクト先のURLアドレス文字列
     */
    public function getRedirectUrl()
    {
        if ($this->isRedirect() === false) {
            throw new \RuntimeException('Redirect url address is not set.');
        }
        
        return $this->redirect_url;
    }

    /**
     * レスポンスデータの応答コードを設定します。
     *
     * @param ResponseCode|String $code レスポンスデータの新しい応答コード
     */
    public function setResponseCode($code)
    {
        $this->setValue(
            $this->response_code,
            Supervisor::getEnumFullName(Supervisor::ENUM_RESPONSE_CODE),
            $code,
            ResponseCode::OK
        );
    }

    /**
     * レスポンスデータ内容の種類を設定します。
     * 
     * @param ContentType|String $type レスポンスデータ内容の新しい種類
     */
    public function setContentType($type)
    {
        $this->setValue(
            $this->content_type,
            Supervisor::getEnumFullName(Supervisor::ENUM_CONTENT_TYPE),
            $type,
            ContentType::HTML
        );
    }

    /**
     * レスポンスデータ内容の文字コードを設定します。
     *
     * @param Charset|String $charset レスポンスデータ内容の新しい文字コード
     */
    public function setCharset($charset)
    {
        $this->setValue(
            $this->response_charset,
            LibSupervisor::getEnumFullName(LibSupervisor::ENUM_CHARSET),
            $charset,
            Charset::UTF8
        );
    }

    /**
     * レスポンスデータのインラインフレームオプションを設定します。
     * 
     * @param XFrameOptions|String $option レスポンスデータの新しいインラインフレームオプション
     */
    public function setFrameOption($option)
    {
        $this->setValue(
            $this->frame_option,
            Supervisor::getEnumFullName(Supervisor::ENUM_X_FRAME_OPTIONS),
            $option,
            XFrameOptions::DENY
        );
    }

    /**
     * レスポンスデータの内容を設定します。
     * 
     * @param String $content レスポンスデータの新しい内容
     */
    public function setContent($content)
    {
        if (String::isValid($content) && $this->isError() === false) {
            $this->response_content = $content;
        }
    }

    /**
     * リダイレクト先となるアプリケーションAPIを設定します。
     * 
     * @param AppApiUrl $api_url                アプリケーションのAPIとなるURLを表すインスタンス
     * @param Boolean $overwrite [初期値=false] 前回行った設定を上書きするかどうか
     */
    public function setRedirect(AppApiUrl $api_url, $overwrite = false)
    {
        $this->setRedirectUrlString($api_url->createUrl(), $overwrite);
    }

    /**
     * リダイレクト先となる外部サイトのURLアドレスを設定します。
     * 
     * @param String $url                       リダイレクト先となる外部サイトのURLアドレス
     * @param Boolean $overwrite [初期値=false] 前回行った設定を上書きするかどうか
     * 
     * @throws \RuntimeException         パラメータ $url が文字列型ではなかった場合
     * @throws \InvalidArgumentException リダイレクト先として許可されていないホストを持つURLアドレスを指定した場合
     */
    public function setRedirectUrl($url, $overwrite = false)
    {
        if (String::isValid($url, true) === false) {
            throw new \InvalidArgumentException('$url only accepts string type.');
        } elseif ($this->isNotAllowedHost($url)) {
            throw new \RuntimeException('$url is not allowed host.');
        }
        
        $this->setRedirectUrlString($url, $overwrite);
    }

    /**
     * 何らかのエラーが発生したことを設定します。
     * 
     * @param ResponseCode|String $code   エラーを示す応答コード
     * @param String $message [初期値=''] エラーページの内容
     */
    public function ariseError($code, $message = '')
    {
        $this->setResponseCode($code);
        $this->setContent($message);
        $this->setRedirectStatus(false);
        $this->setErrorStatus(true);
    }

    /**
     * リダイレクト先となるURLアドレスが設定されているかを調べます。
     * 
     * @return Boolean リダイレクト先が設定されている場合は true。それ以外の場合は false。
     */
    public function isRedirect()
    {
        return $this->is_redirect;
    }

    /**
     * 何らかのエラーが発生しているかどうかを調べます。
     * 
     * @return Boolean 何らかのエラーが発生している場合は true。それ以外の場合は false。
     */
    public function isError()
    {
        return $this->is_error;
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 入力変数に列挙型の値を新しく設定します。
     * 
     * @param mixed $variable   列挙型の値を新しく設定する入力変数
     * @param String $enum_name 設定値が所属する列挙型の名前
     * @param String $set_value 設定値
     * @param mixed $default    設定値が存在しない場合に代用する値
     * 
     * @throws \InvalidArgumentException パラメータ $set_value が null または文字列型ではなかった場合
     */
    private function setValue(&$variable, $enum_name, $set_value, $default)
    {
        $is_not_null_value = (is_null($set_value) === false);
        
        if ($is_not_null_value === true && String::isValid($set_value, true) === false) {
            throw new \InvalidArgumentException('$set_value only accepts string type.');
        }
        
        /** @var Enum $enum_name */
        $variable = $enum_name::init(
            $set_value,
            $default,
            function () use ($is_not_null_value) {
                return $is_not_null_value;
            }
        );
    }

    /**
     * リダイレクトの状態を新しく設定します。
     * 
     * @param Boolean $status リダイレクトの新しい状態
     */
    private function setRedirectStatus($status)
    {
        $this->is_redirect = ($status === true);
    }

    /**
     * リダイレクト先となるURLアドレス文字列を設定します。
     * 
     * @param String $url        リダイレクト先となるURLアドレス文字列
     * @param Boolean $overwrite 前回行った設定を上書きするかどうか
     * 
     * @throws \RuntimeException パラメータ $overwrite が false で既にリダイレクト設定が行われていた場合
     */
    private function setRedirectUrlString($url, $overwrite)
    {
        if ($this->isRedirect() && $overwrite === false) {
            throw new \RuntimeException('Redirect url address is already set.');
        }
        
        $this->redirect_url = $url;
        $this->setRedirectStatus(true);
    }

    /**
     * 入力されたURLアドレスのホストが許可されているかどうかを調べます。
     * 
     * @param String $url 許可されているホストかどうかを調べるURLアドレス
     * 
     * @return Boolean 許可されていないホストを持つ場合は true。それ以外の場合は false。
     */
    private function isNotAllowedHost($url)
    {
        $url_pattern = preg_quote(implode('|', Config::get('sys.security.allow_redirect_hosts', [])), '/');
        
        $is_string    = String::isValid($url_pattern);
        $is_ctrl_chr  = (preg_match('/(%0D|%0A)+/', urlencode($url)) === 1);
        $is_not_match = String::isNotRegexMatched(
            $url,
            '/^https?\:\/\/(' . $url_pattern .')[-_.!~*\'();\/?:@&=+$,%#a-z0-9]*$/i',
            1
        );
        
        return ($is_string === false || $is_ctrl_chr === true || $is_not_match === true);
    }

    /**
     * エラー発生の状態を設定します。
     * 
     * @param Boolean $status エラー発生の新しい状態
     */
    private function setErrorStatus($status)
    {
        $this->is_error = ($status === true);
    }
}
