<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Emre',
            'email' => 'ismailemre.celik@meb.gov.tr',
            'password' => '12345678',
        ]);
    }
}
