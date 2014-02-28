<?php
namespace Phpingguo\System\Validator;

use Phpingguo\System\Exts\Lib\DIWrapper\AuraDIWrapper;

/**
 * 検証に使用するオプション設定を保持するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Options
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $is_nullable    = false;
    private $is_white_space = false;
    private $is_width       = false;
    private $is_new_line    = false;
    private $is_allow_emoji = false;
    private $min_value      = null;
    private $max_value      = null;
    private $regex_match    = null;
    private $regex_unmatch  = null;
    private $ng_words       = [];
    
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * Options クラスの新しいインスタンスを初期化します。
     * 
     * @param Boolean $nullable [初期値=false]    Nullに該当する値を許容するかどうか
     * @param Boolean $white_space [初期値=false] 空白文字に該当する文字列を許容するかどうか
     * @param Boolean $new_line [初期値=false]    改行コードを許容するかどうか
     * @param Boolean $width [初期値=false]       文字列の長さを幅で判定するかどうか
     */
    public function __construct($nullable = false, $white_space = false, $new_line = false, $width = false)
    {
        $this->is_nullable    = $nullable;
        $this->is_white_space = $white_space;
        $this->is_new_line    = $new_line;
        $this->is_width       = $width;
    }
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * Options クラスのインスタンスを取得します。
     * 
     * @param Boolean $force_init [初期値=false] 強制的に初期化するかどうか
     * 
     * @return Options このクラスのインスタンス
     */
    public static function getInstance($force_init = false)
    {
        $instance = AuraDIWrapper::init()->get(__CLASS__);
        
        return ($force_init === true) ? $instance->allReset() : $instance;
    }
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * Nullに該当する値を許容する設定にします。
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function nullable()
    {
        $this->is_nullable = true;
        
        return $this;
    }
    
    /**
     * Nullに該当する値を許容するかどうかを判定します。
     * 
     * @return Boolean Nullに該当する値を許容する場合は true。それ以外の場合は false。
     */
    public function isNullable()
    {
        return $this->is_nullable;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 空白文字に該当する値を許容する設定にします。
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function whitespace()
    {
        $this->is_white_space = true;
        
        return $this;
    }
    
    /**
     * 空白文字に該当する値を許容するかどうかを判定します。
     * 
     * @return Boolean 空白文字に該当する値を許容する場合は true。それ以外の場合は false。
     */
    public function isWhiteSpace()
    {
        return $this->is_white_space;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 改行を許容する設定にします。
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function newLine()
    {
        $this->is_new_line = true;
        
        return $this;
    }
    
    /**
     * 改行を許容するかどうかを判定します。
     * 
     * @return Boolean 改行を許容する場合は true。それ以外の場合は false。
     */
    public function isNewLine()
    {
        return $this->is_new_line;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 文字列の長さを数ではなく幅で判定する設定にします。
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function width()
    {
        $this->is_width = true;
        
        return $this;
    }
    
    /**
     * 文字列の長さを幅で判定するかどうかを判定します。
     * 
     * @return Boolean 文字列の長さを幅で判定する場合は true。それ以外の場合は false。
     */
    public function isWidth()
    {
        return $this->is_width;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 文字列に絵文字を含むことを許容する設定にします。
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function emoji()
    {
        $this->is_allow_emoji = true;
        
        return $this;
    }
    
    /**
     * 文字列に絵文字が含まれることが許容されているかどうかを判定します。
     * 
     * @return Boolean 絵文字が含まれても良い場合は true。それ以外の場合は false。
     */
    public function isAllowEmoji()
    {
        return $this->is_allow_emoji;
    }
    
    /**
     * 検証時に許容する最小値を設定します。
     * 
     * @param mixed $value 検証時に許容する最小値
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function min($value)
    {
        $this->min_value = $value;
        
        return $this;
    }
    
    /**
     * 検証時に許容する最小値の値を取得します。
     * 
     * @return mixed 検証時に許容する最小値の値
     */
    public function getMinValue()
    {
        return $this->min_value;
    }
    
    /**
     * 検証時に許容する最大値を設定します。
     * 
     * @param mixed $value 検証時に許容する最大値
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function max($value)
    {
        $this->max_value = $value;
        
        return $this;
    }
    
    /**
     * 検証時に許容する最大値の値を取得します。
     * 
     * @return mixed 検証時に許容する最大値の値
     */
    public function getMaxValue()
    {
        return $this->max_value;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 検証時にマッチすべき文字列を正規表現で指定します。
     * 
     * @param String $value 検証時にマッチすべき文字列の正規表現
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function preg($value)
    {
        $this->regex_match = $value;
        
        return $this;
    }
    
    /**
     * 検証時にマッチすべき文字列を正規表現形式で取得します。
     * 
     * @return String 検証時にマッチすべき文字列の正規表現形式
     */
    public function getPregValue()
    {
        return $this->regex_match;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 検証時にマッチしてはいけない文字列を正規表現で指定します。
     * 
     * @param String $value 検証時にマッチしてはいけない文字列の正規表現
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function notPreg($value)
    {
        $this->regex_unmatch = $value;
        
        return $this;
    }
    
    /**
     * 検証時にマッチしてはいけない文字列を正規表現形式で取得します。
     * 
     * @return String 検証時にマッチしてはいけない文字列の正規表現形式
     */
    public function getNotPregValue()
    {
        return $this->regex_unmatch;
    }
    
    /**
     * [文字列型バリデーターのみ設定有効]
     * 検証時に使用するNGワードリストを指定します。
     * 
     * @param array $list 検証時に使用するNGワードリスト
     * 
     * @return Options 設定後の自分自身のインスタンス
     */
    public function ngword(array $list)
    {
        $this->ng_words = $list;
        
        return $this;
    }
    
    /**
     * 検証時に使用するNGワードリストを取得します。
     * 
     * @return Array 検証時に使用するNGワードリスト
     */
    public function getNgWordList()
    {
        return $this->ng_words;
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 検証オプションを初期化します。
     * 
     * @return Options 初期化後の自分自身のインスタンス
     */
    private function allReset()
    {
        $this->is_nullable    = false;
        $this->is_white_space = false;
        $this->is_width       = false;
        $this->is_new_line    = false;
        $this->is_allow_emoji = false;
        $this->min_value      = null;
        $this->max_value      = null;
        $this->regex_match    = null;
        $this->regex_unmatch  = null;
        $this->ng_words       = [];
        
        return $this;
    }
}
