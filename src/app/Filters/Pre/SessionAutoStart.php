<?php
namespace Phpeel\App\Filters\Pre;

use Phpeel\System\Filter\Pre\IFilter;
use Phpeel\System\Variable\Session;

/**
 * アプリケーションのセッション機能を自動的に開始するフィルタークラスです。
 *
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class SessionAutoStart implements IFilter
{
    /**
     * @see IFilter::execute()
     */
    public function execute()
    {
        // -------------------------------------------------------------------------------------------------------------
        // [Tips]
        // Memcacheにセッションを保存する場合は以下のようにコールする
        // Session::getInstance()->open('memcache', implode(',', MemcacheAgent::getInstance()->getClustering()));
        //
        // 注意!!
        // この場合、「MemcacheAutoStart」、「SessionAutoStart」の順でフィルタが起動するようにしなければいけない
        // -------------------------------------------------------------------------------------------------------------
        Session::getInstance()->open();
    }
}
