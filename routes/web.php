<?php

use App\Livewire\Login;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class);

Route::get('/', Terminal::class);

// Satirical pages
Route::get('/docs', function () {
    if (request()->attributes->get('is_bot', false)) {
        return abort(404);
    }
    return view('satire.docs');
});

Route::get('/manage', function () {
    if (request()->attributes->get('is_bot', false)) {
        return abort(404);
    }
    return view('satire.manage');
});

Route::get('/support', function () {
    if (request()->attributes->get('is_bot', false)) {
        return abort(404);
    }
    return view('satire.support');
});
