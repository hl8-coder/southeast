<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment() == 'local' && config('sql.sql_log') == true){
            DB::listen(function ($query){
                $tmp = str_replace('%', '^', $query->sql);
                $tmp = str_replace('?', '"'.'%s'.'"', $tmp);
                $qBindings = [];
                foreach ($query->bindings as $key => $value) {
                    if (is_numeric($key)) {
                        $qBindings[] = $value;
                    } else {
                        $tmp = str_replace(':'.$key, '"'.$value.'"', $tmp);
                    }
                }
                $tmp = vsprintf($tmp, $qBindings);
                $tmp = str_replace("\\", "", $tmp);
                $tmp = str_replace("^", "%", $tmp);
                Log::channel(config('sql.log_channel'))->info(' ['.$query->time.'ms] '.$tmp."\n");
            });
        }
    }
}
