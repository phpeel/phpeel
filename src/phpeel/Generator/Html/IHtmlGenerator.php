<?php
namespace Phpeel\System\Generator\Html;

use Phpeel\System\Module\BaseModule;

/**
 * HTMLのコンテンツデータを生成するクラスの共通処理を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IHtmlGenerator
{
    /**
     * 出力するコンテンツデータを取得します。
     *
     * @param BaseModule $module 出力に使用する情報を保持するモジュールのインスタンス
     * @param Array $options     インスタンスで使用するオプションデータを保持する配列
     * 
     * @return String コンテンツデータ
     */
    public function render(BaseModule $module, array $options);
}
