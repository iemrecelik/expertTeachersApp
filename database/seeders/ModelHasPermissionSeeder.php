<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ModelHasPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Permission::create(['name' => 'document report processes']);
        Role::create(['name' => 'auth_admin']);

        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo(Permission::all());
        
        $role = Role::where('name', 'auth_admin')->first();
        $role->givePermissionTo(Permission::all()); */
    }
}
