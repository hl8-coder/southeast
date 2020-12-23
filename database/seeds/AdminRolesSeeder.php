<?php

use Illuminate\Database\Seeder;

class AdminRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('admin_roles')->truncate();

        $adminRoles = [
            [
                'name'        => 'supervisor',
                'description' => 'no limit',
                'sort'        => 1,
                'status'      => 1,
            ],
            [
                'name'        => 'IT Group',
                'description' => 'no limit',
                'sort'        => 2,
                'status'      => 1,
            ],
            [
                'name'        => 'Admin',
                'description' => 'no limit',
                'sort'        => 3,
                'status'      => 1,
            ],
            [
                'name'        => 'CS Leader',
                'description' => 'no limit',
                'sort'        => 4,
                'status'      => 1,
            ],
            [
                'name'        => 'CS Team',
                'description' => 'no limit',
                'sort'        => 5,
                'status'      => 1,
            ],
        ];
        foreach ($adminRoles as $adminRole) {
            $adminRoleExists = \App\Models\AdminRole::where('name', $adminRole['name'])->first();
            if ($adminRoleExists) {
                continue;
            }
            $newAdminRole = \App\Models\AdminRole::insert($adminRole);
            if ($newAdminRole) {
                echo 'add new admin name: ' . $adminRole['name'] . "\n";
            }
        }
    }
}
