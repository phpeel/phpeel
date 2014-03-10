<?php
namespace Phpingguo\System\Filter;

use Phpingguo\ApricotLib\Common\Arrays;
use Phpingguo\System\Filter\Input\IFilter as InputIFilter;
use Phpingguo\System\Filter\Output\IFilter as OutputIFilter;
use Phpingguo\System\Filter\Post\IFilter as PostIFilter;
use Phpingguo\System\Filter\Pre\IFilter as PreIFilter;

/**
 * フィルタホストクラスの共通処理を定義する抽象クラスです。
 * 
 * @abstract
 * @author hiroki sugawara
 */
abstract class BaseFilterHost
{
    // ---------------------------------------------------------------------------------------------
    // private fields
    // ---------------------------------------------------------------------------------------------
    private $filters = [];
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * フィルタオブジェクトを追加します。
     * 
     * @final [オーバーライド禁止]
     * @param Array $filter [初期値=array()] 追加するフィルタオブジェクト
     * 
     * @return BaseFilterHost フィルタを追加した状態の自分自身のインスタンスを返します。
    */
    final public function register(array $filter = [])
    {
        $this->setFilters($filter, 'array_merge');
        
        return $this;
    }
    
    /**
     * フィルタオブジェクトを削除します。
     * 
     * @final [オーバーライド禁止]
     * @param Array $filter [初期値=array()] 削除するフィルタオブジェクト
     * 
     * @return BaseFilterHost フィルタを削除した状態の自分自身のインスタンスを返します。
     */
    final public function unregister(array $filter = [])
    {
        $this->setFilters($filter, 'array_diff');
        
        return $this;
    }
    
    // ---------------------------------------------------------------------------------------------
    // private member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * リストに登録されているフィルタオブジェクトを全て適用します。
     * 
     * @final [オーバーライド禁止]
     * @param String $namespace           フィルタホストクラスがある名前空間の名称
     * @param Object $param [初期値=null] フィルターオブジェクトへ渡すパラメータデータ
     * 
     * @return Object フィルターオブジェクトへ渡したパラメータデータを返します。
     */
    final protected function applyFilters($namespace, $param = null)
    {
        foreach ($this->getFilters() as $filter) {
            $param = $this->applyFilter($filter, $namespace, $param);
        }
        
        return $param;
    }
    
    /**
     * フィルタオブジェクトのリストを取得します。
     * 
     * @return Array フィルタオブジェクトのリストを返します。
     */
    private function getFilters()
    {
        return $this->filters;
    }
    
    /**
     * フィルタオブジェクトのリストを設定します。
     * 
     * @param Array $filters      新しく設定するフィルタオブジェクトのリスト
     * @param String $method_name 実行するメソッドまたは関数の名前
     */
    private function setFilters(array $filter, $method_name)
    {
        Arrays::copyWhen(
            empty($filter) === false,
            $this->filters,
            function () use ($filter, $method_name) {
                return $method_name($this->getFilters(), $filter);
            }
        );
    }
    
    /**
     * フィルタオブジェクトの処理を適用します。
     * 
     * @param String $filter_name         フィルタオブジェクトのクラス名
     * @param String $namespace	          フィルタホストクラスがある名前空間の名称
     * @param Object $param [初期値=null] フィルターオブジェクトへ渡すパラメータデータ
     * 
     * @throws \BadMethodCallException 不正なフィルタオブジェクトが登録されていた場合
     * 
     * @return Object フィルターオブジェクトへ渡したパラメータデータを返します。
     */
    private function applyFilter($filter_name, $namespace, $param = null)
    {
        /** @var InputIFilter|OutputIFilter|PostIFilter|PreIFilter $obj_filter */
        $obj_filter = new $filter_name();
        
        if (($obj_filter instanceof $namespace . '\\IFilter') === false) {
            throw new \BadMethodCallException("'execute' method isn't defined in the '{$filter_name}' class.");
        }
        
        if (isset($param)) {
            $param = $obj_filter->execute($param);
        } else {
            $obj_filter->execute();
        }
        
        return $param;
    }
}
