<?php
namespace Phpingguo\System\Generator;

use Phpingguo\System\Module\BaseModule;

/**
 * コンテンツデータを生成するクラスの共通処理を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IGenerator
{
    /**
     * コンテンツデータをビルドします。
     * 
     * @param BaseModule $module ビルドに使用する情報を保持するモジュールのインスタンス
     * 
     * @return mixed ビルドにより生成されたコンテンツデータ
     */
    public function build(BaseModule $module);
}
