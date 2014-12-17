<?php
namespace Phpeel\System\Filter;

use Phpeel\ApricotLib\Common\Arrays;
use Phpeel\ApricotLib\Common\String;
use Phpeel\System\Core\Supervisor;
use Phpeel\System\Enums\FilterType;
use Phpeel\System\Filter\Input\IFilter as InputIFilter;
use Phpeel\System\Filter\Output\IFilter as OutputIFilter;
use Phpeel\System\Filter\Post\IFilter as PostIFilter;
use Phpeel\System\Filter\Pre\IFilter as PreIFilter;

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
     * @param String $filter_type フィルタホストクラスの種類
     * @param String $namespace フィルタホストクラスがある名前空間の名称
     * @param Object $param [初期値=null] フィルターオブジェクトへ渡すパラメータデータ
     *
     * @return Object フィルターオブジェクトへ渡したパラメータデータを返します。
     */
    final protected function applyFilters($filter_type, $namespace, $param = null)
    {
        foreach ($this->getFilters() as $filter) {
            $param = $this->applyFilter($filter_type, $filter, $namespace, $param);
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
     * @param Array $filter 新しく設定するフィルタオブジェクトのリスト
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
     * @param String $filter_type フィルタホストクラスの種類
     * @param String $filter_name フィルタオブジェクトのクラス名
     * @param String $namespace フィルタホストクラスがある名前空間の名称
     * @param Object $param [初期値=null] フィルターオブジェクトへ渡すパラメータデータ
     *
     * @return Object フィルターオブジェクトへ渡したパラメータデータを返します。
     */
    private function applyFilter($filter_type, $filter_name, $namespace, $param = null)
    {
        /** @var InputIFilter|OutputIFilter|PostIFilter|PreIFilter $obj_filter_host */
        $obj_filter_host = $this->getFilterHost($filter_type, $filter_name, $namespace);

        if (FilterType::init($filter_type, FilterType::PRE)->getValue() === FilterType::PRE) {
            $obj_filter_host->execute();
        } else {
            $param = $obj_filter_host->execute($param);
        }

        return $param;
    }

    /**
     * フィルタオブジェクトを処理するフィルタホストクラスのインスタンスを取得します。
     *
     * @param String $filter_type フィルタホストクラスの種類
     * @param String $filter_name フィルタオブジェクトのクラス名
     * @param String $namespace フィルタホストクラスがある名前空間の名称
     *
     * @throws \BadMethodCallException 不正なフィルタオブジェクトが登録されていた場合
     *
     * @return mixed フィルタオブジェクトを処理するフィルタホストクラスのインスタンス
     */
    private function getFilterHost($filter_type, $filter_name, $namespace)
    {
        $filter_class = String::concat(Supervisor::getModelFilterNamespace($filter_type), $filter_name);
        $obj_filter   = new $filter_class();

        if (($obj_filter instanceof $namespace . '\\IFilter') === false) {
            throw new \BadMethodCallException("'execute' method isn't defined in the '{$filter_name}' class.");
        }

        return $obj_filter;
    }
}
