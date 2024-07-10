<?php

namespace App\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


use Livewire\Component;

class ShowRoles extends Component
{
    public function render()
    {
        return view('livewire.show-roles',['roles' => Role::where('guard_name','web')->with("Permissions")->get()]);
    }
}
