<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Login extends Component
{
    public $username = '';
    public $error = '';
    public $tagline = '';
    public $isTyping = false;
    public $isAnimating = false;
    public $animationStep = 0;
    public $animationText = '';
    public $selectedTheme = '';

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
        if (empty($this->username)) {
            $this->error = 'Username is required';
            return;
        }

        $this->isAnimating = true;
        $this->startLoginAnimation();
    }

    public function startLoginAnimation()
    {
        $this->animationStep = 0;
        $this->animationText = '';
        $loginSets = $this->getLoginSets();
        $this->selectedTheme = array_rand($loginSets);
        $this->dispatch('start-login-animation');
    }

    protected function getLoginSets()
    {
        return config('terminal.login_animations', []);
    }

    public function nextAnimationStep()
    {
        $loginSets = $this->getLoginSets();
        $steps = $loginSets[$this->selectedTheme];

        if ($this->animationStep < count($steps)) {
            $this->animationText = str_replace('__USERNAME__', $this->username, $steps[$this->animationStep]);
            $this->animationStep++;

            if ($this->animationStep === count($steps)) {
                // Store login state and username in session
                session(['terminal_logged_in' => true, 'terminal_username' => $this->username]);
                // Add a small delay before redirect to ensure the final message is displayed
                $this->dispatch('final-step');
                return;
            }

            $this->dispatch('next-animation-step');
        }
    }

    public function viewPlainText()
    {
        Session::put('view_plain_text', true);
        return redirect()->to('/');
    }

    public function updatedUsername()
    {
        $this->isTyping = true;
        $this->error = '';
    }

    public function redirectToTerminal()
    {
        return $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.login');
    }
}
