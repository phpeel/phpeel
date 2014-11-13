<?php
namespace Phpeel\System\Generator\Html;

use Phpeel\System\Module\BaseModule;

/**
 * テンプレートエンジンのアダプターとなるクラスの基本機能を定義するインターフェイスです。
 * 
 * @author hiroki sugawara
 */
interface IEngineAdapter
{
    /**
     * テンプレートエンジンにモジュールクラスが指示するものをレンダリングさせます。
     * 
     * @param BaseModule $module テンプレートエンジンにレンダリングさせるモジュールクラスのインスタンス
     * 
     * @return mixed レンダリングした出力データ
     */
    public function rendering(BaseModule $module);
}
