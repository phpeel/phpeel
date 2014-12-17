<?php
namespace Phpeel\System\Filter\Pre;

use Phpeel\System\Core\Supervisor;
use Phpeel\System\Enums\FilterType;
use Phpeel\System\Filter\BaseFilterHost;

/**
 * 事前型フィルタを管理するクラスです。
 *
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class FilterHost extends BaseFilterHost
{
    // ---------------------------------------------------------------------------------------------
    // public class methods
    // ---------------------------------------------------------------------------------------------
    /**
     * FilterHost クラスのインスタンスを取得します。
     *
     * @return FilterHost 初回呼び出し時は新しいインスタンス。それ以降の時は生成済みのインスタンス。
     */
    public static function getInstance()
    {
        return Supervisor::getDiContainer(Supervisor::DIS_SYSTEM)->get(__CLASS__);
    }

    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 登録したフィルタオブジェクトの処理を全て適用します。
     */
    public function apply()
    {
        $this->applyFilters(FilterType::PRE, __NAMESPACE__);
    }
}
