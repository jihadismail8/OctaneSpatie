<?php

namespace App\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Livewire\Component;

class ShowPermissions extends Component
{
    public function delete(Permission $user)
    {
        $user->delete();
    }
    
    public function render()
    {
        return view('livewire.show-permissions',['permissions' => Permission::where('guard_name','web')->with("Roles")->get()]);
    }
}
