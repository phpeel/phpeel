<?php
namespace Phpingguo\System\Validator\String\Kana;

use Phpingguo\System\Validator\String\StringFormat;

/**
 * 半角カタカナを検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class HalfWidthKana extends StringFormat
{
    /**
     * HalfWidthKana クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct('\xEF(\xBD[\xA1-\xBF]|\xBE[\x80-\x9F])');
    }
}
