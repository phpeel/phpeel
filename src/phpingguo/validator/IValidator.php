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
     * @param mixed $value     バリデーション対象となる変数
     * @param Options $options バリデーション実行オプション
     * 
     * @throws ValidationErrorException 検証に失敗した場合
     *
     * @return Boolean|Array 検証成功時は true。検証中止の場合は false。
     */
    public function validate(&$value, Options $options);
}
