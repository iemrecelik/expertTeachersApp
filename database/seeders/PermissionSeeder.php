<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'show documents']);
        Permission::create(['name' => 'create documents']);
        Permission::create(['name' => 'edit documents']);
        Permission::create(['name' => 'delete documents']);
    }
}
