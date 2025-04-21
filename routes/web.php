<?php

use App\Livewire\Login;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class);
Route::get('/', Terminal::class);

// Satirical pages
Route::get('/docs', function () {
    return view('satire.docs');
});

Route::get('/manage', function () {
    return view('satire.manage');
});

Route::get('/support', function () {
    return view('satire.support');
});
