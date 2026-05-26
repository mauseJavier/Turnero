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

        User::create([
            'name' => 'Gimenez Marcelo',
            'email' => 'marce_nqn_19@hotmail.com',
            'password' => Hash::make('12345678'),
        ]);


        // Crear roles
        Role::create(['name' => 'super']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        
        // Crear permisos
        Permission::create(['name' => 'editar']);
        Permission::create(['name' => 'ver']);
        Permission::create(['name' => 'borrar']);
        Permission::create(['name' => 'crear']);

        // Asignar roles y permisos al usuario
        $user = User::find(1);
        $user->assignRole('super');
        $user->givePermissionTo('editar', 'ver', 'borrar', 'crear');

                // Asignar roles y permisos al usuario
        $user = User::find(2);
        $user->assignRole('super');
        $user->givePermissionTo('editar', 'ver', 'borrar', 'crear');


    }
}
