<?php

namespace App\Observers;

use App\Models\Config;

class ConfigObserver
{
    public function saved(Config $config)
    {
        $config->flushCache();
    }
}
