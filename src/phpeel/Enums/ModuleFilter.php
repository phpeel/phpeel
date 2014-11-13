<?php
namespace Phpeel\System\Enums;

use Phpeel\ApricotLib\Type\Enum\Enum;

/**
 * モジュールフィルタの種類を示します。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ModuleFilter extends Enum
{
    /** 実行する入力型フィルタ */
    const INPUT_EXECUTE  = 'exec_input';
    
    /** 実行しない入力型フィルタ */
    const INPUT_SKIP     = 'skip_input';
    
    /** 実行する事後型フィルタ */
    const POST_EXECUTE   = 'exec_post';
    
    /** 実行しない事後型フィルタ */
    const POST_SKIP      = 'skip_post';
    
    /** 実行する出力型フィルタ */
    const OUTPUT_EXECUTE = 'exec_output';
    
    /** 実行しない出力型フィルタ */
    const OUTPUT_SKIP    = 'skip_output';
}
