<?php
namespace Phpingguo\System\Validator\String\Kana;

use Phpingguo\System\Validator\String\StringFormat;

/**
 * 全角カタカナを検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Hiragana extends StringFormat
{
    /**
     * Hiragana クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct('\xE3(\x81[\x81-\xBF]|\x82[\x80-\x93]|\x83\xBC)');
        
        $this->setAllowFullSize(true);
    }
}
