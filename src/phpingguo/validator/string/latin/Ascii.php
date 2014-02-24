<?php
namespace Phpingguo\System\Validator\String\Latin;

use Phpingguo\System\Validator\String\StringFormat;

/**
 * アスキーコードを検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Ascii extends StringFormat
{
    /**
     * Ascii クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct('[\x21-\x7E]');
        
        $this->setAllowNumeric(true);
    }
}
