<?php
namespace Phpingguo\System\Enums;

use Phpingguo\ApricotLib\Type\Enum\Enum;

/**
 * Enumクラスのフルネーム（名前空間＋クラス名）を表す列挙型です。
 * 
 * @final [列挙型属性]
 * @author hiroki sugawara
 */
final class EnumFullName extends Enum
{
    /** 列挙型「ValidationError」であることを示す */
    const VALIDATION_ERROR = 'Phpingguo\System\Enums\ValidationError';
    
    /** 列挙型「Validator」であることを示す */
    const VALIDATOR        = 'Phpingguo\System\Enums\Validator';
}
