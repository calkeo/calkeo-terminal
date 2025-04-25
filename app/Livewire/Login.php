<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $error = '';
    public $tagline = '';

    public function mount()
    {
        if (session('terminal_logged_in')) {
            return $this->redirect('/');
        }

        $taglines = config('terminal.taglines', []);
        $this->tagline = !empty($taglines) ? $taglines[array_rand($taglines)] : '';
    }

    public function login()
    {
        // Simple validation
        if (empty($this->username) || empty($this->password)) {
            $this->error = 'Username and password are required';
            return;
        }

        // For demo purposes, accept any non-empty username/password
        if (!empty($this->username) && !empty($this->password)) {
            // Store login state and username in session
            session(['terminal_logged_in' => true, 'terminal_username' => $this->username]);
            return $this->redirect('/');
        }

        $this->error = 'Invalid username or password';
    }

    public function focusPassword()
    {
        $this->dispatch('focus-password');
    }

    public function viewPlainText()
    {
        Session::put('view_plain_text', true);
        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.login');
    }
}
