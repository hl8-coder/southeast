<?php
namespace App\Transformers;

use App\Models\CompanyBankAccountRemark;

/**
 * @OA\Schema(
 *   schema="CompanyBankAccountRemark",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="分组id"),
 *   @OA\Property(property="company_bank_account_id", type="integer", description="公司银行卡id"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class CompanyBankAccountRemarkTransformer extends Transformer
{
    public function transform(CompanyBankAccountRemark $remark)
    {
        return [
            'id'                        => $remark->id,
            'company_bank_account_id'   => $remark->company_bank_account_id,
            'remark'                    => $remark->remark,
            'category'                  => $remark->category,
            'admin_name'                => $remark->admin_name,
            'created_at'                => convert_time($remark->created_at),
        ];
    }
}