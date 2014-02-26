<?php
namespace Phpingguo\System\Validator\DateTime;

use Phpingguo\System\Enums\ValidationError;
use Phpingguo\System\Exts\Lib\Common\Arrays;
use Phpingguo\System\Exts\Lib\Common\String;
use Phpingguo\System\Validator\IValidator;
use Phpingguo\System\Validator\Options;
use Phpingguo\System\Validator\TraitCommonValidator;
use Phpingguo\System\Validator\ValidationErrorException;

/**
 * 日時型の値を検証するクラスの基本機能を提供する抽象クラスです。
 *
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseDateTime implements IValidator
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TraitCommonValidator;
    
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $format_string = '';
    private $min_value     = 0;
    private $max_value     = 0;
    
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * BaseDateTime クラスの新しいインスタンスを初期化します。
     *
     * @param String $format 日時フォーマットを表す文字列
     */
    public function __construct($format)
    {
        $this->format_string = $format;
    }
    
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
     * 検証時に許容する最小値を取得します。
     * 
     * @return Number 検証時に許容する最小値
     */
    final protected function getMinValue()
    {
        return $this->min_value;
    }
    
    /**
     * 検証時に許容する最小値を設定します。
     * 
     * @param Number $value 検証時に許容する最小値
     */
    final protected function setMinValue($value)
    {
        $this->min_value = $value;
    }
    
    /**
     * 検証時に許容する最大値を取得します。
     * 
     * @return Number 検証時に許容する最大値
     */
    final protected function getMaxValue()
    {
        return $this->max_value;
    }
    
    /**
     * 検証時に許容する最大値を設定します。
     * 
     * @param Number $value 検証時に許容する最大値
     */
    final protected function setMaxValue($value)
    {
        $this->max_value = $value;
    }
    
    /**
     * 指定した変数のエラーチェックを行います。
     * 
     * @param mixed $value     エラーチェック対象となる変数
     * @param Options $options エラーチェックに利用するオプションの設定
     * 
     * @return Boolean|Array 入力値が無効(null、空配列、長さ0の文字列)の場合に、nullable オプションが
     * 有効の時は false。それ以外の時はエラー理由。
     */
    private function checkError($value, Options $options)
    {
        $error_result = $this->checkNullValue($value, $options);
        
        if (Arrays::isEmpty($error_result)) {
            $this->setError($error_result, $this->checkInvalidFormat($value));
            
            foreach ($this->checkOverLimitValues($value, $options) as $error_val) {
                $this->setError($error_result, $error_val);
            }
        }
        
        return $error_result;
    }
    
    /**
     * 日時、時刻または日付が、無効なフォーマットであるかどうかを判定します。
     * 
     * @param String $value 検査対象になる日時、時刻、日付
     * 
     * @return ValidationError 無効なフォーマットであればそのエラー理由。それ以外であれば null。
     */
    private function checkInvalidFormat($value)
    {
        if (String::isNotRegexMatched($value, "/$this->format_string/", 1)) {
            return ValidationError::FORMAT();
        }
        
        return null;
    }
    
    /**
     * 日時、時刻または日付が、最小値または最大値を超えているかどうかを判定します。
     * 
     * @param String $value    検査対象になる日時、時刻、日付
     * @param Options $options 検証時に利用するオプションの設定
     * 
     * @return Array(ValidationError) 最小値および最大値を超えている場合はそのエラー理由含んだ配列。
     * それ以外であれば空配列。
     */
    private function checkOverLimitValues($value, Options $options)
    {
        $target = $this->getDateTimeNumber($value);
        $min    = $this->getLimitValue('getMinValue', $options);
        $max    = $this->getLimitValue('getMaxValue', $options);
        
        $result = [];
        
        Arrays::addWhen(is_null($max) || $max < $target, $result, ValidationError::MAX());
        Arrays::addWhen(is_null($min) || $target < $min, $result, ValidationError::MIN());
        
        return $result;
    }
    
    /**
     * 許容最小値または許容最大値を取得します。
     * 
     * @param String $method_name 境界値を取得するメソッドの名前
     * @param Options $options    検証時に利用するオプションの設定
     * 
     * @return Number 許容最小値または許容最大値
     */
    private function getLimitValue($method_name, Options $options)
    {
        return is_null($options->$method_name()) ? $this->$method_name() :
            $this->getDateTimeNumber($options->$method_name());
    }
    
    /**
     * 日時表記の文字列を数値に変換したものを取得します。
     * 
     * @param String $datetime 数値に変換する日時表記の文字列
     * 
     * @return String|null 入力値が適切な値の場合は日時表記の文字列から変換した数値。不正な場合は null。
     */
    private function getDateTimeNumber($datetime)
    {
        $date_pattern = '(([0-9]{2,4})[-\/]*([0-9]{1,2})[-\/]*([0-9]{1,2}))*';
        $time_pattern = '(\s*([0-9]{1,2})[:]*([0-9]{1,2})[:]*([0-9]{1,2})*)*';
        
        preg_match("/^{$date_pattern}{$time_pattern}$/", $datetime, $matches);
        
        $matches = $this->getFormattedList($matches);
        
        if (empty($matches) ||
            (isset($matches[2], $matches[3], $matches[4]) &&
            checkdate($matches[3], $matches[4], $matches[2]) === false)
        ) {
            return null;
        }
        
        return floatval(implode('', $matches));
    }
    
    /**
     * 整形した日時の配列を取得します。
     * 
     * @param Array $list 整形対象となる日時情報を格納した配列
     * 
     * @return Array 整形した日時の配列
     */
    private function getFormattedList(array $list)
    {
        $list = array_pad($list, isset($list[5]) ? 9 : 5, null);
        
        unset($list[0], $list[1], $list[5]);
        
        foreach ($list as $key => $value) {
            $list[$key] = sprintf('%02d', $value ?: 0);
        }
        
        return $list;
    }
}
