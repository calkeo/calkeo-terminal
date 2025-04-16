import './bootstrap';

document.addEventListener('livewire:initialized', () => {
    Livewire.on('focus-password', () => {
        document.querySelector('input[type="password"]').focus();
    });
});
