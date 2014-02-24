<?php
namespace Phpingguo\System\Validator\DateTime;

/**
 * 日時型の値を検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class DateTime extends BaseDateTime
{
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * DateTime クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct('^[2-9]([0-9]){1,3}[\-\/]*([0][1-9]|[1][0-2])[\-\/]*([0-2][1-9]|[3][0-1])[ ]*([0-1][0-9]|[2][0-3])(:*[0-5][0-9]){1,2}$');
        
        $this->setMinValue(20000101000000);
        $this->setMaxValue(99991231235959);
    }
}
