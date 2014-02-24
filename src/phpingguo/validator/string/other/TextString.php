<?php
namespace Phpingguo\System\Validator\String\Other;

use Phpingguo\System\Validator\String\StringFormat;

/**
 * テキスト文字かどうかを検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class TextString extends StringFormat
{
    /**
     * TextString クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct(null);
    }
}
