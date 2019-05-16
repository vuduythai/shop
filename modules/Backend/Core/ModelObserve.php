<?php

namespace Modules\Backend\Core;

use Modules\Backend\Models\Config;

class ModelObserve
{

    /**
     * Update 'is_cache_need_deleted' in table #_config
     */
    public static function updateCacheStatus()
    {
        Config::where('slug', 'is_cache_need_deleted')->update(['value'=>System::CACHE_NEED_DELETE]);
    }

    /**
     * Event saved
     */
    public function saved()
    {
        //Log::info('observe model save');
        self::updateCacheStatus();
    }

    /**
     * Even deleted
     * notice: to fire event, when delete, model need instanced
     */
    public function deleted()
    {
        //Log::info('observe model deleted');
        self::updateCacheStatus();
    }
}