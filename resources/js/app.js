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
        // Use requestAnimationFrame to ensure DOM is fully updated
        requestAnimationFrame(() => {
            // Force a reflow to ensure scrollHeight is accurate
            terminalContainer.scrollTop = 0;
            terminalContainer.scrollTop = terminalContainer.scrollHeight;
        });
    }
}

// Scroll to bottom when the component updates
document.addEventListener('livewire:update', () => {
    setTimeout(scrollToBottom, 0);
});

// Also scroll after any DOM changes
document.addEventListener('DOMContentLoaded', function() {
    // Initial scroll
    scrollToBottom();

    // Set up a MutationObserver to watch for changes in the terminal output
    const terminalContent = document.getElementById('terminal-output');
    if (terminalContent) {
        const observer = new MutationObserver(() => {
            setTimeout(scrollToBottom, 0);
        });
        observer.observe(terminalContent, { childList: true, subtree: true });
    }

    // Also scroll after a short delay to ensure content is rendered
    setTimeout(scrollToBottom, 500);
});

// Scroll after any Livewire event
document.addEventListener('livewire:load', () => setTimeout(scrollToBottom, 0));
document.addEventListener('livewire:navigated', () => setTimeout(scrollToBottom, 0));
document.addEventListener('livewire:initialized', () => setTimeout(scrollToBottom, 0));
