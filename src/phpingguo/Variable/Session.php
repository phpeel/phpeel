<?php
namespace Phpingguo\System\Variable;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\ApricotLib\Common\String;
use Phpingguo\System\Core\Supervisor;

/**
 * セッションデータを管理するクラスです。
 * 
 * @codeCoverageIgnore
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Session
{
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * Session クラスの新しいインスタンスを初期化します。
     * 
     * @throws \RuntimeException 実行するサーバーでセッションモジュールが有効になっていない場合
     */
    public function __construct()
    {
        if (get_loaded_extensions('session') === false) {
            throw new \RuntimeException('The session function of this web server is invalid.');
        }
    }

    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * Session クラスのインスタンスを取得します。
     * 
     * @return Session 初回呼び出し時は新規生成したインスタンス。それ以降の時は生成済みのインスタンス。
     */
    public static function getInstance()
    {
        return Supervisor::getDiContainer(Supervisor::DIS_SYSTEM)->get(get_called_class());
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * セッション設定の初期化を行います。
     *
     * @param String|\SessionHandlerInterface $save_handler [初期値=null] セッションハンドラ
     * @param String|Array $save_path [初期値=null]                       セッションの保存先パス
     *
     * @throws \RuntimeException セッションが既に開始されていた場合
     * @throws \LogicException セッションの保存ハンドラあるいは保存パスの初期化に失敗した場合
     */
    public function initialize($save_handler = null, $save_path = null)
    {
        if ($this->isSessionStatus(PHP_SESSION_ACTIVE)) {
            throw new \RuntimeException('The session has been already begun.');
        } elseif ($this->initSaveHandler($save_handler, $save_path) === false) {
            throw new \LogicException('Session handler failed initialization.');
        }
    }

    /**
     * セッションを開始します。
     *
     * @param Boolean $is_regenerate_id [初期値=false] セッションIDを再生成するかどうか
     */
    public function open($is_regenerate_id = false)
    {
        if ($is_regenerate_id === true) {
            $this->regenerateId();
        } else {
            $this->start();
        }
    }

    /**
     * セッションを終了します。
     */
    public function close()
    {
        if ($this->isSessionStatus(PHP_SESSION_ACTIVE)) {
            $this->destroy();
        }
    }

    /**
     * 指定したキーの名前に紐付く値がセッションデータに存在するかどうかを調べます。
     * 
     * @param String|Integer $key 存在の有無を調べるキーの名前
     *
     * @return Boolean 存在する場合は true。それ以外の場合は false。
     */
    public function isExist($key)
    {
        return Arrays::isExist($this->getSessionData(), $key);
    }

    /**
     * セッションデータから指定したキーに該当する値を取得します。
     * 
     * @param String|Integer $key 値を取得するキーの名前
     *
     * @return mixed 指定したキーに該当する値。キーが存在しない場合は null。
     */
    public function get($key)
    {
        return Arrays::findValue($this->getSessionData(), $key);
    }

    /**
     * セッションデータの項目一覧を取得します。
     * 
     * @return Array セッションデータの項目一覧
     */
    public function getAll()
    {
        return $this->getSessionData();
    }

    /**
     * セッションデータに指定したキーに紐付く値を設定します。
     * 
     * @param String|Integer $key 値を設定するキーの名前
     * @param mixed $value        キーに新しく紐付ける値
     *
     * @return Boolean 設定に成功した場合は true。それ以外の場合は false。
     */
    public function set($key, $value)
    {
        return is_array($this->getSessionData()) &&
            Arrays::addWhen(Arrays::isValidKey($key), $_SESSION, $value, $key);
    }

    /**
     * セッションデータから指定したキーとその値を削除します。
     * 
     * @param String|Integer $key 削除するキーの名前
     *
     * @return Boolean 削除に成功した場合は true。それ以外の場合は false。
     */
    public function remove($key)
    {
        return Arrays::removeWhen(is_array($this->getSessionData()), $_SESSION, $key);
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * セッションデータの保存ハンドラと保存パスを初期化します。
     *
     * @param String|\SessionHandlerInterface $save_handler セッション保存ハンドラ
     * @param String|Array $save_path                       セッション保存先パス、またはその配列
     *
     * @return Boolean 初期化処理が成功またはスキップの場合は true。それ以外の場合は false。
     */
    private function initSaveHandler($save_handler, $save_path)
    {
        $path_result    = is_null($save_path) ? null : $this->setSavePath($save_path);
        $handler_result = is_null($save_handler) ? null : $this->setSaveHandler($save_handler);
        
        return ($path_result !== false && $handler_result !== false);
    }

    /**
     * セッションデータの保存ハンドラを設定します。
     * 
     * @param String|\SessionHandlerInterface $save_handler セッションの保存で使用するハンドラ
     * 
     * @return Boolean|null 設定成功時は true。失敗時は false。未設定時は null。
     */
    private function setSaveHandler($save_handler)
    {
        if ($save_handler instanceof \SessionHandlerInterface) {
            /** @noinspection PhpParamsInspection */
            return session_set_save_handler($save_handler, false);
        } elseif (String::isValid($save_handler)) {
            return (ini_set('session.save_handler', $save_handler) !== false);
        }
        
        return false;
    }

    /**
     * セッションデータの保存先パスを設定します。
     * 
     * @param String|Array $save_path セッションの保存先のパス、またはそれらからなる配列
     *
     * @return Boolean|null 設定成功時は true。失敗時は false。未設定時は null。
     */
    private function setSavePath($save_path)
    {
        $set_save_path = Arrays::isValid($save_path) ? implode(',', $save_path) : $save_path;
        
        if (String::isValid($set_save_path)) {
            return (ini_set('session.save_path', $set_save_path) !== false);
        }
        
        return false;
    }

    /**
     * セッションデータを取得します。
     * 
     * @throws \RuntimeException セッションが開始されていない場合
     * 
     * @return Array セッションデータ
     */
    private function getSessionData()
    {
        if ($this->isSessionStatus(PHP_SESSION_NONE)) {
            throw new \RuntimeException('The session of this web server had already closed.');
        }
        
        return $_SESSION;
    }

    /**
     * コマンドラインモードで実行しているかどうかを調べます。
     * 
     * @return Boolean コマンドラインモードで実行している場合は true。それ以外の場合は false。
     */
    private function isCliExecuted()
    {
        return (PHP_SAPI === 'cli');
    }

    /**
     * セッションデータの現在の状況を調べます。
     * 
     * @param Integer $expected 期待する現在の状況
     *
     * @return Boolean 現在の状況と期待値が一致する場合は true。それ以外の場合は false。
     */
    private function isSessionStatus($expected)
    {
        return ($this->isCliExecuted() === false && session_status() === $expected);
    }

    /**
     * セッションを開始します。
     *
     * @throws \RuntimeException セッションが既に開始されていた場合
     */
    private function start()
    {
        if ($this->isSessionStatus(PHP_SESSION_ACTIVE)) {
            throw new \RuntimeException('The session has been already begun.');
        }

        $_SESSION = [];
        ($this->isCliExecuted() === false) && session_start();
    }

    /**
     * セッションを破棄します。
     */
    private function destroy()
    {
        // [Tips] "Session object destruction failed" の警告エラーが発生する時の対処法
        // ・外部ハンドラを使用中の場合
        //   利用する外部サーバーが正常稼働しているかどうかを確認すること
        // ・標準ハンドラを使用中の場合
        //   セッションファイル生成先ディレクトリが書き込み可能なパーミッションかどうかを確認すること
        Arrays::clear($_SESSION);
        Arrays::isContain($_SESSION, session_name()) && $this->setCookie(session_name(), '', time() - 42000);
        ($this->isCliExecuted() === false) && session_destroy();
    }

    /**
     * セッションが開始していなければ開始し、セッションIDを再生成します。
     */
    private function regenerateId()
    {
        $this->isSessionStatus(PHP_SESSION_NONE) && $this->start();
        ($this->isCliExecuted() === false) && session_regenerate_id(true);
    }

    /**
     * クッキーデータへ指定したキーとそれに紐付く値を設定します。
     * 
     * @param String $name               値を設定するキーの名前
     * @param mixed $value               新しくキーに紐づけられる値
     * @param Integer $expire [初期値=0] キーの保存有効期限
     * @param String $path [初期値='/']  キーを保存するファイルパス
     */
    private function setCookie($name, $value, $expire = 0, $path = '/')
    {
        ($this->isCliExecuted() === false) && setcookie($name, $value, $expire, $path);
    }
}
