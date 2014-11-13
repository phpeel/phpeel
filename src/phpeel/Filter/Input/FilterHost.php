<?php
namespace Phpeel\System\Filter\Input;

use Phpeel\System\Core\Supervisor;
use Phpeel\System\Filter\BaseFilterHost;
use Phpeel\System\Request\Request;

/**
 * 入力型フィルタを管理するクラスです。
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
     * @param Request $request フィルタオブジェクトを適用するリクエストデータ
     * 
     * @return Request フィルタを適用したリクエストデータ
     */
    public function apply(Request $request)
    {
        return $this->applyFilters(__NAMESPACE__, $request);
    }
}
