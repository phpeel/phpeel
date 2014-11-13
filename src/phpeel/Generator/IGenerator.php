<?php
namespace Phpeel\System\Generator;

use Phpeel\System\Module\BaseModule;

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
     * @param Array $options     インスタンスで使用するオプションデータを保持する配列
     * 
     * @return mixed ビルドにより生成されたコンテンツデータ
     */
    public function build(BaseModule $module, array $options);
}
