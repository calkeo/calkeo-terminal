import './bootstrap';

function focusTerminalInput() {
    setTimeout(() => {
        document.getElementById('terminal-input').focus();
    }, 100);
}

document.addEventListener('livewire:initialized', () => {
    focusTerminalInput();

    Livewire.on('focus-password', () => {
        document.querySelector('input[type="password"]').focus();
    });

    Livewire.on('focusInput', focusTerminalInput);

    let component = document.getElementById('terminalRoot');
    let alpineData = Alpine.$data(component);

    Livewire.hook('request', ({ url, options, payload, respond, succeed, fail }) => {
        // Parse the payload to check if it's an executeCommand call
        try {
            const payloadObj = JSON.parse(payload);
            if (payloadObj.components && payloadObj.components.length > 0) {
                const component = payloadObj.components[0];
                if (component.calls && component.calls.length > 0) {
                    const call = component.calls[0];
                    if (call.method === 'executeCommand') {
                        // Set the global variable to true when a command is being executed
                        alpineData.isCommandExecuting = true;
                    }
                }
            }
        } catch (e) {
            console.error('Error parsing payload:', e);
        }

        // Also handle success and failure cases
        succeed(({ status, json }) => {
            alpineData.isCommandExecuting = false;
            focusTerminalInput();
        });

        fail(({ status, content, preventDefault }) => {
            alpineData.isCommandExecuting = false;
            focusTerminalInput();
        });
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
