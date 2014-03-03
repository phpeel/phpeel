<?php
namespace Phpingguo\System\Enums;

use Phpingguo\ApricotLib\Type\Enum\Enum;

/**
 * バリデータの種類を示します。
 * 
 * @final [列挙型属性]
 * @author h-sugawara@m-craft.com
 */
final class Validator extends Enum
{
    /** 符号あり整数値の検証を行うことを示す */
    const INTEGER         = 'Phpingguo\System\Validator\Number\Integer';
    
    /** 符号なし整数値の検証を行うことを示す */
    const UNSIGNED_INT    = 'Phpingguo\System\Validator\Number\UnsignedInteger';
    
    /** ラテンアルファベットの検証を行うことを示す */
    const ALPHABET        = 'Phpingguo\System\Validator\String\Latin\Alphabet';
    
    /** 小文字のラテンアルファベットの検証を行うことを示す */
    const LOWER_ALPHABET  = 'Phpingguo\System\Validator\String\Latin\LowerAlphabet';
    
    /** 大文字のラテンアルファベットの検証を行うことを示す */
    const UPPER_ALPHABET  = 'Phpingguo\System\Validator\String\Latin\UpperAlphabet';
    
    /** ラテンアルファベットとアラビア数字の検証を行うことを示す */
    const ALPHANUMERIC    = 'Phpingguo\System\Validator\String\Latin\Alphanumeric';
    
    /** アスキーコード文字の検証を行うことを示す */
    const ASCII           = 'Phpingguo\System\Validator\String\Latin\Ascii';
    
    /** ひらがなの検証を行うことを示す */
    const HIRAGANA        = 'Phpingguo\System\Validator\String\Kana\Hiragana';
    
    /** 半角カタカナの検証を行うことを示す */
    const HANKAKU_KANA    = 'Phpingguo\System\Validator\String\Kana\HalfWidthKana';
    
    /** 全角カタカナの検証を行うことを示す */
    const ZENKAKU_KANA    = 'Phpingguo\System\Validator\String\Kana\FullSizeKana';
    
    /** 全角文字の検証を行うことを示す */
    const FULLSIZE_STRING = 'Phpingguo\System\Validator\String\Other\FullSizeString';
    
    /** 文字列の検証を行うことを示す */
    const TEXT            = 'Phpingguo\System\Validator\String\Other\TextString';
    
    /** メールアドレスの検証を行うことを示す */
    const MAIL_ADDRESS    = 'Phpingguo\System\Validator\String\Other\MailAddress';
    
    /** 日時の検証を行うことを示す */
    const DATETIME        = 'Phpingguo\System\Validator\DateTime\DateTime';
    
    /** 日時の検証を行うことを示す */
    const DATE            = 'Phpingguo\Validator\DateTime\Date';
    
    /** 時刻の検証を行うことを示す */
    const TIME            = 'Phpingguo\Validator\DateTime\Time';
}
