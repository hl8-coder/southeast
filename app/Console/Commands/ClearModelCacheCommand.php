<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearModelCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear_model_cache {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '手动清除某个 model 的缓存';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!app()->isLocal()){
            $question = "You are not in the local environment, Are you sure to clear the model " . ucfirst($this->argument('name')) ." cache?";
            $confirm = $this->confirm($question, true);
            if (!$confirm){
                $this->error('You have stopped clear cache!');
                exit();
            }
        }
        $this->doClean();
    }

    private function doClean()
    {
        $modelName   = ucfirst($this->argument('name'));
        $modelPath   = 'App\Models\\' . $modelName;
        $modelObject = new $modelPath();
        $modelObject->flushCache();
        $this->info("The model $modelName cache has been clear!");
    }
}
