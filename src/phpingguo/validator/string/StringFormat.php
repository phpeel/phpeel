<?php
namespace Phpingguo\System\Validator\String;

use Phpingguo\System\Enums\ValidationError;
use Phpingguo\System\Exts\Lib\Common\String;
use Phpingguo\System\Validator\Options;

/**
 * 文字列の入力形式を検証する機能を提供する抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class StringFormat extends BaseString
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $regex_string    = '';
    private $allow_full_size = false;
    
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * StringFormat クラスの新しいインスタンスを初期化します。
     * 
     * @param String $regex	正規表現検索する文字列
     */
    public function __construct($regex)
    {
        $this->setRegexString($regex);
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @final [オーバーライド禁止]
     * @see \Phpingguo\Validator\BaseString::checkInvalidFormat();
     */
    final protected function checkInvalidFormat($value, Options $options)
    {
        $error_result = parent::checkInvalidFormat($value, $options);
        
        if (is_null($error_result) === false) {
            return $error_result;
        }
        
        list($allow_pattern, $deny_pattern) = $this->getRegexPattern($options);
        
        $get_pattern_text = function (array $pattern) {
            return empty($pattern) ? null : ('/' . implode('|', $pattern) . '/');
        };
        
        if ($this->isDenyContain($value, $get_pattern_text, $deny_pattern)) {
            return ValidationError::FORMAT();
        }
        
        $replaced	= $this->getRemovedAllowChars($value, $get_pattern_text, $allow_pattern);
        
        if ($this->isExistControlChar($replaced) ||
            $this->isNotRegexContain($replaced, $this->getRegexString()) ||
            $this->checkString($this->getRemovedHalfWidth($replaced)) === false
        ) {
            return ValidationError::FORMAT();
        }
        
        return null;
    }
    
    /**
     * 全角文字を許容するかどうかを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return Boolean 全角文字を許容する場合は true を、それ以外の場合は false を返します。
     */
    final protected function getAllowFullSize()
    {
        return $this->allow_full_size;
    }
    
    /**
     * 全角文字を許容するかどうかを設定します。
     * 
     * @final [オーバーライド禁止]
     * @param Boolean $enable	全角文字を許容するかどうか
     */
    final protected function setAllowFullSize($enable)
    {
        $this->allow_full_size = ($enable === true);
    }
    
    /**
     * 正規表現文字を取得します。
     * 
     * @final [オーバーライド禁止]
     * @return String 正規表現文字を返します。
     */
    final protected function getRegexString()
    {
        return $this->regex_string;
    }
    
    /**
     * 正規表現文字を設定します。
     * 
     * @final [オーバーライド禁止]
     * @param String $string	正規表現文字
     */
    final protected function setRegexString($string)
    {
        $this->regex_string = $string;
    }
    
    /**
     * 検証に使用する正規表現パターンのリストを取得します。
     * 
     * @param Options $options	検証時に利用するオプションの設定
     * 
     * @return Array 検証に使用する正規表現パターンのリストを返します。
     */
    private function getRegexPattern(Options $options)
    {
        $allow_pattern = [];
        $deny_pattern  = [];
        
        $add_pattern	= function ($condition, $value) use (&$allow_pattern, &$deny_pattern) {
            if ($condition === true) {
                $allow_pattern[]	= $value;
            } else {
                $deny_pattern[]		= $value;
            }
        };
        
        $add_pattern($options->isWhiteSpace(), '[ \t]');
        $add_pattern($options->isNewLine(), '[\r\n\f]');
        $add_pattern($options->isAllowEmoji(), '\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]');
        
        return [ $allow_pattern, $deny_pattern ];
    }
    
    /**
     * 入力文字列から許可する文字を削除したものを取得します。
     * 
     * @param String $haystack				検索対象の文字列
     * @param Callable $get_pattern_text	正規表現パターン文字列
     * @param Array $pattern				正規表現パターン配列
     * 
     * @return String 入力文字列から許可する文字を削除したものを返します。
     */
    private function getRemovedAllowChars($haystack, callable $get_pattern_text, array $pattern)
    {
        return empty($pattern) ? $haystack : preg_replace($get_pattern_text($pattern), '', $haystack);
    }
    
    /**
     * 入力文字列に許可しない文字が含まれているかどうかを判定します。
     * 
     * @param String $haystack				検索対象の文字列
     * @param Callable $get_pattern_text	正規表現パターン文字列
     * @param Array $pattern				正規表現パターン配列
     * 
     * @return Boolean 許可しない文字が含まれていない場合は true を、それ以外の場合は false を返します。
     */
    private function isDenyContain($haystack, callable $get_pattern_text, array $pattern)
    {
        return String::isNotRegexMatched($haystack, $get_pattern_text($pattern), 0);
    }
    
    /**
     * 入力文字列に制御文字が含まれているかどうかを判定します。
     * 
     * @param String $haystack	検索対象の文字列
     * 
     * @return Boolean 制御文字が含まれている場合は true を、それ以外の場合は false を返します。
     */
    private function isExistControlChar($haystack)
    {
        return String::isNotRegexMatched($haystack, '/[\x00-\x1F]|\x7F|\xC2[\x80-\xA0]|\xC2\xAD/', 0);
    }
    
    /**
     * 入力文字列に設定済みの正規表現文字列が含まれていないかどうかを判定します。
     * 
     * @param String $haystack	検索対象の文字列
     * @param String $regex		正規表現文字列
     * 
     * @return Boolean 正規表現文字列が含まれていない場合は true を、それ以外の場合は false を返します。
     */
    private function isNotRegexContain($haystack, $regex)
    {
        return String::isNotRegexMatched($haystack, isset($regex) ? '/^(' . $regex . ')*$/' : null, 1);
    }
    
    /**
     * 全角文字列の長さと幅が一致するかどうかのチェックを行います。
     * 
     * @param String $value	検査対象となる文字列
     * 
     * @return Boolean 長さと幅が正しく一致した場合は true を、それ以外の場合は false を返します。
     */
    private function checkString($value)
    {
        // 全角文字列の幅と長さが一致するかどうかを調べる
        if (0 < strlen($value)) {
            return ($this->getLength($value) * 2 === $this->getWidth($value));
        }
        
        return true;
    }
    
    /**
     * 入力文字列から半角ASCIIおよび半角カタカナを取り除いた文字列を取得します。
     * 
     * @param String $value	対象となる文字列
     * 
     * @return String 半角ASCIIおよび半角カタカナを取り除いたものを返します。
     */
    private function getRemovedHalfWidth($value)
    {
        $result	= $this->getAllowFullSize() ? $value :
            preg_replace('/\xEF(\xBD[\xA1-\xBF]|\xBE[\x80-\x9F])|[\x21-\x7E]/', '', $value);
        
        return $result;
    }
}
