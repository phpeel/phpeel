<?php
namespace Phpingguo\System\Validator;

use Phpingguo\System\Enums\ValidationError;
use Phpingguo\System\Exts\Lib\Common\Arrays;

/**
 * 検証機能の共通処理を提供するトレイトです。
 * 
 * @author hiroki sugawara
 */
trait TraitCommonValidator
{
    /**
     * 与えられた配列にバリデーションエラーの理由を設定します。
     * 
     * @param Array $error_types					エラータイプを保持する配列
     * @param ValidationError $result [初期値=null]	バリデーション検証結果
     */
    private function setError(array &$error_types, ValidationError $result = null)
    {
        Arrays::addWhen(
            is_null($result) === false,
            $error_types,
            function () use ($result) {
                return $result->getValue();
            }
        );
    }
    
    /**
     * 入力値の無効な値のチェックを行います。
     * 
     * @param Integer $value	無効値チェックを行う入力値
     * @param Options $options	検証時実行オプションのデータ
     * 
     * @return Boolean|Array 入力値が無効(null、空配列、長さ0の文字列)の場合に、nullable オプションが
     * 有効の時は false を、それ以外の時はエラー理由を返します。
     */
    private function checkNullValue(&$value, Options $options)
    {
        $error_result = [];
        
        if (is_null($value) || Arrays::isEmpty($value) || 0 === strlen($value)) {
            if ($options->isNullable()) {
                return false;
            }
            
            $this->setError($error_result, ValidationError::INVALID());
        }
        
        return $error_result;
    }
}
