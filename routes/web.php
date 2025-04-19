<?php

use App\Livewire\Login;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class);
Route::get('/', Terminal::class);