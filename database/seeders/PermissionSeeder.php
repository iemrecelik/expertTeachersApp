<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    private Array $staffPermissions = [
        'show module documents',
        'show documents',
        'create documents',
        'edit documents',
        
        'show module categories',
        'show categories',
        'create categories',
        // 'edit categories',

        'show module unions',
        'show unions',
        'create unions',
        'edit unions',

        'show module institutions',
        'show institutions',
        'create institutions',
        'edit institutions',
        
        'show module lawsuits',
        'show lawsuits',
        'create lawsuits',
        'edit lawsuits',
        
        'show module teachers',
        'show teachers',
        'create teachers',
        'create excel teachers',
        'create images teachers',
        'edit teachers',
        'create law to teachers',
        'delete law to teachers',
        'add document teachers',
        'delete document teachers',

        'show statistical lawsuits',

        'show module list',
        'show list',
        'create list',
        'edit list',
        'delete list',

        'show module document comment',
        'show document comment',
        'create document comment',
        'edit document comment',
        'delete document comment'
    ];

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

            'show module unions',
            'show unions',
            'create unions',
            'edit unions',
            'delete unions',

            'show module institutions',
            'show institutions',
            'create institutions',
            'edit institutions',
            'delete institutions',
            
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
            'create law to teachers',
            'delete law to teachers',
            'add document teachers',
            'delete document teachers',

            'processes document_record_reports',

            'show statistical lawsuits',

            'show module list',
            'show list',
            'create list',
            'edit list',
            'delete list',

            'show module document comment',
            'show document comment',
            'create document comment',
            'edit document comment',
            'delete document comment'
        ];

        foreach ($permissions as $key => $val) {
            if(Permission::where('name', $val)->get()->isEmpty()) {
                Permission::create(['name' => $val]);
            }
        }

        $role = Role::where('name', 'staff')->first();
        $role->givePermissionTo($this->staffPermissions);

        $role = Role::where('name', 'admin')->first();
        $role->givePermissionTo(Permission::all());
        
        $role = Role::where('name', 'auth_admin')->first();
        $role->givePermissionTo(Permission::all());

        /* $user = User::find(2);

        $user->givePermissionTo(Permission::all()); */
    }
}
