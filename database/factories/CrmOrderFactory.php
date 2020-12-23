<?php

use App\Models\Admin;
use App\Models\CallLog;
use App\Models\CrmOrder;
use Faker\Generator as Faker;

$factory->define(App\Models\CrmOrder::class, function (Faker $faker) {
    $admin = App\Models\Admin::inRandomOrder()->first(); #随机捞取一个管理员做分发
    $tag_admin = App\Models\Admin::inRandomOrder()->first(); #随机捞取一个管理员做分发
    $data = [
        'admin_id' => $admin->id,
        'admin_name' => $admin->name,
    ];
    switch(random_int(1,12)){
        case 1: //資料剛匯入
            $data['type'] = random_int(1,2);
            $data['call_status'] = 0;
            $data['remark'] = 'test case 1 -> insert data(no admin)';
            break;
        case 2: //資料被分配 (未打)
            $data['type'] = 1;
            $data['call_status'] = 0;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['remark'] = 'test case 2 -> [W] insert data';
            break;
        case 3:
            $data['type'] = 1;
            $data['call_status'] = 2;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['tag_at'] = now();
            $data['remark'] = 'test case 3 -> [W] call phone - No answer';
            break;
        case 4:
            $data['type'] = 1;
            $data['call_status'] = 3;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 4 -> [W] call phone - Not own number';
            break;
        case 5:
            $data['type'] = 1;
            $data['call_status'] = 1;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 5 -> [W] call phone - Successful(no prefer data)';
            break;
        case 6:
            $data['type'] = 1;
            $data['call_status'] = 1;
            $data['channel'] = 1;
            $data['purpose'] = 1;
            $data['prefer_product'] = 1;
            $data['source'] = 1;
            $data['prefer_bank'] = 1;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 6 -> [W] call phone - Successful';
            break;
        case 7:
            $data['type'] = 1;
            $data['call_status'] = 1;
            $data['channel'] = 1;
            $data['purpose'] = 1;
            $data['prefer_product'] = 1;
            $data['source'] = 1;
            $data['prefer_bank'] = 10;
            $data['bank_remark'] = 'other bank';
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 7 -> [W] call phone - Successful(bank_remark)';
            break;
        case 8:
            $data['type'] = 2;
            $data['call_status'] = 0;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['remark'] = 'test case 8 -> [R]  insert data';
            break;
        case 9:
            $data['type'] = 2;
            $data['call_status'] = 2;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 9 -> [R] call phone - No answer';
            break;
        case 10:
            $data['type'] = 2;
            $data['call_status'] = 1;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 10 -> [R] call phone - Successful(no prefer data)';
            break;
        case 11:
            $data['type'] = 1;
            $data['call_status'] = 1;
            $data['reason'] = 1;
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 11 -> [R] call phone - Successful';
            break;
        case 12:
            $data['type'] = 1;
            $data['call_status'] = 1;
            $data['reason'] = 5;
            $data['reason_remark'] = 'Other reason';
            $data['tag_admin_id'] = $tag_admin->id;
            $data['tag_admin_name'] = $tag_admin->name;
            $data['tag_at'] = now();
            $data['last_save_case_admin_id'] = $tag_admin->id;
            $data['last_save_case_admin_name'] = $tag_admin->name;
            $data['last_save_case_at'] =  now();
            $data['remark'] = 'test case 12 -> [R] call phone - Successful(reason_remark)';
            break;
    }
    return $data;
});

$factory->afterCreating(App\Models\CrmOrder::class, function ($item,Faker $faker) {
    $data = [
        'crm_id' => $item->id,
        'user_id' => $item->user_id,
        'status' => $item->call_status,
        'admin_id' => $item->tag_admin_id,
        'admin_name' => $item->tag_admin_name,
        'category' => $item->type,
        'desc' => $item->remark,
    ] ;
    if($item->call_status){
        factory(App\Models\CallLog::class )->create($data);
    }
});