<?php

namespace App\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Livewire\Component;

class CreateRoles extends Component
{
    public $name;
    public $roles;
    public $permissions;
    public $selectedPermissions=[];

    protected $rules = [
        'name'=>'required|unique:roles',
        'selectedPermissions'=>'required',

    ];

    public function mount()
    {   $this->permissions = Permission::where('guard_name','web')->get();
    }

    public function save()
    {
        $name = $this->name;
        $role = new Role();
        $role->name = $name;
        $role->guard_name = "web";
        $permissions = $this->selectedPermissions;

        $role->save();
        foreach ($permissions as $permission) {
            $p = Permission::where('id', '=', $permission)->firstOrFail(); 
         //Fetch the newly created role and assign permission
            $role = Role::where('name', '=', $name)->first(); 
            $role->givePermissionTo($p);
        }


        return redirect()->to('users');
    }

    public function render()
    {
        return view('livewire.create-roles');
    }
}
