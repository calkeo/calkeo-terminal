<?php

namespace App\Livewire;

use Livewire\Component;

class Login extends Component
{
    public $username = '';
    public $password = '';
    public $error = '';

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
            return $this->redirect('/terminal');
        }

        $this->error = 'Invalid username or password';
    }

    public function focusPassword()
    {
        $this->dispatch('focus-password');
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.app');
    }
}