<?php

namespace App\Livewire;
use App\Models\User;

use Livewire\Component;

class ShowUsers extends Component
{
    public function delete(User $user)
    {
        $user->delete();
    }
    
    public function render()
    {
        return view('livewire.show-users', ['users' => User::with("roles")->get()]);
    }


}
