<?php
namespace Phpeel\System\Filter\Post;

/**
 * 事後に適用するフィルタの共通処理を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IFilter
{
    /**
     * フィルタ処理を実行します。
     * 
     * @param AppModule $module フィルタ処理を適用する対象となるモジュールクラスのインスタンス
     * 
     * @return AppModule フィルタ処理済みのモジュールクラス
     */
    public function execute(AppModule $module);
}
