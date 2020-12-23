<?php
namespace App\Transformers;

use App\Models\Bonus;
use App\Models\Model;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="Bonus",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="is_claim", type="boolean", description="是否需要申请"),
 *   @OA\Property(property="display_is_claim", type="string", description="是否需要申请显示"),
 *   @OA\Property(property="category", type="integer", description="新旧红利"),
 *   @OA\Property(property="display_category", type="string", description="新旧红利显示"),
 *   @OA\Property(property="languages", type="array", description="多语言标题", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="title", type="integer", description="标题"),
 *   )),
 *   @OA\Property(property="code", type="string", description="红利代码"),
 *   @OA\Property(property="product_code", type="string", description="产品代码"),
 *   @OA\Property(property="effective_start_at", type="string", description="红利有效开始时间", format="date-time"),
 *   @OA\Property(property="effective_end_at", type="string", description="红利有效结束时间", format="date-time"),
 *   @OA\Property(property="sign_start_at", type="string", description="申请开始时间", format="date-time"),
 *   @OA\Property(property="sign_end_at", type="string", description="申请结束时间", format="date-time"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="bonus_group_id", type="integer", description="红利组别id"),
 *   @OA\Property(property="bonus_group_name", type="string", description="红利组别名称"),
 *   @OA\Property(property="type", type="integer", description="计算类型"),
 *   @OA\Property(property="display_type", type="string", description="计算类型显示"),
 *   @OA\Property(property="rollover", type="integer", description="流水倍数(本金+红利)"),
 *   @OA\Property(property="amount", type="number", description="计算数值"),
 *   @OA\Property(property="is_auto_hold_withdrawal", type="boolean", description="是否自动添加hold withdrawal标签"),
 *   @OA\Property(property="display_is_auto_hold_withdrawal", type="boolean", description="是否自动添加hold withdrawal标签是否显示"),
 *   @OA\Property(property="cycle", type="integer", description="周期"),
 *   @OA\Property(property="display_cycle", type="string", description="周期显示"),
 *   @OA\Property(property="user_type", type="integer", description="会员类型"),
 *   @OA\Property(property="user_count", type="integer", description="会员总数"),
 *   @OA\Property(property="display_user_type", type="string", description="会员类型显示"),
 *   @OA\Property(property="risk_group_ids", type="string", description="风控组别"),
 *   @OA\Property(property="payment_group_ids", type="string", description="支付组别"),
 *   @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
 *      @OA\Property(property="currency", type="string", description="币别"),
 *      @OA\Property(property="min_transfer", type="integer", description="最小转账金额"),
 *      @OA\Property(property="deposit_count", type="integer", description="充值次数"),
 *      @OA\Property(property="max_prize", type="integer", description="奖金上限"),
 *   )),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="show_title", type="string", description="前端显示用"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 * )
 */
class BonusTransformer extends Transformer
{
    public function transform(Bonus $bonus)
    {
        $data = [
            'id'                                => $bonus->id,
            'is_claim'                          => $bonus->is_claim,
            'display_is_claim'                  => transfer_show_value($bonus->is_claim, Model::$booleanDropList),
            'category'                          => $bonus->category,
            'display_category'                  => transfer_show_value($bonus->category, Bonus::$categories),
            'languages'                         => $bonus->languages,
            'code'                              => $bonus->code,
            'product_code'                      => $bonus->product_code,
            'effective_start_at'                => convert_time($bonus->effective_start_at),
            'effective_end_at'                  => convert_time($bonus->effective_end_at),
            'sign_start_at'                     => convert_time($bonus->sign_start_at),
            'sign_end_at'                       => convert_time($bonus->sign_end_at),
            'status'                            => $bonus->status,
            'display_status'                    => transfer_show_value($bonus->status, Model::$booleanStatusesDropList),
            'bonus_group_id'                    => $bonus->bonus_group_id,
            'bonus_group_name'                  => $bonus->bonus_group_name,
            'type'                              => $bonus->type,
            'display_type'                      => transfer_show_value($bonus->type, Bonus::$types),
            'rollover'                          => $bonus->rollover,
            'amount'                            => $bonus->amount,
            'is_auto_hold_withdrawal'           => $bonus->is_auto_hold_withdrawal,
            'display_is_auto_hold_withdrawal'   => transfer_show_value($bonus->is_auto_hold_withdrawal, Model::$booleanDropList),
            'cycle'                             => $bonus->cycle,
            'display_cycle'                     => transfer_show_value($bonus->cycle, Bonus::$cycles),
            'user_type'                         => $bonus->user_type,
            'user_count'                        => !empty($bonus->user_ids) ? count($bonus->user_ids) : 0,
            'display_user_type'                 => transfer_show_value($bonus->user_type, Bonus::$userTypes),
            'risk_group_ids'                    => $bonus->risk_group_ids,
            'payment_group_ids'                 => $bonus->payment_group_ids,
            'currencies'                        => $bonus->currencies,
            'admin_name'                        => $bonus->admin_name,
            'created_at'                        => convert_time($bonus->created_at),
            'updated_at'                        => convert_time($bonus->updated_at),
        ];

        switch ($this->type) {
            case 'associate':
                $data = collect($data)->only(['id', 'title'])->toArray();
                $languageSet = $bonus->getLanguageSet(app()->getLocale());
                $data['title'] = $languageSet['title'];
                return $data;
                break;

            case 'front_index':
                $data = collect($data)->only([
                    'id',
                    'title',
                    'code',
                    'bonus_group_name',
                    'product_code',
                    'is_claim',
                    'effective_start_at',
                    'effective_end_at',
                    'sign_start_at',
                    'sign_end_at',
                    'cycle',
                    'user_type',
                ])->toArray();
                $languageSet = $bonus->getLanguageSet(app()->getLocale());
                $data['title'] = $languageSet['title'];
                $data['show_title'] = $languageSet['title'] . "(" . $data['code'] . ")";
                return $data;
                break;
        }

        return $data;
    }
}
