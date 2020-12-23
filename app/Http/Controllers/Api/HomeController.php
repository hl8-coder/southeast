<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\ApiController;
use App\Http\Requests\Backstage\HomeRequest;
use App\Models\RiskGroup;

/**
 * 该控制器用来操作首页显示内容，要求速度快，这里将一般数据直接写死，不走缓存和数据库
 * Class HomeController
 * @package App\Http\Controllers\Api
 */
class HomeController extends ApiController
{

    /**
     * @OA\Get(
     *      path="/home",
     *      operationId="api.home.home",
     *      tags={"Api-首页"},
     *      summary="首页菜单列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function home(HomeRequest $request)
    {
        $menu = [
            'sport'       => 1,
            'e_sport'     => 1,
            'live_casino' => 1,
            'slots'       => 1,
            'games'       => 1,
            'lottery'     => 1,
            'p2p'         => 1,
            'promotion'   => 1,
            'vip_hl8'     => 1,
            'affiliate'   => 1,
            'contact_us'  => 1,
            'information' => 1
        ];
        $user = auth('api')->user();
        if ($user) {
            $rules     = [];
            $riskGroup = RiskGroup::findByCache($user->risk_group_id);
            if ($riskGroup) {
                $rules = $riskGroup->rules ?? [];
            }

            if (in_array('no_show_promotion_page', $rules)) {
                $menu['promotion'] = 0;
            }
        }
        return $this->response->array($menu);
    }
}
