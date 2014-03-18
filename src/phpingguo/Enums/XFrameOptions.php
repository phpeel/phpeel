<?php
namespace Phpingguo\System\Enums;

use Phpingguo\ApricotLib\Type\Enum\Enum;

/**
 * インラインフレームの表示オプションの種類を示します。
 * 
 * @final [列挙型属性]
 * @author hiroki sugawara
 */
final class XFrameOptions extends Enum
{
    /** 如何なる場合でもインラインフレームの表示を禁止する */
    const DENY        = 'DENY';
    
    /** インラインフレームの親子ページが同一ドメインにある場合にのみ表示を許可する */
    const SAME_ORIGIN = 'SAMEORIGIN';
}
