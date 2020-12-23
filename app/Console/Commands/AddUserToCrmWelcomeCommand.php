<?php

namespace App\Console\Commands;

use App\Models\CrmOrder;
use App\Models\User;
use App\Services\CrmService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddUserToCrmWelcomeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:add-user-to-crm-welcome-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'southeast:add-user-to-crm-welcome-command';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $userNames = $this->users();
        User::query()->whereIn('name', $userNames)->with('affiliate')->chunk(10, function ($users) {
            foreach ($users as $user) {

                $welcomeOrder = [
                    'user_id'    => $user->id,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->created_at,
                ];
                $affiliate    = $user->affiliate;
                if ($affiliate) {
                    $welcomeOrder['affiliate_id']    = $affiliate->id;
                    $welcomeOrder['affiliated_code'] = $affiliate->affiliate_code;
                }
                $exists = CrmOrder::query()->where('user_id', $user->id)->where('type', CrmOrder::TYPE_WELCOME)->exists();
                if ($exists) {
                    echo 'ignore exists user id: ' . $user->id, "\n";
                }else{
                    DB::table('crm_orders')->insert($welcomeOrder);
                }
            }
        });
    }

    private function users()
    {
        return [
            'tudaica98',
            'giocamchua',
            'kesutthan',
            '0355858612',
            'thanhdat199611',
            'lehoaianh88',
            'vivov11i1935',
            'thanakorn3299',
            'thinees24',
            'kwang251030',
            'epicwin69',
            '0613426800',
            'apichartqqqqq',
            'm12345mg',
        ];
    }
}
