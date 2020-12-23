<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ConfigsTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(AdminsTableSeeder::class);
        $this->call(GamePlatformsTableSeeder::class);
        $this->call(VipsTableSeeder::class);
        $this->call(RewardsTableSeeder::class);
        $this->call(PaymentGroupsTableSeeder::class);
        $this->call(RiskGroupsTableSeeder::class);
        $this->call(LanguagesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(BonusGroupsTableSeeder::class);
        $this->call(GamePlatformProductsTableSeeder::class);
        $this->call(ExchangeRatesTableSeeder::class);
        $this->call(PromotionTypesTableSeeder::class);
        $this->call(PromotionsTableSeeder::class);

        $this->call(MenusTableSeeder::class);
//        $this->call(ActionsTableSeeder::class);

        //create user & crm_order & call_log
        $this->call(UserTableSeeder::class);
//        $this->call(CrmOrdersTableSeeder::class);
//        $this->call(CallLogsTableSeeder::class);

        // 方便測試用
        //$this->call(BankTransactionsTableSeeder::class);

        $this->call(GamesTableSeeder::class);
        $this->call(ChangingConfigsTableSeeder::class);
        $this->call(B46PlatformSeeder::class);
    }
}
