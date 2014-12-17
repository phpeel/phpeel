<?php
namespace Phpeel\App\Filters\Pre;

use Phpeel\ApricotLib\Caching\MemcacheAgent;
use Phpeel\System\Filter\Pre\IFilter;

/**
 * Memcacheによるキャッシュ機能を自動的に開始するフィルタークラスです。
 *
 * @final [継承禁止クラス]
 * @author hiroki sugawara
 */
final class MemcacheAutoStart implements IFilter
{
    /**
     * @see IFilter::execute()
     */
    public function execute()
    {
        MemcacheAgent::getInstance()->setClustering();
    }
}
