<?php
namespace Phpeel\System\Filter\Output;

use Phpeel\System\Module\BaseModule;

/**
 * サーバーの出力に適用するフィルタの共通処理を定義するインターフェイスです。
 *
 * @author hiroki sugawara
 */
interface IFilter
{
    /**
     * フィルタ処理を実行します。
     *
     * @param BaseModule $module フィルタ処理を適用するモジュールのインスタンス
     *
     * @return BaseModule フィルタ処理済みのモジュールのインスタンス
     */
    public function execute(BaseModule $module);
}
