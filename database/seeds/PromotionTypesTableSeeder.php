<?php

use Illuminate\Database\Seeder;

class PromotionTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('promotion_types')->delete();

        \DB::table('promotion_types')->insert(array(
            0 =>
                array(
                    'id'              => 1,
                    'currencies'      => '["THB", "USD"]',
                    'languages'       => '[{"title": "NEW MEMBERS", "currency": "USD", "description": "NEW MEMBERS"}, {"title": "สมาชิกใหม่", "currency": "THB", "description": "โปรโมชั่นโดนใจ สมาชิกใหม่รับเต็มๆ"}]',
                    'code'            => 'new_members',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567849961_SftU93D6Kk.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'christian',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-08 11:10:51',
                ),
            1 =>
                array(
                    'id'              => 2,
                    'currencies'      => '["VND", "THB"]',
                    'languages'       => '[{"title": "SPORTS BOOK", "currency": "VND", "description": "SPORTS BOOK"}, {"title": "หน้ากีฬา", "currency": "THB", "description": "รวมกีฬา ท้าเซียน พร้อมให้คุณประลองแล้ววันนี้!"}]',
                    'code'            => 'sport',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567849995_F5VrWdfAqJ.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'jennifer',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-12 18:21:39',
                ),
            2 =>
                array(
                    'id'              => 3,
                    'currencies'      => '["THB", "USD"]',
                    'languages'       => '[{"title": "LIVE CASINO", "currency": "USD", "description": "LIVE CASINO"}, {"title": "คาสิโนสด", "currency": "THB", "description": "ลุ้นรวยกับดีลเลอร์สุดสวยวันนี้ ที่ Empiregem"}]',
                    'code'            => 'live',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567850086_37cC6yAI9E.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'christian',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-08 11:11:23',
                ),
            3 =>
                array(
                    'id'              => 4,
                    'currencies'      => '["THB", "USD"]',
                    'languages'       => '[{"title": "SLOT GAMES", "currency": "USD", "description": "SLOT GAMES"}, {"title": "เกมส์สล็อต", "currency": "THB", "description": "เกมส์ยอดนิยม ที่ไม่ควรพลาด"}]',
                    'code'            => 'slot',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567850106_WT4nZrkTYx.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'christian',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-08 11:11:37',
                ),
            4 =>
                array(
                    'id'              => 5,
                    'currencies'      => '["THB", "USD"]',
                    'languages'       => '[{"title": "EG VIRTUAL", "currency": "USD", "description": "EG VIRTUAL"}, {"title": "EG เวอร์ชวล", "currency": "THB", "description": "เปลี่ยนทุกประสบการณ์การเดิมพันกับเกมส์เสมือนจริง กับ"}]',
                    'code'            => 'eg',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567850122_5kDq19D9fe.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'christian',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-08 11:12:06',
                ),
            5 =>
                array(
                    'id'              => 6,
                    'currencies'      => '["THB", "USD"]',
                    'languages'       => '[{"title": "REBATE", "currency": "USD", "description": "REBATE"}, {"title": "คืนเงิน", "currency": "THB", "description": "เล่นมากเท่าไหร่ ยิ่งได้เท่าตัว"}]',
                    'code'            => 'rebate',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567850144_upbRIzbIF9.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'christian',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-08 11:12:18',
                ),
            6 =>
                array(
                    'id'              => 7,
                    'currencies'      => '["THB", "USD"]',
                    'languages'       => '[{"title": "VIP ACCESS", "currency": "USD", "description": "VIP ACCESS"}, {"title": "สมาชิก VIP", "currency": "THB", "description": "สมาชิก EG VIP รับสิทธิประโยชน์สุด Exclusive มากมาย"}]',
                    'code'            => 'vip',
                    'web_img_path'    => 'uploads/images/201909/07/7_1567850159_KOnLnVvktT.jpg',
                    'mobile_img_path' => '',
                    'status'          => 1,
                    'admin_name'      => 'christian',
                    'sort'            => 0,
                    'created_at'      => NULL,
                    'updated_at'      => '2019-09-08 11:14:38',
                ),
        ));


    }
}
