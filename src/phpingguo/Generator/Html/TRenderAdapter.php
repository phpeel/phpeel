<?php
namespace Phpingguo\System\Generator\Html;

use Phpingguo\ApricotLib\Common\String;
use Phpingguo\System\Core\Config;
use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Module\BaseModule;

/**
 * テンプレートエンジンのアダプタクラスの共通機能を実装するトレイトです。
 * 
 * @author hiroki sugawara
 */
trait TRenderAdapter
{
    /**
     * テンプレートエンジンがレンダリングするテンプレートファイルの相対パス付きの名前を取得します。
     * 
     * @final [オーバーライド禁止]
     * @param BaseModule $module モジュールクラスのインスタンス
     * 
     * @return String テンプレートエンジンがレンダリングするテンプレートファイルの相対パス付きの名前
     */
    final protected function getFileName(BaseModule $module)
    {
        return String::concat(
            Supervisor::getApiPath(
                Supervisor::getApiVerDirName($module->getRequest()->getRequestData()),
                $module->getModuleData()->getModuleName(),
                $module->getModuleData()->getSceneName()
            ),
            Config::get('sys.default_template_extensions', '.tmpl')
        );
    }

    /**
     * テンプレートエンジンがレンダリング時に使用する変数の一覧を取得します。
     * 
     * @final [オーバーライド禁止]
     * @param BaseModule $module モジュールクラスのインスタンス
     * 
     * @return Array テンプレートエンジンがレンダリング時に使用する変数の一覧
     */
    final protected function getParameters(BaseModule $module)
    {
        return array_merge([ 'request' => $module->getRequest() ], $module->getModuleData()->getVariables());
    }
}
