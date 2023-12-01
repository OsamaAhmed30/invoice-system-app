<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create(['name' => 'Owner']);
       //Admin Seeder
       $user = User::create([
        'name' => 'Admin', 
        'email' => 'Admin@gmail.com',
        'password' => bcrypt('12345678'),
        'roles_name'=>'Owner',
        'status' => 'مفعل'

    ]);
    
    
   
    $permissions = Permission::pluck('id','id')->all();
 
    $role->syncPermissions($permissions);
   
    $user->assignRole([$role->id]);
   
    }
}
