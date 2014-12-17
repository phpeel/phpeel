<?php
namespace Phpeel\System\Filter\Post;

use Phpeel\System\Core\Supervisor;
use Phpeel\System\Filter\BaseFilterHost;
use Phpeel\System\Module\BaseModule;

/**
 * 事後型フィルタを管理するクラスです。
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
     *
     * @param BaseModule $module フィルタオブジェクトを適用するモジュールクラスのインスタンス
     *
     * @return BaseModule フィルタオブジェクトを適用したモジュールクラスのインスタンス
     */
    public function apply(BaseModule $module)
    {
        return $this->applyFilters('Post', __NAMESPACE__, $module);
    }
}
