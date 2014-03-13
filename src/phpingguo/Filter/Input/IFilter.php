<?php
namespace Phpingguo\System\Filter\Input;

use Phpingguo\System\Request\Request;

/**
 * クライアントからの入力に適用するフィルタの共通処理を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IFilter
{
    /**
     * フィルタ処理を実行します。
     * 
     * @param Request $request フィルタ処理を適用するリクエストデータ
     * 
     * @return Request フィルタ処理済みのリクエストデータ
     */
    public function execute(Request $request);
}
