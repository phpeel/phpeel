<?php
namespace Phpeel\System\Enums;

use Phpeel\ApricotLib\Type\Enum\Enum;

/**
 * HTML/XMLを生成するテンプレートエンジンの種類を示します。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class TemplateEngine extends Enum
{
    /** Twig をテンプレートエンジンとして使用する */
    const TWIG   = 'twig';
    
    /** Smarty をテンプレートエンジンとして使用する */
    const SMARTY = 'smarty';
}
