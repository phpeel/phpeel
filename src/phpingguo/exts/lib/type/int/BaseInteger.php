<?php
namespace Phpingguo\System\Exts\Lib\Type\Int;

use Phpingguo\System\Core\AuraDIWrapper;
use Phpingguo\System\Core\Config;
use Phpingguo\System\Exts\Lib\Type\IScalarValue;
use Phpingguo\System\Exts\Lib\Type\TraitScalarValue;
use Phpingguo\System\Exts\Lib\Type\TraitSignedNumber;

/**
 * フレームワークで使用できる整数型を表すための基本となる抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseInteger implements IScalarValue
{
    // ---------------------------------------------------------------------------------------------
    // import trait
    // ---------------------------------------------------------------------------------------------
    use TraitScalarValue, TraitSignedNumber;
    
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * BaseInteger クラスの新しいインスタンスを初期化します。
     * 
     * @param Integer|UnsignedInt $value [初期値=null]	インスタンスが保持する整数型の値
     * @param Boolean $unsigned [初期値=false]			取得する値に符号なしを許すかどうか
     */
    public function __construct($value = null, $allow_unsigned = false)
    {
        $this->setDefaultValue(Config::get('sys.type.int_default', 0));
        $this->setAllowUnsigned($allow_unsigned);
        $this->setInstanceValue($value);
    }
    
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * BaseInteger クラスのインスタンスを取得します。
     * 
     * @final [オーバーライド禁止]
     * @return BaseInteger 生成した、または、生成済みのインスタンスを返します。
     */
    final public static function getInstance()
    {
        // 初期値設定の再ロードは行わない
        return AuraDIWrapper::init()->get(get_called_class());
    }
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @final [オーバーライド禁止]
     * @see \Phpingguo\Exts\Lib\Type\IScalarValue::getValue()
     */
    final public function getValue($base_value = null)
    {
        $value = $this->hasInstanceValue() ? $this->getInstanceValue() : $this->getIntegerValue($base_value);
        
        if (is_null($value)) {
            throw new \InvalidArgumentException(__METHOD__ . ' only accepts integer.');
        }
        
        return $value;
    }
    
    /**
     * @final [オーバーライド禁止]
     * @see \Phpingguo\Exts\Lib\Type\IScalarValue::isValue()
     */
    final public function isValue(&$check_value)
    {
        // 内部処理用のキャッシュ値をクリアする
        $this->clearCacheValue();
        
        if (is_null($check_value) === false && is_numeric($check_value)) {
            $integer_value  = intval($check_value);
            $float_value    = floatval($check_value);
            $allow_unsigned = $this->getAllowUnsigned();
            
            // INT型の最小値以上かつ最大値以下、かつ、型チェックなし比較で同じ値であれば、整数型
            // 符号付チェックがある場合、0以上であれば符号なし整数型、それ以外は符号あり整数型
            if (floatval(~PHP_INT_MAX) <= $float_value && $float_value <= floatval(PHP_INT_MAX) &&
                $integer_value == $float_value &&
                ($allow_unsigned === false || $allow_unsigned === true && $float_value >= 0)) {
                // 内部処理用にキャッシュ値をセットする
                $this->setCacheValue($integer_value);
                
                return true;
            }
        }
        
        return false;
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 指定した引数の値を整数型としての値として取得します。
     * 
     * @param mixed $base_value	整数型の値を取得する変数
     * 
     * @return Integer 値を取得できる場合はその値を、そうでない場合は null を返します。
     */
    private function getIntegerValue($base_value)
    {
        return $this->isValue($base_value) ? $this->getCacheValue(true) : null;
    }
}
