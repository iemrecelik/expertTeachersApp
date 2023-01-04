<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserAssignRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where([
            ['id', 1],
            ['name', 'Emre'],
        ])->first();

        $user->assignRole('super_admin');
    }
}
