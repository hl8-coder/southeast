<?php

use Illuminate\Database\Seeder;

class UserRiskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::query()->where('is_agent', false)->get();
        foreach ($users as $user) {
            $user->userRisks()->create();
        }
    }
}
