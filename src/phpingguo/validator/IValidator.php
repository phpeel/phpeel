<?php
namespace Phpingguo\System\Validator;

/**
 * バリデーション処理を行うための共通メソッドを定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IValidator
{
    /**
     * バリデーションを実行します。
     * 
     * @param mixed $value		バリデーション対象となる変数
     * @param Options $options	バリデーション実行オプション
     * 
     * @throws ValidationErrorException	検証に失敗した場合
     * 
     * @return Boolean|Array 検証に成功した時は true を、検証を中止した場合は false を返します。
     */
    public function validate(&$value, Options $options);
}
