<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\OldRegulation\OldFirstColApp;

class OldFirstColAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OldFirstColApp::factory(10)->create();
    }
}
