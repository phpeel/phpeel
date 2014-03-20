<?php
namespace Phpingguo\System\Filter\Post;

use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Filter\BaseFilterHost;

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
        return Supervisor::getDiContainer(Supervisor::DIS_SYS_SGLT)->get(__CLASS__);
    }
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 登録したフィルタオブジェクトの処理を全て適用します。
     * 
     * @param AppModule $module フィルタオブジェクトを適用するモジュールクラスのインスタンス
     * 
     * @return AppModule フィルタオブジェクトを適用したモジュールクラスのインスタンス
     */
    public function apply(AppModule $module)
    {
        return $this->applyFilters(__NAMESPACE__, $module);
    }
}
