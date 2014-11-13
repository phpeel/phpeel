<?php
namespace Phpeel\System\Filter\Output;

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
     * @param Response $response フィルタ処理を適用するレスポンスデータ
     * 
     * @return Response フィルタ処理済みのレスポンスデータ
     */
    public function execute(Response $response);
}
