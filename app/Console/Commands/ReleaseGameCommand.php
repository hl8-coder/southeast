<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use Illuminate\Console\Command;

class ReleaseGameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:release_game {platform_code}';

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
        $platformCode = $this->argument('platform_code');

        if (!empty($platformCode)) {
            $platformCode = strtolower($platformCode);
            switch ($platformCode) {
                case "ss": // SmartSoft
                    # 1.创建 game_platform数据. status=0 暂时不对展示
                    $game_platform_data = [
                        'name' => 'SS',
                        'code' => 'SS',
                        'icon' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAYAAAByDd+UAAACRUlEQVRIicWWz4tOURjHv9fYKIkiSs3KjyzGj9mwIqOJHSsysRh/gKKQIplSojFTpGnKr4UiCwspY2FsFE2THYma/FqIyAwbGR+d7hnv897n3Pu+Y4pPPTVzzvM8n/Oe+55zX/1rMusDmtXvlrRd0oSkc5KeuwxDlmVu7I+wQWwGHuM5C2RltaWkkmO0Abedpp6XQFeitkyXFC4BBl3raobjAqclbAGOAeOVravpB+Y0I+wGXlW2gnvAGeAS8NHN1ngbttmJjPCAK6nnMLAwsfXtwJDLzhl1IiN879JzJoGNRnAd+Al8Ao6Y8U3Aa1P3AVjpREY45lQ5N0zTC242l580OTuBg8D8Rs/whWuVs8s0e+Nm6+mpOhaznDVNixkdSmbUOB7WLmlA0mo32+SWjhRW/dBllNPjREZYtV1Pgb3xjIbcFfHZ/nKZBZzICE+5bM8X4IT5tK3xTJbx3YmMMMTlksIUvUa8AfiRyBl3ooIwxHrgiitNs8PUXUtk1AnLvqVPJHVLWibpoqRJl1Fjnfn7nZutwqx0EbDc/L8YOF1yma81eaNutsGWhm9gX3jQMTlcBPtMw7nA0fgKugtsNXNdTpUz4URGeNWl59yP92TqnRlij6swWMfsgvOOpHZJbYXxzhgjkoYlPYvjqyRtk7TGrb7GoBsxn3Aqwsv3m1vq9HgAdFSd++I2LQVu/YVorHDZO0+ZcCq2AI9cW0949x0yV9+MfrWF2A98dpqc88C8RM2MhCEWxOZf45m8GS/xyrr/h6TfaODfJawqbZwAAAAASUVORK5CYII=',
                        'request_url' => 'https://gameserver.ssgportal.com/GamblingService/GamblingWebService.asmx',
                        'report_request_url' => 'https://gameserver.ssgportal.com/GamblingService/GamblingWebService.asmx',
                        'account' =>
                            [
                                'fish_url' => 'https://gameserver.ssgportal.com/JetX/JetX/Loader.aspx?StartPage=Board',
                                'slot_url' => 'https://gameserver.ssgportal.com/Slots/Loader.aspx?GameType=Slots&StartPage=Game',
                                'hash_value' => '7d3da30c-760b-40f1-8c89-1b4217fa1f0c',
                                'portal_name' => 'CloudGeek',
                                'client_external_key' => '111'
                            ]
                        ,
                        'is_update_list' => 0,
                        'update_interval' => 1,
                        'interval' => 2,
                        'delay'=> 10,
                        'offset' => 20,
                        'limit' => 1,
                        'is_auto_transfer' => 0,
                        'status' => 1,
                        'is_update_odds' => 0,
                        'is_maintain' => 1,
                        'is_wallet_maintain' => 0,
                    ];
                    GamePlatform::updateOrCreate(['code' => 'SS'],$game_platform_data);
                    # 2. 创建 game_platform_products数据.
                    $game_platform_product_codes[] = [
                        'platform_code' => 'SS',
                        'code' => 'SS_Slot',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'SS_Slot', 'content' => '<p>SS_Slot</p>', 'language' => 'en-US', 'description' => 'SS_Slot'],
                                ['name' => 'SS_Slot', 'content' => '<p>SS_Slot</p>', 'language' => 'vi-VN', 'description' => 'SS_Slot'],
                                ['name' => 'SS_Slot', 'content' => '<p>SS_Slot</p>', 'language' => 'th', 'description' => 'SS_Slot'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'type' => 2,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,  // 暂时不对外展示,等后台上传games成功后再修改状态.
                        'is_close_adjustment' => 1,
                    ];
                    $game_platform_product_codes[] = [
                        'platform_code' => 'SS',
                        'code' => 'SS_Fish',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'SS_Slot', 'content' => '<p>SS_Fish</p>', 'language' => 'en-US', 'description' => 'SS_Slot'],
                                ['name' => 'SS_Slot', 'content' => '<p>SS_Fish</p>', 'language' => 'vi-VN', 'description' => 'SS_Slot'],
                                ['name' => 'SS_Slot', 'content' => '<p>SS_Fish</p>', 'language' => 'th', 'description' => 'SS_Slot'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'type' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,  // 暂时不对外展示,等后台上传games成功后再修改状态.
                        'is_close_adjustment' => 1,
                    ];

                    foreach ($game_platform_product_codes as $game_platform_product_code) {
                        $platform_code = $game_platform_product_code['platform_code'];
                        $code = $game_platform_product_code['code'];
                        GamePlatformProduct::updateOrCreate(['code' => $code, 'platform_code' => $platform_code], $game_platform_product_code);
                    }


                    # 3. 创建 game_platform_pull_report_schedules 拉去时间表 php artisan southeast:generate-game-platform-pull-report-schedules --days=7 --platform_code=SS

                    # 4. 创建games数据.
                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Argo',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Argo', 'content' => '<p>Argo</p>', 'language' => 'en-US', 'description' => 'Argo'],
                                ['name' => 'Argo', 'content' => '<p>Argo</p>', 'language' => 'vi-VN', 'description' => 'Argo'],
                                ['name' => 'Argo', 'content' => '<p>Argo</p>', 'language' => 'th', 'description' => 'Argo'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];
                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Aztec',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Aztec', 'content' => '<p>Aztec</p>', 'language' => 'en-US', 'description' => 'Aztec'],
                                ['name' => 'Aztec', 'content' => '<p>Aztec</p>', 'language' => 'vi-VN', 'description' => 'Aztec'],
                                ['name' => 'Aztec', 'content' => '<p>Aztec</p>', 'language' => 'th', 'description' => 'Aztec'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];
                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Birds',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Birds', 'content' => '<p>Birds</p>', 'language' => 'en-US', 'description' => 'Birds'],
                                ['name' => 'Birds', 'content' => '<p>Birds</p>', 'language' => 'vi-VN', 'description' => 'Birds'],
                                ['name' => 'Birds', 'content' => '<p>Birds</p>', 'language' => 'th', 'description' => 'Birds'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];
                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'BookOfWin',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'BookOfWin', 'content' => '<p>BookOfWin</p>', 'language' => 'en-US', 'description' => 'BookOfWin'],
                                ['name' => 'BookOfWin', 'content' => '<p>BookOfWin</p>', 'language' => 'vi-VN', 'description' => 'BookOfWin'],
                                ['name' => 'BookOfWin', 'content' => '<p>BookOfWin</p>', 'language' => 'th', 'description' => 'BookOfWin'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Christmas',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Christmas', 'content' => '<p>Christmas</p>', 'language' => 'en-US', 'description' => 'Christmas'],
                                ['name' => 'Christmas', 'content' => '<p>Christmas</p>', 'language' => 'vi-VN', 'description' => 'Christmas'],
                                ['name' => 'Christmas', 'content' => '<p>Christmas</p>', 'language' => 'th', 'description' => 'Christmas'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Cowboy',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Cowboy', 'content' => '<p>Cowboy</p>', 'language' => 'en-US', 'description' => 'Cowboy'],
                                ['name' => 'Cowboy', 'content' => '<p>Cowboy</p>', 'language' => 'vi-VN', 'description' => 'Cowboy'],
                                ['name' => 'Cowboy', 'content' => '<p>Cowboy</p>', 'language' => 'th', 'description' => 'Cowboy'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'DonutCity',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'DonutCity', 'content' => '<p>DonutCity</p>', 'language' => 'en-US', 'description' => 'DonutCity'],
                                ['name' => 'DonutCity', 'content' => '<p>DonutCity</p>', 'language' => 'vi-VN', 'description' => 'DonutCity'],
                                ['name' => 'DonutCity', 'content' => '<p>DonutCity</p>', 'language' => 'th', 'description' => 'DonutCity'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Football',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Football', 'content' => '<p>Football</p>', 'language' => 'en-US', 'description' => 'Football'],
                                ['name' => 'Football', 'content' => '<p>Football</p>', 'language' => 'vi-VN', 'description' => 'Football'],
                                ['name' => 'Football', 'content' => '<p>Football</p>', 'language' => 'th', 'description' => 'Football'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Galaxy',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Galaxy', 'content' => '<p>Galaxy</p>', 'language' => 'en-US', 'description' => 'Galaxy'],
                                ['name' => 'Galaxy', 'content' => '<p>Galaxy</p>', 'language' => 'vi-VN', 'description' => 'Galaxy'],
                                ['name' => 'Galaxy', 'content' => '<p>Galaxy</p>', 'language' => 'th', 'description' => 'Galaxy'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Fish',
                        'type' => 1,
                        'code' => 'JetX',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'JetX', 'content' => '<p>JetX</p>', 'language' => 'en-US', 'description' => 'JetX'],
                                ['name' => 'JetX', 'content' => '<p>JetX</p>', 'language' => 'vi-VN', 'description' => 'JetX'],
                                ['name' => 'JetX', 'content' => '<p>JetX</p>', 'language' => 'th', 'description' => 'JetX'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Pharaoh',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Pharaoh', 'content' => '<p>Pharaoh</p>', 'language' => 'en-US', 'description' => 'Pharaoh'],
                                ['name' => 'Pharaoh', 'content' => '<p>Pharaoh</p>', 'language' => 'vi-VN', 'description' => 'Pharaoh'],
                                ['name' => 'Pharaoh', 'content' => '<p>Pharaoh</p>', 'language' => 'th', 'description' => 'Pharaoh'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Samurai',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Samurai', 'content' => '<p>Samurai</p>', 'language' => 'en-US', 'description' => 'Samurai'],
                                ['name' => 'Samurai', 'content' => '<p>Samurai</p>', 'language' => 'vi-VN', 'description' => 'Samurai'],
                                ['name' => 'Samurai', 'content' => '<p>Samurai</p>', 'language' => 'th', 'description' => 'Samurai'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    $games[] = [
                        'platform_code' => 'SS',
                        'product_code' => 'SS_Slot',
                        'type' => 2,
                        'code' => 'Viking',
                        'currencies' => ['VND','THB','USD'],
                        'languages' =>
                            [
                                ['name' => 'Viking', 'content' => '<p>Viking</p>', 'language' => 'en-US', 'description' => 'Viking'],
                                ['name' => 'Viking', 'content' => '<p>Viking</p>', 'language' => 'vi-VN', 'description' => 'Viking'],
                                ['name' => 'Viking', 'content' => '<p>Viking</p>', 'language' => 'th', 'description' => 'Viking'],
                            ]
                        ,
                        'devices' => ['1','2'],
                        'is_hot' => 0,
                        'is_new' => 0,
                        'is_iframe' => 1,
                        'is_using_cookie' => 0,
                        'is_effective_bet' => 1,
                        'is_close_bonus' => 1,
                        'is_close_cash_back' => 1,
                        'is_close_adjustment' => 1,
                        'is_calculate_reward' => 1,
                        'is_calculate_cash_back' => 1,
                        'is_calculate_rebate' => 1,
                        'status' => 1,
                        'is_soon' => 0,
                        'is_mobile_iframe' => 1,
                    ];

                    foreach ($games as $game) {
                        $platform_code = $game['platform_code'];
                        $product_code = $game['product_code'];
                        $code = $game['code'];
                        Game::updateOrCreate(['platform_code' => $platform_code, 'product_code' => $product_code, 'code' => $code], $game);
                    }

            }
        }
    }
}
