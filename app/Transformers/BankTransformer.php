<?php
namespace App\Transformers;

use App\Models\Bank;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="Bank",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="平台id"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="name", type="string", description="银行名称"),
 *   @OA\Property(property="languages", type="array", description="银行名称多语言", @OA\Items(
 *      @OA\Property(property="language", type="string", description="语言"),
 *      @OA\Property(property="front_name", type="string", description="前端显示银行名称"),
 *      @OA\Property(property="maintenance_schedules", type="array", description="维护计划", @OA\Items()),
 *   )),
 *   @OA\Property(property="code", type="string", description="银行编码"),
 *   @OA\Property(property="min_balance", type="number", description="最小金额"),
 *   @OA\Property(property="daily_limit", type="number", description="日限制金额"),
 *   @OA\Property(property="annual_limit", type="number", description="总流水限制(存款+提款)"),
 *   @OA\Property(property="is_auto_deposit", type="integer", description="是否开启自动充值"),
 *   @OA\Property(property="status", type="integer",description="状态"),
 *   @OA\Property(property="display_status", type="string",description="状态显示文字"),
 *   @OA\Property(property="admin_name", type="string",description="管理员名称"),
 *   @OA\Property(property="icon", type="string", description="银行小图标"),
 *   @OA\Property(property="images", description="图片", ref="#/components/schemas/Image"),
 * )
 */
class BankTransformer extends Transformer
{
    protected $availableIncludes = ['images'];

    public function transform(Bank $bank)
    {
        $path = empty($bank->image) ? "" : $bank->image;
        $data = [
            'id'                => $bank->id,
            'currency'          => $bank->currency,
            'name'              => $bank->name,
            'languages'         => $bank->languages,
            'code'              => $bank->code,
            'min_balance'       => $bank->min_balance,
            'daily_limit'       => $bank->daily_limit,
            'annual_limit'      => $bank->annual_limit,
            'is_auto_deposit'   => (int)$bank->is_auto_deposit,
            'status'            => $bank->status,
            'display_status'    => transfer_show_value($bank->status, Model::$booleanStatusesDropList),
            'image'             => strstr($path, 'http') == false ? get_image_url($path) : $path,
            'icon'              => get_image_url($bank->icon),
            'admin_name'        => $bank->admin_name,
        ];
        switch ($this->type){
            case 'front':
                unset($data['languages']);
                $languageSet = $bank->getLanguageSet(app()->getLocale());
                $data['front_name'] = $languageSet['front_name'];
                if (!empty($languageSet['maintenance_schedules'])) {
                    $data['maintenance_schedules'] = explode(';', str_replace('；', ';', $languageSet['maintenance_schedules']));
                } else {
                    $data['maintenance_schedules'] = [];
                }
                break;
        }

        return $data;
    }

    public function includeImages(Bank $bank)
    {
        return $this->collection($bank->images, new ImageTransformer());
    }
}
