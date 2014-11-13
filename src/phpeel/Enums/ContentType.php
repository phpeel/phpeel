<?php
namespace Phpeel\System\Enums;

use Phpeel\ApricotLib\Type\Enum\Enum;

/**
 * レスポンスのコンテンツデータの種類を示します。
 * 
 * @final [列挙型属性]
 * @author hiroki sugawara
 */
final class ContentType extends Enum
{
    /** レスポンスのコンテンツデータの種類は html である */
    const HTML = 'text/html';
    
    /** レスポンスのコンテンツデータの種類は xml である */
    const XML  = 'application/xml';
    
    /** レスポンスのコンテンツデータの種類は json である */
    const JSON = 'application/json';
}
