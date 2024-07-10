<?php

namespace App\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Livewire\Component;

class CreatePermissions extends Component
{
    public $name;
    public $roles;
    public $permissions;
    public $selectedroles=[];

    protected $rules = [
        'name'=>'required|unique:roles',
        'selectedroles'=>'required',

    ];

    public function mount()
    {   $this->roles = Role::where('guard_name','web')->get();
    }

    public function save()
    {
        $name = $this->name;
        $Permission = new Permission();
        $Permission->name = $name;
        $Permission->guard_name = "web";
        $roles = $this->selectedroles;
        $Permission->save();
        if (!empty($roles)) {
            foreach ($roles as $role) {
                $r = Role::where('id', '=', $role)->firstOrFail(); //Match input role to db record
                $permission = Permission::where('name', '=', $name)->first(); //Match input //permission to db record
                $r->givePermissionTo($permission);
            }
        }

        return redirect()->to('users');
    }

    public function render()
    {
        return view('livewire.create-permissions');
    }
}
