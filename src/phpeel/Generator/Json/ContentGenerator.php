<?php
namespace Phpeel\System\Generator\Json;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\System\Generator\IGenerator;
use Phpeel\System\Module\BaseModule;

/**
 * JSONのコンテンツデータを生成するクラスです。
 * 
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class ContentGenerator implements IGenerator
{
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * @see IGenerator::build
     */
    public function build(BaseModule $module, array $options)
    {
        return json_encode($this->getEncodeValues($module), $this->parseOptions($options));
    }

    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * JSONエンコードする変数とその値の一覧を取得します。
     * 
     * @param BaseModule $module 変数を保持するモジュールのインスタンス
     * 
     * @return Array JSONエンコードする変数とその値の一覧
     */
    private function getEncodeValues(BaseModule $module)
    {
        $variables = $module->getModuleData()->getVariables();
        
        Arrays::removeWhen(true, $variables, 'response');
        
        return $variables;
    }

    /**
     * JSONエンコード時に使用するオプションを解析します。
     * 
     * @param Array $options オプション設定データ
     * 
     * @return Integer 解析したエンコードオプションの値
     */
    private function parseOptions(array $options)
    {
        $option = 0;
        
        Arrays::eachWalk(
            $options,
            function ($value) use (&$option) {
                $option |= constant($value);
            }
        );
        
        return $option;
    }
}
