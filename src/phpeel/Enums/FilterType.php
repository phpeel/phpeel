<?php
namespace Phpeel\System\Enums;

use Phpeel\ApricotLib\Type\Enum\Enum;

/**
 * モデルフィルタクラスの種類を示します。
 *
 * @final [列挙型属性]
 * @author hiroki sugawara
 */
final class FilterType extends Enum
{
    /** モデルフィルタの種類は pre である */
    const PRE = 'Pre';

    /** モデルフィルタの種類は input である */
    const INPUT = 'Input';

    /** モデルフィルタの種類は output である */
    const OUTPUT = 'Output';
}
