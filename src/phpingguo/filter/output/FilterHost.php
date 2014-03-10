<?php
namespace Phpingguo\System\Filter\Output;

use Phpingguo\System\Core\Supervisor;
use Phpingguo\System\Filter\BaseFilterHost;

/**
 * 出力型フィルタを管理するクラスです。
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
        return Supervisor::getDiContainer('system')->get(__CLASS__);
    }
    
    // ---------------------------------------------------------------------------------------------
    // public member methods
    // ---------------------------------------------------------------------------------------------
    /**
     * 登録したフィルタオブジェクトの処理を全て適用します。
     * 
     * @param Response $response フィルタオブジェクトを適用するレスポンスデータ
     * 
     * @return Response フィルタオブジェクトを適用したレスポンスデータ
     */
    public function apply(Response $response)
    {
        return $this->applyFilters(__NAMESPACE__, $response);
    }
}
