import './bootstrap';

document.addEventListener('livewire:initialized', () => {
    Livewire.on('focus-password', () => {
        document.querySelector('input[type="password"]').focus();
    });

    Livewire.on('focusInput', function () {
        setTimeout(() => {
            document.getElementById('terminal-input').focus();
        }, 100);
    });
});

function scrollToBottom() {
    const terminalContainer = document.getElementById('terminal-container');
    if (terminalContainer) {
        terminalContainer.scrollTop = terminalContainer.scrollHeight;
    }
}

// Scroll to bottom when the component updates
document.addEventListener('livewire:update', scrollToBottom);

// Also scroll after any DOM changes
document.addEventListener('DOMContentLoaded', function() {
    // Initial scroll
    scrollToBottom();

    // Set up a MutationObserver to watch for changes in the terminal output
    const terminalContent = document.getElementById('terminal-output');
    if (terminalContent) {
        const observer = new MutationObserver(scrollToBottom);
        observer.observe(terminalContent, { childList: true, subtree: true });
    }

    // Also scroll after a short delay to ensure content is rendered
    setTimeout(scrollToBottom, 100);
});

// Scroll after any Livewire event
document.addEventListener('livewire:load', scrollToBottom);
document.addEventListener('livewire:navigated', scrollToBottom);
document.addEventListener('livewire:initialized', scrollToBottom);
