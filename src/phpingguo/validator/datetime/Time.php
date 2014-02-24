<?php
namespace Phpingguo\System\Validator\DateTime;

/**
 * 時刻型の値を検証するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class Time extends BaseDateTime
{
    // ---------------------------------------------------------------------------------------------
    // constructor / destructor
    // ---------------------------------------------------------------------------------------------
    /**
     * Time クラスの新しいインスタンスを初期化します。
     */
    public function __construct()
    {
        parent::__construct('^([0-1][0-9]|[2][0-3])(:*[0-5][0-9]){1,2}$');
        
        $this->setMinValue(0);
        $this->setMaxValue(235959);
    }
}
