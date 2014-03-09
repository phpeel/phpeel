<?php
namespace Phpingguo\System\Variable;

use Phpingguo\ApricotLib\Common\Arrays;
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
        return Supervisor::getDiContainer('system')->get(get_called_class());
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * セッションを開始します。
     * 
     * @throws \RuntimeException セッションが既に開始されていた場合
     */
    public function open()
    {
        if ($this->isSessionStatus(PHP_SESSION_ACTIVE)) {
            throw new \RuntimeException('The session has been already begun.');
        }
        
        $this->init();
    }

    /**
     * セッションを終了します。
     */
    public function close()
    {
        if ($this->isSessionStatus(PHP_SESSION_ACTIVE)) {
            Arrays::clear($_SESSION);
            $this->destroy();
            
            if (Arrays::isExistKey($_COOKIE, 'ValidateUniqId')) {
                $this->setCookie('ValidateUniqId', '', time() - 42000);
            }
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
        return Arrays::isExistKey($this->getSessionData(), $key);
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
        return Arrays::addWhen($this->getSessionData() && Arrays::isValidKey($key), $_SESSION, $value, $key);
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
        return Arrays::removeWhen($this->getSessionData() != false, $_SESSION, $key);
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * セッションデータを初期化します。
     */
    private function init()
    {
        $old_session = [];
        
        // セッションデータを変数へ退避してリセットする
        $this->start();
        Arrays::copyWhen(true, $old_session, $_SESSION);
        Arrays::clear($_SESSION);
        $this->destroy();
        
        // 退避したセッションデータを使って新しいセッションデータを生成する
        Arrays::copyWhen(true, $_SESSION, $this->getNewSessionData($old_session));
    }

    /**
     * 新しいセッションデータを生成します。
     * 
     * @param Array $old_session 古いセッションデータ
     *
     * @return Array 生成した新しいセッションデータ
     */
    private function getNewSessionData(array $old_session)
    {
        $new_session = [];
        $unique_id   = $this->generateUniqueId();
        
        if ($this->compareUniqueId($old_session, $_COOKIE)) {
            $this->setId($unique_id);
            $this->start();
            Arrays::copyWhen(true, $new_session, $old_session);
        } else {
            $this->start();
            $this->regenerateId();
            Arrays::addWhen(true, $new_session, [ 'ValidateUniqId' => $unique_id ], '_SESSION_VALIDATION');
            $this->setCookie('ValidateUniqId', $unique_id);
        }
        
        return $new_session;
    }

    /**
     * セッションデータの正当性評価に使用するユニークIDを生成します。
     * 
     * @return String 生成したユニークID
     */
    private function generateUniqueId()
    {
        return md5(uniqid(rand(), true));
    }

    /**
     * セッションデータとクッキーデータに保存されているユニークIDの値を比較します。
     * 
     * @param Array $session セッションデータ
     * @param Array $cookie  クッキーデータ
     *
     * @return Boolean 両方のユニークIDが同一である場合は true。それ以外の場合は false。
     */
    private function compareUniqueId(array $session, array $cookie)
    {
        $session_unique_id = Arrays::findValue($session, '_SESSION_VALIDATION=>ValidateUniqId', null);
        $cookie_unique_id  = Arrays::getValue($cookie, 'ValidateUniqId', null);
        
        return (isset($session_unique_id) && $session_unique_id == $cookie_unique_id);
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
     */
    private function start()
    {
        if ($this->isCliExecuted()) {
            $_SESSION = [];
        } else {
            session_start();
        }
    }

    /**
     * セッションを破棄します。
     */
    private function destroy()
    {
        ($this->isCliExecuted() === false) && session_destroy();
    }

    /**
     * セッションIDを再生成します。
     */
    private function regenerateId()
    {
        ($this->isCliExecuted() === false) && session_regenerate_id(true);
    }

    /**
     * セッションIDを新しく設定します。
     * 
     * @param String $value 新しいセッションIDの値
     */
    private function setId($value)
    {
        ($this->isCliExecuted() === false) && session_id($value);
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
