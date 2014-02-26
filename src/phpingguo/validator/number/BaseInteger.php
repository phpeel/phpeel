<?php
namespace Phpingguo\System\Validator\Number;

use Phpingguo\System\Enums\ValidationError;
use Phpingguo\System\Exts\Lib\Common\Arrays;
use Phpingguo\System\Exts\Lib\Common\String;
use Phpingguo\System\Validator\IValidator;
use Phpingguo\System\Validator\Options;
use Phpingguo\System\Validator\TraitCommonValidator;
use Phpingguo\System\Validator\ValidationErrorException;

/**
 * 整数型の値を検証するクラスの基本機能を提供する抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseInteger implements IValidator
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TraitCommonValidator;
    
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
            $this->setError($error_result, $this->doExclusiveErrorCheck($value));
            
            foreach ($this->checkValueRange($value, $options) as $error_val) {
                $this->setError($error_result, $error_val);
            }
        }
        
        return $error_result;
    }
    
    /**
     * クラスごとに用意された専用のチェックを行います。
     * 
     * @param Integer $value チェック対象の値
     * 
     * @return ValidationError チェックに失敗した場合はそのエラー理由。それ以外の場合は null。
     */
    private function doExclusiveErrorCheck($value)
    {
        $called = 'Phpingguo\\System\\Exts\\Lib\\Type\\Int\\' . String::removeNamespace(get_called_class());
        
        if ($called::getInstance()->isValue($value) === false) {
            return ValidationError::FORMAT();
        }
        
        return null;
    }
    
    /**
     * 入力値の最大値と最小値の範囲チェックを行います。
     * 
     * @param Integer $value   範囲チェックを行う入力値
     * @param Options $options 検証時実行オプションのデータ
     * 
     * @return Array(ValidationError) 最小値または最大値を超えている場合はその理由含んだ配列。
     * それ以外であれば空配列。
     */
    private function checkValueRange($value, Options $options)
    {
        $min = $this->getLimitValue('getMinValue', $options, ~PHP_INT_MAX);
        $max = $this->getLimitValue('getMaxValue', $options);
        
        $result = [];
        
        Arrays::addWhen($value < $min, $result, ValidationError::MIN());
        Arrays::addWhen($max < $value, $result, ValidationError::MAX());
        
        return $result;
    }
    
    /**
     * 許容最小値または許容最大値を取得します。
     * 
     * @param String $method_name                   境界値を取得するメソッドの名前
     * @param Options $options                      検証時に利用するオプションの設定
     * @param Integer $default [初期値=PHP_INT_MAX] オプション設定の値がない場合の初期値
     * 
     * @return Integer 許容最小値または許容最大値
     */
    private function getLimitValue($method_name, Options $options, $default = PHP_INT_MAX)
    {
        return is_null($options->$method_name()) ? $default : $options->$method_name();
    }
}
