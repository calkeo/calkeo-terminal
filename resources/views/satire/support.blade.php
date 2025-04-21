@extends('satire.layouts.app')

@section('title', 'Support')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-900">Support Ticket System</h1>
        <p class="mt-1 text-sm text-gray-500">We're here to help (or at least pretend to be)</p>
    </div>

    <div class="border-t border-gray-200">
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Support Status</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <span class="status-badge status-open">Open for Business</span> (Mostly)
            </div>
        </div>
        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Response Time</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Between 5 minutes and 5 business days</div>
        </div>
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Support Team</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">1 highly trained support agent (and their
                coffee
                machine)</div>
        </div>
    </div>

    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <h2 class="text-lg font-medium text-gray-900">Recent Tickets</h2>
                <p class="mt-1 text-sm text-gray-500">Here are some of our most recent support tickets (they're all
                    fake)</p>

                <div class="mt-4 space-y-4" id="ticket-container">
                    <!-- Ticket 1 -->
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <div class="flex items-center">
                                <span class="status-badge status-open">Open</span>
                                <span class="ml-2 ticket-id">#TICKET-42</span>
                            </div>
                            <span class="ticket-time">2 minutes ago</span>
                        </div>
                        <div class="ticket-body">
                            <h3 class="ticket-title">Terminal is not working as expected</h3>
                            <p class="ticket-description">
                                I tried to use the terminal but it's not doing what I want. I typed 'help' and it showed
                                me a list of
                                commands, but I don't understand any of them. Please help!
                            </p>
                            <div class="ticket-meta mt-2">
                                <div class="ticket-meta-item">
                                    <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>User</span>
                                </div>
                                <div class="ticket-meta-item">
                                    <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Today</span>
                                </div>
                            </div>

                            <div class="response">
                                <div class="flex items-center">
                                    <div class="agent-avatar">S</div>
                                    <div class="ml-2">
                                        <div class="font-medium">Support Agent</div>
                                        <div class="text-xs text-gray-500">Online</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    Have you tried turning it off and on again? If that doesn't work, try turning it off
                                    and on again
                                    twice. If it still doesn't work, try turning it off and on again three times. If it
                                    still doesn't
                                    work, please submit a new ticket.
                                </div>
                            </div>
                        </div>
                        <div class="ticket-footer">
                            <button class="btn btn-secondary">Close Ticket</button>
                            <button class="btn btn-primary">Reply</button>
                        </div>
                    </div>

                    <!-- Ticket 2 -->
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <div class="flex items-center">
                                <span class="status-badge status-in-progress">In Progress</span>
                                <span class="ml-2 ticket-id">#TICKET-41</span>
                            </div>
                            <span class="ticket-time">15 minutes ago</span>
                        </div>
                        <div class="ticket-body">
                            <h3 class="ticket-title">Coffee command not working</h3>
                            <p class="ticket-description">
                                I tried to use the 'coffee' command but it's not brewing any coffee. I've tried all the
                                parameters:
                                --type=espresso, --shots=3, --sugar=none. Nothing works. Please help, I'm getting
                                desperate!
                            </p>
                            <div class="ticket-meta mt-2">
                                <div class="ticket-meta-item">
                                    <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>User</span>
                                </div>
                                <div class="ticket-meta-item">
                                    <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Today</span>
                                </div>
                            </div>

                            <div class="response">
                                <div class="flex items-center">
                                    <div class="agent-avatar">S</div>
                                    <div class="ml-2">
                                        <div class="font-medium">Support Agent</div>
                                        <div class="text-xs text-gray-500">Online</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    I understand your frustration. The coffee command is currently experiencing issues
                                    due to a shortage
                                    of virtual coffee beans. Our team is working on importing more virtual coffee beans
                                    from the cloud. In
                                    the meantime, you can try the 'tea' command as a temporary alternative.
                                </div>
                            </div>

                            <div class="typing-indicator mt-4">
                                <div class="agent-avatar">S</div>
                                <div class="ml-2">
                                    <div class="font-medium">Support Agent</div>
                                    <div class="text-xs text-gray-500">Typing...</div>
                                </div>
                                <div class="ml-auto flex">
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                    <div class="typing-dot"></div>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-footer">
                            <button class="btn btn-secondary">Close Ticket</button>
                            <button class="btn btn-primary">Reply</button>
                        </div>
                    </div>

                    <!-- Ticket 3 -->
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <div class="flex items-center">
                                <span class="status-badge status-closed">Closed</span>
                                <span class="ml-2 ticket-id">#TICKET-40</span>
                            </div>
                            <span class="ticket-time">1 hour ago</span>
                        </div>
                        <div class="ticket-body">
                            <h3 class="ticket-title">Bug in the bug reporting system</h3>
                            <p class="ticket-description">
                                I found a bug in the bug reporting system. When I try to report a bug, it crashes. How
                                am I supposed to
                                report this bug if I can't use the bug reporting system?
                            </p>
                            <div class="ticket-meta mt-2">
                                <div class="ticket-meta-item">
                                    <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span>User</span>
                                </div>
                                <div class="ticket-meta-item">
                                    <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Today</span>
                                </div>
                            </div>

                            <div class="response">
                                <div class="flex items-center">
                                    <div class="agent-avatar">S</div>
                                    <div class="ml-2">
                                        <div class="font-medium">Support Agent</div>
                                        <div class="text-xs text-gray-500">Online</div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    This is a known issue. We're aware that the bug reporting system has bugs. Our bug
                                    fixers are
                                    currently fixing the bug in the bug reporting system. In the meantime, you can try
                                    reporting the bug
                                    through our alternative bug reporting system, which also has bugs but different
                                    ones.
                                </div>
                            </div>
                        </div>
                        <div class="ticket-footer">
                            <button class="btn btn-secondary">Reopen Ticket</button>
                            <button class="btn btn-primary">Reply</button>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-medium text-gray-900">Submit a New Ticket</h2>
                <p class="mt-1 text-sm text-gray-500">We'll get back to you eventually (maybe)</p>

                <form class="mt-4 space-y-4">
                    <div class="form-group">
                        <label for="ticket-title" class="form-label">Title</label>
                        <input type="text" id="ticket-title" class="form-input"
                            placeholder="Brief description of your issue">
                    </div>

                    <div class="form-group">
                        <label for="ticket-description" class="form-label">Description</label>
                        <textarea id="ticket-description" class="form-textarea"
                            placeholder="Please provide as much detail as possible"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="ticket-priority" class="form-label">Priority</label>
                        <select id="ticket-priority" class="form-input">
                            <option value="low">Low (We'll get to it eventually)</option>
                            <option value="medium">Medium (Maybe this week)</option>
                            <option value="high">High (Still maybe this week)</option>
                            <option value="critical">Critical (Definitely this week, probably)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ticket-category" class="form-label">Category</label>
                        <select id="ticket-category" class="form-input">
                            <option value="bug">Bug Report</option>
                            <option value="feature">Feature Request</option>
                            <option value="question">Question</option>
                            <option value="complaint">Complaint</option>
                            <option value="praise">Praise (We like these)</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="ticket-attachments" class="form-label">Attachments</label>
                        <input type="file" id="ticket-attachments" class="form-input" multiple>
                        <p class="mt-1 text-xs text-gray-500">Max file size: 42MB (We don't know why)</p>
                    </div>

                    <div class="form-group">
                        <label class="flex items-center">
                            <input type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600">
                            <span class="ml-2 text-sm text-gray-700">I understand that submitting a ticket does not
                                guarantee a
                                response</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" class="btn btn-secondary mr-2">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Ticket</button>
                    </div>
                </form>

                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900">FAQ</h3>
                    <p class="mt-1 text-sm text-gray-500">Frequently Asked Questions (that we never answer)</p>

                    <div class="mt-4 space-y-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900">How do I reset my password?</h4>
                            <p class="mt-1 text-sm text-gray-700">Have you tried turning it off and on again?</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900">How do I contact support?</h4>
                            <p class="mt-1 text-sm text-gray-700">You're already here, aren't you?</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900">Is there a way to get faster support?</h4>
                            <p class="mt-1 text-sm text-gray-700">Yes, try submitting your ticket yesterday.</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900">Do you offer 24/7 support?</h4>
                            <p class="mt-1 text-sm text-gray-700">Yes, but our support agents are only available during
                                business
                                hours.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_text')
Support tickets are processed in the order they are received (or in a completely random order, we're not really sure)
@endsection

@section('scripts')
<script>
    // Add some interactivity to the support page
    document.addEventListener('DOMContentLoaded', function() {
        // Generate random tickets periodically
        setInterval(function() {
            const ticketContainer = document.getElementById('ticket-container');
            const ticketTypes = ['open', 'in-progress', 'closed', 'pending'];
            const randomType = ticketTypes[Math.floor(Math.random() * ticketTypes.length)];

            const ticketTitles = [
                'Terminal is not responding to commands',
                'Help command is not helpful',
                'Clear command made everything disappear',
                'Exit command is not working, I\'m trapped',
                'Coffee command is out of coffee',
                'Motivate command is demotivating',
                'Terminal is too terminal-like',
                'Terminal is not terminal-like enough',
                'I can\'t find the any key',
                'My terminal has a virus (it\'s showing ads)',
                'Terminal is asking for my credit card',
                'Terminal is speaking a different language',
                'Terminal is judging me',
                'Terminal is making fun of my code',
                'Terminal is too smart for its own good'
            ];

            const ticketDescriptions = [
                'I typed a command and nothing happened. I waited for 5 whole seconds!',
                'The help command just showed me a list of commands I don\'t understand. How is that helpful?',
                'I used the clear command and now I can\'t see anything. Please help!',
                'I tried to exit the terminal but it won\'t let me leave. I\'ve been here for hours!',
                'The coffee command is saying "Out of coffee". This is unacceptable!',
                'I used the motivate command and it told me to "try harder". That\'s not motivating!',
                'The terminal looks too much like a terminal. Can you make it look more like a spaceship?',
                'The terminal doesn\'t look enough like a terminal. Can you make it look more like a terminal?',
                'The error message says "Press any key to continue" but I can\'t find the any key!',
                'My terminal is showing ads for weight loss pills. I didn\'t install any adware!',
                'The terminal is asking for my credit card number. Is this normal?',
                'The terminal is displaying everything in what appears to be Klingon. How do I change it back?',
                'The terminal keeps rolling its eyes at my commands. It\'s very rude!',
                'The terminal laughed at my code and called it "amateur hour". This is harassment!',
                'The terminal solved my problem before I could finish describing it. It\'s showing off!'
            ];

            const randomTitle = ticketTitles[Math.floor(Math.random() * ticketTitles.length)];
            const randomDescription = ticketDescriptions[Math.floor(Math.random() * ticketDescriptions.length)];

            const ticketId = Math.floor(Math.random() * 1000) + 1;

            const ticket = document.createElement('div');
            ticket.className = 'ticket-card new-ticket';
            ticket.innerHTML = `
                <div class="ticket-header">
                    <div class="flex items-center">
                        <span class="status-badge status-${randomType}">${randomType.charAt(0).toUpperCase() + randomType.slice(1)}</span>
                        <span class="ml-2 ticket-id">#TICKET-${ticketId}</span>
                    </div>
                    <span class="ticket-time">Just now</span>
                </div>
                <div class="ticket-body">
                    <h3 class="ticket-title">${randomTitle}</h3>
                    <p class="ticket-description">
                        ${randomDescription}
                    </p>
                    <div class="ticket-meta mt-2">
                        <div class="ticket-meta-item">
                            <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>User</span>
                        </div>
                        <div class="ticket-meta-item">
                            <svg class="h-4 w-4 ticket-meta-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Today</span>
                        </div>
                    </div>
                </div>
                <div class="ticket-footer">
                    <button class="btn btn-secondary">Close Ticket</button>
                    <button class="btn btn-primary">Reply</button>
                </div>
            `;

            ticketContainer.insertBefore(ticket, ticketContainer.firstChild);

            // Remove old tickets if there are too many
            if (ticketContainer.children.length > 5) {
                ticketContainer.removeChild(ticketContainer.lastChild);
            }
        }, 15000);

        // Simulate typing indicator
        const typingIndicators = document.querySelectorAll('.typing-indicator');
        typingIndicators.forEach(indicator => {
            setTimeout(() => {
                indicator.style.display = 'none';

                const response = document.createElement('div');
                response.className = 'response';
                response.innerHTML = `
                    <div class="flex items-center">
                        <div class="agent-avatar">S</div>
                        <div class="ml-2">
                            <div class="font-medium">Support Agent</div>
                            <div class="text-xs text-gray-500">Online</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        Have you tried turning it off and on again? If that doesn't work, try turning it off and on again twice. If it still doesn't work, try turning it off and on again three times. If it still doesn't work, please submit a new ticket.
                    </div>
                `;

                indicator.parentNode.insertBefore(response, indicator.nextSibling);
            }, 3000);
        });
    });
</script>
@endsection