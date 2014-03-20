<?php
namespace Phpingguo\System\Response;

use Phpingguo\ApricotLib\Common\String;
use Phpingguo\ApricotLib\Enums\Charset;
use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Enums\ContentType;
use Phpingguo\System\Enums\ResponseCode;
use Phpingguo\System\Enums\XFrameOptions;

/**
 * サーバーからクライアントへレスポンスするデータを保持するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Response
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $response_data = null;

    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * Response クラスの新しいインスタンスを初期化します。
     * 
     * @param ResponseCode|String $code [初期値=null] レスポンスデータの応答コード
     * @param ContentType|String $type [初期値=null]  レスポンスデータ内容の種類
     * @param Charset|String $charset [初期値=null]   レスポンスデータ内容の文字コード
     */
    public function __construct($code = null, $type = null, $charset = null)
    {
        $instance = Supervisor::getDiContainer(null)
            ->newInstance('Phpingguo\\System\\Response\\ResponseData', [ $code, $type, $charset ]);
        
        $this->setResponseData($instance);
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
        return $this->getResponseData()->getResponseCode();
    }

    /**
     * レスポンスデータ内容の種類を取得します。
     * 
     * @return ContentType レスポンスデータ内容の種類
     */
    public function getContentType()
    {
        return $this->getResponseData()->getContentType();
    }

    /**
     * レスポンスデータ内容の文字コードを取得します。
     * 
     * @return Charset レスポンスデータ内容の文字コード
     */
    public function getCharset()
    {
        return $this->getResponseData()->getCharset();
    }

    /**
     * レスポンスデータのインラインフレームオプションを取得します。
     * 
     * @return XFrameOptions レスポンスデータのインラインフレームオプション
     */
    public function getFrameOption()
    {
        return $this->getResponseData()->getFrameOption();
    }

    /**
     * リダイレクト先のURLアドレス文字列を取得します。
     * 
     * @return String リダイレクト先のURLアドレス文字列
     */
    public function getRedirectUrl()
    {
        return $this->getResponseData()->getRedirectUrl();
    }

    /**
     * レスポンスデータの応答コードを設定します。
     * 
     * @param ResponseCode|String $code レスポンスデータの新しい応答コード
     */
    public function setResponseCode($code)
    {
        $this->getResponseData()->setResponseCode($code);
    }

    /**
     * レスポンスデータ内容の種類を設定します。
     * 
     * @param ContentType|String $type レスポンスデータ内容の新しい種類
     */
    public function setContentType($type)
    {
        $this->getResponseData()->setContentType($type);
    }

    /**
     * レスポンスデータ内容の文字コードを設定します。
     * 
     * @param Charset|String $charset レスポンスデータ内容の新しい文字コード
     */
    public function setCharset($charset)
    {
        $this->getResponseData()->setCharset($charset);
    }

    /**
     * レスポンスデータのインラインフレームオプションを設定します。
     * 
     * @param XFrameOptions|String $option レスポンスデータの新しいインラインフレームオプション
     */
    public function setFrameOption($option)
    {
        $this->getResponseData()->setFrameOption($option);
    }

    /**
     * レスポンスデータの内容を設定します。
     *
     * @param String $content レスポンスデータの新しい内容
     */
    public function setContent($content)
    {
        $this->getResponseData()->setContent($content);
    }

    /**
     * リダイレクト先となるアプリケーションAPIを設定します。
     * 
     * @param AppApiUrl $api_url                アプリケーションのAPIとなるURLを表すインスタンス
     * @param Boolean $overwrite [初期値=false] 前回行った設定を上書きするかどうか
     */
    public function setRedirect(AppApiUrl $api_url, $overwrite = false)
    {
        $this->getResponseData()->setRedirect($api_url, $overwrite);
    }

    /**
     * リダイレクト先となる外部サイトのURLアドレスを設定します。
     * 
     * @param String $url                       リダイレクト先となる外部サイトのURLアドレス
     * @param Boolean $overwrite [初期値=false] 前回行った設定を上書きするかどうか
     */
    public function setRedirectUrl($url, $overwrite = false)
    {
        $this->getResponseData()->setRedirectUrl($url, $overwrite);
    }

    /**
     * 何らかのエラーが発生したことを設定します。
     * 
     * @param ResponseCode|String $code   エラーを示す応答コード
     * @param String $message [初期値=''] エラーページの内容
     */
    public function ariseError($code, $message = '')
    {
        $this->getResponseData()->ariseError($code, $message);
    }

    /**
     * リダイレクト先となるURLアドレスが設定されているかを調べます。
     * 
     * @return Boolean リダイレクト先が設定されている場合は true。それ以外の場合は false。
     */
    public function isRedirect()
    {
        return $this->getResponseData()->isRedirect();
    }

    /**
     * 何らかのエラーが発生しているかどうかを調べます。
     * 
     * @return Boolean 何らかのエラーが発生している場合は true。それ以外の場合は false。
     */
    public function isError()
    {
        return $this->getResponseData()->isError();
    }

    /**
     * レスポンスデータをクライアントへ出力します。
     */
    public function output()
    {
        if ($this->getResponseData()->isRedirect()) {
            $this->outputRedirect();
        } else {
            $this->outputContent();
        }
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * クライアントへ送信するレスポンスデータを取得します。
     * 
     * @return ResponseData クライアントへ送信するレスポンスデータ
     */
    private function getResponseData()
    {
        return $this->response_data;
    }

    /**
     * クライアントへ送信するレスポンスデータを設定する。
     *
     * @param ResponseData $data クライアントへ送信する新しいレスポンスデータ
     */
    private function setResponseData(ResponseData $data)
    {
        $this->response_data = $data;
    }

    /**
     * リダイレクトヘッダを出力します。
     */
    private function outputRedirect()
    {
        $redirect_codes = [
            ResponseCode::MOVED_PERMANENTLY,
            ResponseCode::FOUND,
            ResponseCode::SEE_OTHER,
            ResponseCode::NOT_MODIFIED,
            ResponseCode::USE_PROXY,
            ResponseCode::TEMPORARY_REDIRECT
        ];
        
        if (String::isContains($this->getResponseCode(), $redirect_codes) === false) {
            $this->setResponseCode(ResponseCode::MOVED_PERMANENTLY);
        }
        
        $this->setHeader($this->getResponseCode());
        $this->setHeader("Location: {$this->getRedirectUrl()}");
    }

    /**
     * レスポンスデータを出力します。
     */
    private function outputContent()
    {
        $this->setHeader($this->getResponseCode());
        $this->setHeader('Pragma: no-cache');
        $this->setHeader('Cache-Control: no-cache');
        $this->setHeader('Expires: ' . date('r'));
        $this->setHeader("Content-Type: {$this->getContentType()}; charset={$this->getCharset()}");
        $this->setHeader('X-Frame-Options: deny');
        
        $this->setHeaderRegisterCallback();
        
        $content = $this->getResponseData()->getContent();
        
        if (empty($content) === false) {
            $this->setHeader('Content-MD5: ' . base64_encode(md5($content, true)));
            echo $content;
        }
    }

    /**
     * レスポンスヘッダを生成します。
     *
     * @param String $string                 ヘッダ文字列
     * @param Boolean $replace [初期値=true] 以前設定した同じ値を上書きするかどうか
     */
    private function setHeader($string, $replace = true)
    {
        (PHP_SAPI !== 'cli') && header($string, $replace);
    }

    /**
     * ヘッダー送信時に実行されるコールバックを設定します。
     * 
     * @codeCoverageIgnore
     */
    private function setHeaderRegisterCallback()
    {
        header_register_callback(
            function () {
                header_remove('X-Powered-By');
            }
        );
    }
}
