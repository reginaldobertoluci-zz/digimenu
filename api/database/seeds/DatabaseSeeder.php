<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;
use App\Permission;
use App\Venue;
use App\Menu;
use App\Section;
use App\Item;

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
            ['name' => 'owner', 'display_name' => 'Owner', 'description' => 'Administrador Global do Sistema'],
            ['name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Administrador do Sistema'],
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
            ['name' => 'Owner', 'email' => 'owner@digimenu', 'password' => Hash::make('owner')],
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

        // Cria estabelecimentos de exemplo
        DB::table('venues')->truncate();

        $venues = array(
            ['name' => 'Estabelecimento Teste']

        );

        foreach ($venues as $venue)
        {
           Venue::create($venue);
        }

        // Designa os usuários do estabelecimento
        DB::table('venue_user')->truncate();

        $venue_users = array(
        	['venue' => 'Estabelecimento Teste', 'email' => 'proprietario@digimenu']
        );

        foreach ($venue_users as $venue_user) {
        	$venue = Venue::where('name', '=', $venue_user['venue'])->first();
        	$user = User::where('email', '=', $venue_user['email'])->first();
        	$user->venues()->save($venue);
        }

 		// Cria menus de exemplo
        DB::table('menus')->truncate();
        $menus = array(
        	['name' => 'Menu de teste', 'qrcode'=>str_random(16), 'venue' => 'Estabelecimento Teste']
        );

        foreach ($menus as $menu) {
        	$venue = Venue::where('name', '=', $menu['venue'])->first();
        	$menu = Menu::create(array('name'=>$menu['name'], 'qrcode'=>$menu['qrcode']));
        	$menu->venue()->associate($venue);
			$menu->save();

        }

        // Cria seções de exemplo
        DB::table('sections')->truncate();
        $sections = array(
        	['name' => 'Seção de teste 1', 'order' => 1, 'menu' => 'Menu de teste'],
            ['name' => 'Seção de teste 2', 'order' => 1, 'menu' => 'Menu de teste'],
        );

        foreach ($sections as $section) {
        	$menu = Menu::where('name', '=', $section['menu'])->first();
        	$section = Section::create(array('name'=>$section['name'], 'order' => $section['order']));
        	$section->menu()->associate($menu);
			$section->save();

        }

        // Adiciona itens em uma seção
        DB::table('menu_items')->truncate();
        $items = array(
        	['name' => 'Item de teste', 'price'=>10, 'section'=>'Seção de teste 1'],
            ['name' => 'Item de teste 2', 'price'=>11, 'section'=>'Seção de teste 1'],
            ['name' => 'Item de teste 3', 'price'=>12, 'section'=>'Seção de teste 1'],
            ['name' => 'Item de teste', 'price'=>13, 'section'=>'Seção de teste 2'],
            ['name' => 'Item de teste 2', 'price'=>14, 'section'=>'Seção de teste 2'],



        );

        foreach ($items as $item) {
        	$section = Section::where('name', '=', $item['section'])->first();
        	
			$item = Item::create(array('name'=>$item['name'], 'price' => $item['price']));
        	$item->section()->associate($section);
			$item->save();

        }
        
        Model::reguard();
    }
}
