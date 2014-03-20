<?php
namespace Phpingguo\System\Generator\Html;

use Phpingguo\System\Module\BaseModule;

/**
 * HTML/XMLのコンテンツデータを生成するクラスの共通処理を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IHtmlGenerator
{
    /**
     * 出力するコンテンツデータを取得します。
     *
     * @param BaseModule $module 出力に使用する情報を保持するモジュールのインスタンス
     * 
     * @return String コンテンツデータ
     */
    public function render(BaseModule $module);
}
