<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateUser extends Component
{
    public User $users ;
    public $roles;
    public $permissions;
    public $selectedroles=[];

    protected $rules = [
        'users.name' => 'required|string|min:6',
        'users.email' => 'required|string|max:500|unique:users',
        'users.phone' => 'required|string|max:500|unique:users',
        'users.password' => 'required|string|max:500',
        'selectedroles'=>'',

    ];

    public function mount()
    {   $this->roles = Role::where('guard_name','web')->get();
        $this->users = new User();
    }

    public function save()
    {
        $this->validate();

        $this->users->plain = $this->users->password;
        $this->users->save();
        $this->users->roles()->sync($this->selectedroles); 

        return redirect()->to('/users');
    }

    public function render()
    {
        return view('livewire.create-user');
    }
}