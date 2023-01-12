<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'show module documents',
            'show documents',
            'create documents',
            'edit documents',
            'delete documents',
            
            'show module categories',
            'show categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            'show module lawsuits',
            'show lawsuits',
            'create lawsuits',
            'edit lawsuits',
            'delete lawsuits',
            
            'show module teachers',
            'show teachers',
            'create teachers',
            'create excel teachers',
            'create images teachers',
            'edit teachers',
            'delete teachers',

            'processes document_record_reports'
        ];

        foreach ($permissions as $key => $val) {
            if(Permission::where('name', $val)->get()->isEmpty()) {
                Permission::create(['name' => $val]);
            }
        }

        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo(Permission::all());
        
        $role = Role::where('name', 'auth_admin')->first();
        $role->givePermissionTo(Permission::all());

        /* $user = User::find(2);

        $user->givePermissionTo(Permission::all()); */
    }
}
