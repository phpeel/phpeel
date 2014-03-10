<?php
namespace Phpingguo\System\Filter\Pre;

/**
 * 事前に適用するフィルタの共通処理を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IFilter
{
    /**
     * フィルタ処理を実行します。
     */
    public function execute();
}
