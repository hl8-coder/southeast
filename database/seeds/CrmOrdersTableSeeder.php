<?php

use App\Models\CrmOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrmOrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //php artisan db:seed --class=CrmOrdersTableSeeder
        $users = DB::table('users')->whereNotExists(function ($query) {
            $query->select('crm_orders.user_id')
                ->from('crm_orders')
                ->whereRaw('crm_orders.user_id = users.id');
        })->get();

        foreach ($users as $user){
            factory(App\Models\CrmOrder::class)->create(['user_id' => $user->id]);
        }
    }
}
