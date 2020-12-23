<?php
namespace App\GamePlatforms\Tools;

use App\Models\Game;
use App\Models\GameBetDetail;
use App\Models\GamePlatformProduct;
use App\Models\GamePlatformUser;
use App\Repositories\GamePlatformTransferDetailRepository;

class GPITool extends Tool
{
    protected $currencies = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];

    protected $languages = [
        'zh-CN' => 'zh-cn',
        'vi-VN' => 'vi-vn',
        'en-US' => 'en-us',
        'th' => 'th-th',
    ];

    protected $errors = [
        '1' => 'Transaction already exists.',
        '-1' => 'Unknown error.',
        '-2' => 'Player not found.',
        '-3' => 'Ticket not found or expired.',
        '-4' => 'Insufficient balance.',
        '-7' => 'Player is blocked.',
        '-27' => 'Parameter type mismatch.',
        '-29' => 'Currency mismatch.',
        '-33' => 'Invalid amount.',
        '-48' => 'Invalid merchant password.',
        '-119' => 'Missing input parameter.',
    ];

    protected $countries = [
        'VND' => 'VN',
        'THB' => 'TH',
        'USD' => 'US',
        'CNY' => 'CN',
    ];

    protected $liveTableIdMapping = [
        '1' => [1, 51, 301],
        '52' => [52, 302],
        '3' => [3, 313],
        '31' => [31, 81, 331],
        '82' => [82, 332],
        '33' => [33, 343],
        '113' => [113, 503],
        '223' => [223, 613, 323],
        '61' => [61, 311],
        '91' => [91, 341],
        '4' => [4, 54, 304],
        '14' => [14],
        '5' => [5, 305],
        '6' => [6, 306],
        '316' => [316],
        '68' => [68, 308],
        '59' => [59],
        '60' => [310],
        '72' => [72],
        '67' => [67],
        '1001' => [1001],
        '1031' => [1031],
        '1061' => [1061],
        '1091' => [1091],
        '18' => [18, 318, 1018],
        '1018' => [1018],
        '25' => [25],
        '312' => [312],
        '1004' => [1004],
        '36' =>[36,336,346],
    ];

    protected $liveSlotGameTypes = [
        'C baccarat',
        'NC baccarat',
        'S98 baccarat',
        'fab4',
        'C squeeze',
        'NC squeeze',
        'dragontiger',
        'sicbo',
        'roulette',
        's3pictures',
        'sevenup',
        'colordice',
        'blackjack',
        'superfatan',
        'superhilo',
        'LK baccarat',
        'fishprawncrab',
        'super roulette',
    ];

    protected $mappingLoginGameCode = [
        "lantern_festival" => "lanternfestival",
        "legend_of_nezha" => "legendofnezha",
        "golden_eggs" => "goldeneggs",
        "zeus" => "zeus",
        "world_of_warlords" => "worldofwarlords",
        "pharaoh" => "pharaoh",
        "qixi" => "qixi",
        "samurai_sushi" => "samuraisushi",
        "fortune_cat" => "fortunecat",
        "dimsumlicious" => "dimsumlicious",
        "god_of_gamblers" => "godofgamblers",
        "seven_wonders" => "sevenwonders",
        "underwater_world" => "underwaterworld",
        "baseball" => "baseball",
        "monkey_king" => "monkeyking",
        "bikini_beach" => "bikinibeach",
        "little_monsters" => "littlemonsters",
        "fruitilicious" => "fruitilicious",
        "desert_oasis" => "desertoasis",
        "mafia" => "mafia",
        "forbidden_chamber" => "forbiddenchamber",
        "boxing" => "boxing",
        "god_of_fortune" => "godoffortune",
        "roman_empire" => "romanempire",
        "casino_royale" => "casinoroyale",
        "queen_bee" => "queenbee",
        "tokyo_hunter" => "tokyohunter",
        "fortune_koi" => "fortunekoi",
        "fortune_tree" => "fortunetree",
        "seven_brothers" => "sevenbrothers",
        "golden_wheel" => "goldenwheel",
        "panda" => "panda",
        "klassik" => "klassik",
        "four_guardians" => "fourguardians",
        "phoenix" => "phoenix",
        "fu_lu_shou" => "fulushou",
        "flora_secret" => "florassecret",
        "nutcracker" => "nutcracker",
        "red_chamber" => "redchamber",
        "candylicious" => "candylicious",
        "fortune_dice" => "fortunedice",
        "soccer" => "soccer",
        "three_kingdoms" => "threekingdoms",
        "four_beauties" => "fourbeauties",
        "sweet_treats" => "sweettreats",
        "cleopatra" => "cleopatra",
        "lucky_bomber" => "luckybomber",
        "lucky_royale" => "luckyroyale",
        "sky_strikers" => "skystrikers",
        "monsters_cash" => "monsterscash",
        "lion_dance" => "liondance",
        "trick_or_treat" => "trickortreat",
        "winter_wonderland" => "winterwonderland",
        "wilds_and_the_beanstalk" => "wildsandthebeanstalk",
        "zodiac" => "zodiac",
        "lady_fortune" => "ladyfortune",
        "blossom_garden" => "blossomgarden",
        "hula_girl" => "hulagirl",
        "wuxia_princess_mega_reels" => "wuxiaprincessmegareels",
        "phantom_thief" => "phantomthief",
        "genies_luck" => "geniesluck",
        "world_soccer_slot_2" => "worldsoccerslot2",
        "kungfu_furry" => "kungfufurry",
        "gem_forest" => "gemforest",
        "frost_dragon" => "frostdragon",
        "jewel_land" => "jewelland",
        "money_monkey" => "moneymonkey",
        "moon_rabbit" => "moonrabbit",
        "alchemists_spell" => "alchemistsspell",
        "chess_royale" => "chessroyale",
        "vikings_mega_reels" => "vikingsmegareels",
        "space_neon" => "spaceneon",
        "jazz_it_up" => "jazzitup",
        "fountain_of_fortune" => "fountainoffortune",
        "fa_fa_zhu" => "fafazhu",
        "magic_paper" => "magicpaper",
        "panda_warrior" => "pandawarrior",
        "three_beauties" => "threebeauties",
        "strip_n_roll" => "stripnroll",
        "captain_rabbit" => "captainrabbit",
        "fortune_hong_bao" => "fortunehongbao",
        "nuwa_and_the_five_elements" => "nuwaandthefiveelements",
        "xuan_wu_blessing" => "xuanwublessing",
        "cosmic_boost" => "cosmicboost",
        "wind_chimes" => "windchimes",
        "lunar_legends" => "lunarlegends",
        "dino_age" => "dinoage",
        "cash_and_kisses" => "cashandkisses",
        "lucky_tarot" => "luckytarot",
        "fortune_dragon" => "fortunedragon",
    ];

    # 老虎机有些是连体的游戏code放这里处理
    protected $slotMappingLocalCode = [
        'fafazhu' => 'fa_fa_zhu',
    ];

    # NIUNIU DMQQ TXHD TLMN PKDN BCMN GGTH RMIN
    protected $mappingP2pGameCode = [
        'NIUNIU' => 'html5Niuniu',
        'DMQQ' => 'html5DominoQQ',
        'TXHD' => 'html5HoldemPoker',
        'TLMN' => 'html5TienLen',
        'PKDN' => 'html5PokDeng',
        'BCMN' => 'html5BaiCao',
        'GGTH' => 'html5GaoGae',
        'RMIN' => 'html5IndianRummy',
    ];

    public function transferBetDetail($originBetDetails)
    {
        $betDetails = [];
        $now = now();

        if (isset($originBetDetails['item'])) {
            $originBetDetails = 1 != count($originBetDetails['item']) ? $originBetDetails['item'] : [$originBetDetails['item']];
        } else {
            $originBetDetails = [];
        }

        $gameCodes = [];
        $userIds = [];
        foreach ($originBetDetails as $key => $record) {
            $record = $record['@attributes'];
            $gameCodes[] = $this->getGPIGameCode($record);
            $userIds[] = $record['user_id'];
        }
        $gameCodes = array_unique($gameCodes);
        $userIds = array_unique($userIds);
        $games = Game::getByCodes($this->platform->code, $gameCodes);
        $users = $this->getUsers($userIds);

//        $newCodes = $games->pluck('code')->toArray();
//        $diffCodes = array_diff($gameCodes, $newCodes);
//        if (!empty($diffCodes)) {
//            Log::info("GPI 遗漏游戏：" . json_encode($diffCodes));
//        }

        foreach ($originBetDetails as $key => $record) {

            $record = $record['@attributes'];

            $gameCode = $this->getGPIGameCode($record);
            if (!$game = $games->where('code', $gameCode)->first()) {
                continue;
            }

            if (!isset($users[$record['user_id']])) {
                continue;
            }

            $user = $users[$record['user_id']];

            # 如果是真人并且盈亏为0则是不算有效投注
            if (GamePlatformProduct::TYPE_LIVE == $game->type && empty((float)$record['winlose'])) {
                $availableBet = 0;
            } else {
                $availableBet = $record['bet'];
            }

            $prize = $record['bet'] + $record['winlose'];

            # p2p
            if (GamePlatformProduct::TYPE_P2P == $game->type) {

                $availableBet = $record['bet'] * $record['exchange_rate'];
                $winLose = $record['winlose'] * $record['exchange_rate'];
                $profit = $record['collected_rake'] * $record['exchange_rate'];
                $betDetails[$key] = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'platform_currency' => $this->getPlatformCurrency($user->currency),
                    'order_id'          => $record['round_id'] . $record['user_id'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $game->getEnName(),
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'issue'             => $record['round_id'],
                    'stake'             => $availableBet,
                    'bet'               => $availableBet,
                    'profit'            => -1 * $profit,
                    'prize'             => 0,
                    'bet_at'            => $record['trans_date'],
                    'payout_at'         => $record['trans_date'],
                    'user_currency'     => $user->currency,
                    'user_stake'        => $availableBet,
                    'user_bet'          => $availableBet,
                    'user_prize'        => $availableBet + $winLose,
                    'user_profit'       => $winLose,
                    'after_balance'     => $record['balance'],
                    'platform_profit'   => $profit,
                    'platform_status'   => 1 == $record['status'] ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_CANCEL,
                    'available_bet'     => $availableBet,
                    'available_profit'  => $profit,
                    'win_info'          => !empty($record['game_result']) ? $record['game_result'] : '',
                    'bet_info'          => $record['player_hand'],
                    'jpc'               => 0,
                    'jpw'               => 0,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            } else {
                $betDetails[$key] = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'platform_currency' => $this->getPlatformCurrency($user->currency),
                    'order_id'          => $record['bet_id'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $game->getEnName(),
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'issue'             => $record['round_id'],
                    'stake'             => $record['bet'],
                    'bet'               => $availableBet,
                    'profit'            => $record['winlose'],
                    'prize'             => $prize,
                    'bet_at'            => $record['trans_date'],
                    'payout_at'         => $record['trans_date'],
                    'user_currency'     => $user->currency,
                    'user_stake'        => $record['bet'],
                    'user_bet'          => $availableBet,
                    'user_prize'        => $prize,
                    'user_profit'       => $record['winlose'],
                    'after_balance'     => $record['balance'],
                    'platform_profit'   => -1 * $record['winlose'],
                    'platform_status'   => 1 == $record['status'] ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_CANCEL,
                    'available_bet'     => $availableBet,
                    'available_profit'  => -1 * $record['winlose'],
                    'win_info'          => !empty($record['game_result']) ? $record['game_result'] : '',
                    'bet_info'          => $record['player_hand'],
                    'jpc'               => $record['jcon'],
                    'jpw'               => $record['jwin'],
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        if (!empty($betDetails)) {
            # 添加总的投注明细表
            batch_insert('game_bet_details', $betDetails, true);
        }

        return [
            'origin_total' => count($originBetDetails),
            'transfer_total' => count($betDetails),
        ];
    }

    /**
     * 获取token
     *
     * @param GamePlatformUser $platformUser
     * @return string
     */
    public function getToken(GamePlatformUser $platformUser)
    {
        $token = $platformUser->id . '-' . md5($platformUser->name . strtolower($this->platform->code) . md5($platformUser->password));
        return substr($token, 0, 36);
    }

    public function getCountry($currency)
    {
        return isset($this->countries[$currency]) ? $this->countries[$currency] : 'US';
    }

    public function checkResponse($response, $method, $data)
    {
        $result = get_response_body($response, 'xml', $this->platform->code);
        $statusCode = $response->getStatusCode();
        $this->responseLog($method, $statusCode, $result);

        switch ($result['error_code']) {
            case 0:
                if ('register' == $method) {
                    return '';
                } elseif ('transfer' == $method || 'check' == $method) {
                    $detail = $data['detail'];
                    # 更新第三方交易id
                    GamePlatformTransferDetailRepository::setPlatformOrderNo($data['detail'], $result['trx_id']);
                    # 更新状态成功
                    return GamePlatformTransferDetailRepository::setSuccess($detail);
                } elseif ('login' == $method) {
                    return $result['instantPlayUrl'];
                } elseif ('balance' == $method) {
                    return (float)$result['balance'];
                } elseif ('pull' == $method) {
                    return $result['items'];
                } elseif ('kick_out' == $method) {
                    return true;
                }
                break;
            case 1:
            case -1:
            case -2:
            case -3:
            case -4:
            case -7:
            case -27:
            case -29:
            case -33:
            case -48:
            case -119:
            default:
                if ('transfer' == $method || 'check' == $method) {
                    return GamePlatformTransferDetailRepository::setFail($data['detail'], $this->getError($result['error_code']));
                }
                error_response(422, $this->getError($result['error_code']));
                break;
        }
    }

    public function getGameCode($code)
    {
        foreach ($this->liveTableIdMapping as $key => $value) {
            if (in_array($code, $value)) {
                return $key;
            }
        }

        if (!empty($this->slotMappingLocalCode[strtolower($code)])) {
            return $this->slotMappingLocalCode[strtolower($code)];
        }

        return str_replace(' ', '_', strtolower($code));
    }

    public function getSlotGameCode($code)
    {
        if (!empty($this->slotMappingLocalCode[strtolower($code)])) {
            return $this->slotMappingLocalCode[strtolower($code)];
        }

        $newCode = str_replace(' ', '_', strtolower($code));

        if (!empty($this->mappingLoginGameCode[$newCode])) {
            return $newCode;
        }

        return str_replace(' ', '', strtolower($code));
    }

    /**
     * GPI的真人和老虎机对应的是table_id,而彩票和games对应的是game_id
     *
     * @param $record
     * @return string
     */
    public function getGPIGameCode($record)
    {
        # 先判断游戏类别
        if (in_array($record['game_type'], $this->liveSlotGameTypes)) { # live
            $gameCode = 'GPI_Live';
//            $gameCode = $this->getGameCode($record['table_id']);
        } elseif ("1" == $record['game_type']) { # slot
            $gameCode = $this->getSlotGameCode($record['table_id']);
        } else { # lottery
            $tableId = str_replace(' ', '', strtolower($record['table_id']));
            if (in_array($tableId, [
                'xocdia',
                'thaihilo',
                'fishprawncrab',
                'fishprawncrabgame',
                'moneyblast',
                'super98baccarat'
            ])) {
                $gameCode = $tableId;
            } elseif (!empty($this->mappingP2pGameCode[$record['game_code']])) {
                $gameCode = $this->mappingP2pGameCode[$record['game_code']];
            } else {
                $gameCode = $this->getGameCode($record['game_id']);
            }
        }

        return $gameCode;
    }

    /**
     *
     *
     * @param $code
     * @return mixed
     */
    public function getLoginCode($code)
    {
        return isset($this->mappingLoginGameCode[$code]) ? $this->mappingLoginGameCode[$code] : $code;
    }
}
