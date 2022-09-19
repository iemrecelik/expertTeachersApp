<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\DcCategory;

class DcCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DcCategory::factory(10)->create();
    }
}
