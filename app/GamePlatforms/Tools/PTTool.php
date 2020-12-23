<?php
namespace App\GamePlatforms\Tools;

use App\Models\Game;
use App\Models\GameBetDetail;

class PTTool extends IMBaseTool
{

    public function transferBetDetail($data, $insertIndividually=false)
    {
        $betDetails  = [];
        $originalCount= 0;
        $now = now();
        $gameCodes = [];
        $userIds = [];
        foreach ($data as $records) {
            foreach ($records as $record) {
                $gameCodes[] = $this->getGameCode($record['GameName']);
                $userIds[] = $record['PlayerName'];
            }
         }
        $gameCodes = array_unique($gameCodes);
        $userIds = array_unique($userIds);
        $games = Game::getByCodes($this->platform->code, $gameCodes);
        $users = $this->getUsers($userIds);

        foreach ($data as $records) {
            $originalCount += count($records);
            foreach ($records as $record) {
                $gameCode = $this->getGameCode($record['GameName']);
                if (!$game = $games->where('code', $gameCode)->first()) {
                    continue;
                }

                if (!isset($users[$record['PlayerName']])) {
                    continue;
                }

                $user = $users[$record['PlayerName']];

                if ('VND' == $user->currency) {
                    $record['Bet'] = $record['Bet'] / 1000;
                    $record['Win'] = $record['Win'] / 1000;
                    $record['ProgressiveBet'] = $record['ProgressiveBet'] / 1000;
                    $record['ProgressiveWin'] = $record['ProgressiveWin'] / 1000;
                    $record['Balance'] = $record['Balance'] / 1000;
                    $record['CurrentBet'] = $record['CurrentBet'] / 1000;
                }

                $availables = $this->getAvailableBetAndProfit($record);

                $betDetails[] = [
                    'platform_code'     => $this->platform->code,
                    'product_code'      => $game->product_code,
                    'platform_currency' => $user->currency,
                    'order_id'          => $record['GameCode'],
                    'game_code'         => $game->code,
                    'game_type'         => $game->type,
                    'game_name'         => $game->getEnName(),
                    'user_id'           => $user->id,
                    'user_name'         => $user->name,
                    'stake'             => $record['Bet'],
                    'bet'               => $availables['bet'],
                    'profit'            => $availables['profit'],
                    'prize'             => $availables['bet'] + $availables['profit'],
                    'bet_at'            => $record['GameDate'],
                    'payout_at'         => $record['GameDate'],
                    'user_currency'     => $user->currency,
                    'user_stake'        => $record['Bet'],
                    'user_bet'          => $availables['bet'],
                    'user_prize'        => $availables['bet'] + $availables['profit'],
                    'user_profit'       => $availables['profit'],
                    'platform_profit'   => -1 * $availables['profit'],
                    'platform_status'   => '0' == $record['ExitGame'] ? GameBetDetail::PLATFORM_STATUS_BET_SUCCESS : GameBetDetail::PLATFORM_STATUS_CANCEL,
                    'available_bet'     => $availables['bet'],
                    'available_profit'  => -1 * $availables['profit'],
                    'bet_info'          => $this->getBetInfo($record),
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
            'origin_total'   => $originalCount,
            'transfer_total' => count($betDetails),
        ];
    }

    public function getAvailableBetAndProfit($record)
    {
        return [
            'bet'    => $record['Bet'],
            'profit' => floatval($record['Win']) - floatval($record['Bet']),
        ];
    }

    public function getBetInfo($record)
    {
        $device = '';
        $info = 'bet:' . $record['Bet'] . ' result:' . $record['Win'] . ' time:' . $record['GameDate'] . ' in:' . $device;
        return $info;
    }

    public function getGameCode($gameName)
    {
        try {
            $gameName = explode('(', $gameName);
            $gameName = end($gameName);
            return str_replace(')', '', $gameName);
        } catch(\Exception $e){

        }
    }
}
