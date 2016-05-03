<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;
use App\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();


        // Cria as roles (funções) dos usuários
        DB::table('roles')->truncate();

        $roles = array(
            ['name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Administrador Global do Sistema'],
            ['name' => 'venue_owner', 'display_name' => 'Proprietário', 'description' => 'Proprietário de Estabelecimento'],
            ['name' => 'user', 'display_name' => 'Usuário', 'description' => 'Usuário do sistema']

        );

        foreach ($roles as $role)
        {
           Role::create($role);
        }

        // Cria as permissions(permissões)
        DB::table('permissions')->truncate();

        $permissions = array(
            ['name' => 'create-user', 'display_name' => 'Criação de usuários', 'description' => 'Permite criar outros usuários do sistema'],
            ['name' => 'view-menu', 'display_name' => 'Visualizar menus', 'description' => 'Permite visualizar os menus'],
            ['name' => 'create-menu', 'display_name' => 'Criação de menus', 'description' => 'Permite criação de menus'],

        );

        foreach ($permissions as $permission)
        {
           Permission::create($permission);
        }

        //Designa as permissões das funções
		DB::table('permission_role')->truncate();

        $permissions_role = array(
        	['permission' => 'create-user', 'role' => 'admin'],
        	['permission' => 'create-menu', 'role' => 'admin'],
        	['permission' => 'create-menu', 'role' => 'venue_owner'],
        	['permission' => 'view-menu', 'role' => 'venue_owner'],
        	['permission' => 'view-menu', 'role' => 'user'],


        );

        foreach ($permissions_role as $permission_role) {
        	$permission = Permission::where('name', '=', $permission_role['permission'])->first();
        	$role = Role::where('name', '=', $permission_role['role'])->first();
        	$role->attachPermission($permission);	
        }
        
        //Cria os usuários
        DB::table('users')->truncate();

        $users = array(
            ['name' => 'Administrador', 'email' => 'admin@digimenu', 'password' => Hash::make('admin')],
            ['name' => 'Proprietário', 'email' => 'proprietario@digimenu', 'password' => Hash::make('proprietario')],
            ['name' => 'Usuário', 'email' => 'usuario@digimenu', 'password' => Hash::make('usuario')]
        );

        foreach ($users as $user)
        {
            User::create($user);
        }

        //Designa as funções dos usuários
        DB::table('role_user')->truncate();

        $role_users = array(
        	['role' => 'admin', 'email' => 'admin@digimenu'],
        	['role' => 'venue_owner', 'email' => 'proprietario@digimenu'],
        	['role' => 'user', 'email' => 'usuario@digimenu'],

        );

        foreach ($role_users as $role_user) {
        	$role = Role::where('name', '=', $role_user['role'])->first();
        	$user = User::where('email', '=', $role_user['email'])->first();
            $user->roles()->attach($role->id);	
        }

        
        Model::reguard();
    }
}
