<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Mause Javier',
            'email' => 'mause.javi@gmail.com',
            'password' => Hash::make('12345678'),
        ]);


        // Crear roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Crear permisos
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'view users']);

        // Asignar roles y permisos al usuario
        $user = User::find(1);
        $user->assignRole('admin');
        $user->givePermissionTo('edit users');


    }
}
