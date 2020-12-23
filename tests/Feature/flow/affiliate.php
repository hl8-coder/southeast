<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Tests\Feature\Traits\Authorization;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\GameBetDetail;

class affiliate extends TestCase
{ //function start

    use Authorization;

    /**
     * 代理注册
     *
     * @return void
     */
    public function testRegister()
    {

        # 產生代理
        $mainAffiliates = factory(\App\Models\User::class,2)->create([
            'is_agent' => true,
            'currency' => 'VND',
        ]);

        foreach ($mainAffiliates as $mainAffiliate) { // first foreach start
            # 產生代理
            $sub1Affiliates = factory(\App\Models\User::class,3)->create([
                'parent_id' => $mainAffiliate->id,
                'parent_id_list' => $mainAffiliate->id,
                'parent_name' => $mainAffiliate->name,
                'parent_name_list' => $mainAffiliate->name,
                'is_agent' => true,
                'currency' => 'VND',
            ]);

            # 產生會員
            $sub1Members = factory(\App\Models\User::class,5)->create([
                'parent_id' => $mainAffiliate->id,
                'parent_id_list' => $mainAffiliate->id,
                'parent_name' => $mainAffiliate->name,
                'parent_name_list' => $mainAffiliate->name,
                'currency' => 'VND',
            ]);

            foreach ($sub1Affiliates as $sub1Affiliate) { // sec foreach start
                # 產生代理
                $sub2Affiliates = factory(\App\Models\User::class,2)->create([
                    'parent_id' => $sub1Affiliate->id,
                    'parent_id_list' => $mainAffiliate->id,
                    'parent_name' => $sub1Affiliate->name,
                    'parent_name_list' => $mainAffiliate->name,
                    'is_agent' => true,
                    'currency' => 'VND',
                ]);

                # 產生會員
                $sub2Members = factory(\App\Models\User::class,5)->create([
                    'parent_id' => $sub1Affiliate->id,
                    'parent_id_list' => $sub1Affiliate->parent_id_list . "," . $sub1Affiliate->id,
                    'parent_name' => $sub1Affiliate->name,
                    'parent_name_list' => $sub1Affiliate->parent_name_list . "," . $sub1Affiliate->name,
                    'currency' => 'VND',
                ]);

                foreach ($sub2Affiliates as $sub2Affiliate) { // third foreach start
                    # 產生會員
                    $sub3Members = factory(\App\Models\User::class,5)->create([
                        'parent_id' => $sub2Affiliate->id,
                        'parent_id_list' => $sub2Affiliate->parent_id_list . "," . $sub2Affiliate->id,
                        'parent_name' => $sub2Affiliate->name,
                        'parent_name_list' => $sub2Affiliate->parent_name_list . "," . $sub2Affiliate->name,
                        'currency' => 'VND',
                    ]);
                } // third foreach end
            } // sec foreach end
        } // first foreach end

    }

} //function end
