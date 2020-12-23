<?php


namespace App\Transformers;

/**
 * @OA\Schema(
 *   schema="MembersActivityReports",
 *   type="object",
 *   @OA\Property(property="date", type="string", description="时间"),
 *   @OA\Property(property="register", type="integer", description="注册数量"),
 *   @OA\Property(property="deposit", type="integer", description="充值"),
 *   @OA\Property(property="inactive", type="integer", description="不活跃会员数量"),
 * )
 */
class MembersActivityReportsTransformer extends Transformer
{
    public function transform($data)
    {
        return [
            'date'     => $data['date'],
            'register' => $data['register'],
            'deposit'  => $data['deposit'],
            'inactive' => $data['inactive'],
        ];
    }
}