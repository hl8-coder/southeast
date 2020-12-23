<?php

namespace App\Console\Commands;

use App\Imports\GamesImport;
use App\Models\GamePlatformProduct;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportGamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:import-games {--product_code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $productCode = $this->option('product_code');

        if ($productCode) {
            $products = GamePlatformProduct::getAll()->where('code', $productCode);
        } else {
            $products = GamePlatformProduct::getAll();
        }

        foreach ($products as $product) {
            $this->import($product);
        }
    }

    public function import(GamePlatformProduct $product)
    {
        $prefixStr = strtoupper(str_replace('_', '', $product->code));

        Excel::import(new GamesImport($product), app_path('Imports/Games/Excels') . '/' . $prefixStr . '.xlsx');
    }
}
