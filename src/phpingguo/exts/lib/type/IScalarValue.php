<?php
namespace Phpingguo\System\Exts\Lib\Type;

/**
 * スカラー値を取り扱うクラスの基本処理を定義したインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IScalarValue
{
    /**
     * スカラータイプクラスのデフォルト値を取得します。
     * 
     * @return mixed スカラータイプクラスのデフォルト値を返します。
     */
    public function getDefaultValue();
    
    /**
     * 指定した値をスカラータイプクラスの値として取得します。
     * 
     * @param mixed $base_value		クラスによりスカラータイプクラスに変換される値
     * 
     * @return mixed 指定した値をスカラータイプクラスの値として返します。
     */
    public function getValue($base_value);
    
    /**
     * 指定した値がスカラータイプクラスの値であるかどうかを判別します。
     * 
     * @param mixed $check_value	スカラータイプクラスの値であるかどうかを判定する値
     * 
     * @return Boolean 指定した値の型がスカラータイプクラスである場合は true を、
     * それ以外の場合は false を返します。
     */
    public function isValue(&$check_value);
}
