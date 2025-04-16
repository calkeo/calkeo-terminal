<?php

use App\Livewire\Login;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Route;

Route::get('/', Login::class);
Route::get('/terminal', Terminal::class);
