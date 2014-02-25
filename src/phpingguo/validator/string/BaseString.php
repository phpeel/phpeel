<?php
namespace Phpingguo\System\Validator\String;

use Phpingguo\System\Enums\Charset;
use Phpingguo\System\Enums\ValidationError;
use Phpingguo\System\Exts\Lib\Common\Arrays;
use Phpingguo\System\Exts\Lib\Common\String as CString;
use Phpingguo\System\Exts\Lib\Type\String\Text;
use Phpingguo\System\Validator\IValidator;
use Phpingguo\System\Validator\Options;
use Phpingguo\System\Validator\TraitCommonValidator;
use Phpingguo\System\Validator\ValidationErrorException;

/**
 * 文字列の検証を行うクラスの基本機能を提供する抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseString implements IValidator
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TraitCommonValidator;
    
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $allow_numeric = false;
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @final [オーバーライド禁止]
     * @see \Phpingguo\Validator\IValidator::validate()
     */
    final public function validate(&$value, Options $options)
    {
        $error_result = $this->checkError($value, $options);
        
        if (Arrays::isValid($error_result)) {
            throw new ValidationErrorException($error_result);
        }
        
        return ($error_result !== false);
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 入力文字列の長さを取得します。
     * 
     * @final [オーバーライド禁止]
     * @param String $value	長さを取得する対象となる文字列
     * 
     * @return Integer 文字列の長さを返します。
     */
    final protected function getLength($value)
    {
        return mb_strlen($value, Charset::UTF8);
    }
    
    /**
     * 入力文字列の幅を取得します。
     * 
     * @final [オーバーライド禁止]
     * @param String $value	幅を取得する対象となる文字列
     * 
     * @return Integer 文字列の幅を返します。
     */
    final protected function getWidth($value)
    {
        return strlen(mb_convert_encoding($value, Charset::SJIS_WIN, Charset::UTF8));
    }
    
    /**
     * 入力文字列の、幅判定オプション設定時は幅を、それ以外の時は長さを取得します。
     * 
     * @final [オーバーライド禁止]
     * @param String $value		幅を取得する対象となる文字列
     * @param Options $options	サイズ判定に利用するオプションの設定
     * 
     * @return Integer 幅判定オプション設定時は幅を、それ以外の時は長さを返します。
     */
    final protected function getSize($value, Options $options)
    {
        return $options->isWidth() ? $this->getWidth($value) : $this->getLength($value);
    }
    
    /**
     * 入力文字列に純粋な数値を許容するかどうかを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return Boolean 数値を許容する場合は true を、それ以外の場合は false を返します。
     */
    final protected function getAllowNumeric()
    {
        return $this->allow_numeric;
    }
    
    /**
     * 入力文字列に純粋な数値を許容するかどうかを設定します。
     * 
     * @final [オーバーライド禁止]
     * @param Boolean $enable	純粋な数値を許容するかどうか
     */
    final protected function setAllowNumeric($enable)
    {
        $this->allow_numeric = ($enable === true);
    }
    
    /**
     * 指定した変数のエラーチェックを行います。
     * 
     * @param mixed $value		エラーチェック対象となる変数
     * @param Options $options	エラーチェックに利用するオプションの設定
     * 
     * @return Boolean|Array 入力値が無効(null、空配列、長さ0の文字列)の場合に、nullable オプションが
     * 有効の時は false を、それ以外の時はエラー理由を返します。
     */
    private function checkError($value, Options $options)
    {
        $error_result = $this->checkNullValue($value, $options);
        
        if (Arrays::isEmpty($error_result)) {
            $this->setError($error_result, $this->checkInvalidFormat($value, $options));
            
            foreach ($this->checkOverLimitValues($value, $options) as $error_val) {
                $this->setError($error_result, $error_val);
            }
            
            $this->setError($error_result, $this->checkNgWordExist($value, $options));
        }
        
        return $error_result;
    }
    
    /**
     * 文字列が無効なフォーマットであるかどうかを判定します。
     * 
     * @param String $value		判定対象となる文字列
     * @param Options $options	検証時に利用するオプションの設定
     * 
     * @return ValidationError 無効なフォーマットであればその理由を、それ以外であれば null を返します。
     */
    protected function checkInvalidFormat($value, Options $options)
    {
        if (CString::isNotRegexMatched($value, $options->getPregValue(), 1) ||
            CString::isNotRegexMatched($value, $options->getNotPregValue(), 0) ||
            mb_check_encoding($value, Charset::UTF8) === false ||
            ($this->getAllowNumeric() === false && Text::getInstance()->isValid($value) === false)
        ) {
            return ValidationError::FORMAT();
        }
        
        return null;
    }
    
    /**
     * 文字列の長さまたは幅が最小値または最大値を超えているかどうかを判定します。
     * 
     * @param String $value		検査対象になる文字列
     * @param Options $options	検証時に利用するオプションの設定
     * 
     * @return Array(ValidationError) 最小値または最大値を超えている場合はその理由含んだ配列を、
     * それ以外であれば空配列を返します。
     */
    private function checkOverLimitValues($value, Options $options)
    {
        $min    = $this->getLimitValue('getMinValue', $options);
        $max    = $this->getLimitValue('getMaxValue', $options, PHP_INT_MAX);
        $length = $this->getSize($value, $options);
        
        $result = [];
        
        Arrays::addWhen($length < $min, $result, ValidationError::MIN());
        Arrays::addWhen($max < $length, $result, ValidationError::MAX());
        
        return $result;
    }
    
    /**
     * 許容最小値または許容最大値を取得します。
     * 
     * @param String $method_name			境界値を取得するメソッドの名前
     * @param Options $options				検証時に利用するオプションの設定
     * @param Integer $default [初期値=0]	オプション設定の値がない場合の初期値
     * 
     * @return Integer 許容最小値または許容最大値を返します。
     */
    private function getLimitValue($method_name, Options $options, $default = 0)
    {
        return is_null($options->$method_name()) ? $default : $options->$method_name();
    }
    
    /**
     * 文字列にNGワードの単語が含まれているかどうかを判定します。
     * 
     * @param String $value		検査対象になる文字列
     * @param Options $options	検証時に利用するオプションの設定
     * 
     * @return ValidationError NGワードがあった場合はその理由を、それ以外であれば null を返します。
     */
    private function checkNgWordExist($value, Options $options)
    {
        return null;
    }
}
