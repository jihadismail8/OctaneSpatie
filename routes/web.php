<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Devices;

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/dashboard', function () {return view('dashboard');})->name('dashboard');
    Route::get('/', function () {redirect('dashboard');});
    Route::get('/users', App\Livewire\ShowUsers::class)->name('users');
    Route::get('/users/add', App\Livewire\CreateUser::class)->name('adduser');
    Route::get('/users/edit/{user}', App\Livewire\EditUsers::class);
    Route::get('/Roles', App\Livewire\ShowRoles::class)->name('roles');
    Route::get('/Roles/add', App\Livewire\CreateRoles::class)->name('addrole');
    Route::get('/Roles/edit/{role}', App\Livewire\EditRoles::class);
    Route::get('/Permissions', App\Livewire\ShowPermissions::class)->name('permissions');
    Route::get('/Permissions/add', App\Livewire\CreatePermissions::class)->name('addpermissions');
    Route::get('/Permissions/{permission}', App\Livewire\EditPermissions::class);

});

