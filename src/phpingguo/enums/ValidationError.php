<?php
namespace Phpingguo\System\Enums;

use Phpingguo\System\Exts\Lib\Type\Enum;

/**
 * バリデーションのエラー理由を示します。
 * 
 * @final [列挙型属性]
 * @author hiroki sugawara
 */
final class ValidationError extends Enum
{
    /** 検証失敗の理由が、「対象の値がnullである」ことを示す */
    const INVALID = 'null';
    
    /** 検証失敗の理由が、「対象の値の書式が不正である」ことを示す */
    const FORMAT  = 'format';
    
    /** 検証失敗の理由が、「対象の値の最小値よりも小さい」ことを示す */
    const MIN     = 'min';
    
    /** 検証失敗の理由が、「対象の値の最大値よりも大きい」ことを示す */
    const MAX     = 'max';
    
    /** 検証失敗の理由が、「対象の値が使用禁止単語である」ことを示す */
    const NG_WORD = 'ngword';
}
