<?php
namespace Phpingguo\System\Validator\String\Kana;

use Phpingguo\System\Validator\String\StringFormat;

/**
 * 全角カタカナを検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class FullSizeKana extends StringFormat
{
    /**
     * FullSizeKana クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct('\xE3(\x82[\xA1-\xBF]|\x83[\x80-\xB6])');
        
        $this->setAllowFullSize(true);
    }
}
