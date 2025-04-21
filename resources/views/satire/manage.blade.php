@extends('satire.layouts.app')

@section('title', 'Management')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-900">Management Dashboard</h1>
        <p class="mt-1 text-sm text-gray-500">Everything is under control (or at least we pretend it is)</p>
    </div>

    <div class="border-t border-gray-200">
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">System Status</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                <span class="status-indicator status-online"></span> All systems operational (we think)
            </div>
        </div>
        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Last Updated</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Just now (or sometime in the last hour)</div>
        </div>
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Dashboard Version</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">2.0.1 (Now with 50% more metrics!)</div>
        </div>
    </div>

    <div class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Productivity Metric -->
            <div class="dashboard-card p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="metric-label">Productivity Level</dt>
                            <dd class="metric-value text-indigo-600">OVER 9000!</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="progress-bar">
                        <div class="progress-fill bg-indigo-600" style="width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Coffee Metric -->
            <div class="dashboard-card p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="metric-label">Coffee Levels</dt>
                            <dd class="metric-value text-amber-600">CRITICAL</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="progress-bar">
                        <div class="progress-fill bg-amber-600" style="width: 15%;"></div>
                    </div>
                </div>
            </div>

            <!-- Bug Count -->
            <div class="dashboard-card p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="metric-label">Bug Count</dt>
                            <dd class="metric-value text-red-600">42,069</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="progress-bar">
                        <div class="progress-fill bg-red-600" style="width: 75%;"></div>
                    </div>
                </div>
            </div>

            <!-- Motivation Level -->
            <div class="dashboard-card p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="metric-label">Motivation Level</dt>
                            <dd class="metric-value text-green-600">HIGH</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="progress-bar">
                        <div class="progress-fill bg-green-600" style="width: 85%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
            <!-- Productivity Chart -->
            <div class="dashboard-card p-5">
                <h3 class="text-lg font-medium text-gray-900">Productivity Over Time</h3>
                <p class="mt-1 text-sm text-gray-500">As measured by coffee consumption</p>

                <div class="chart-container mt-4">
                    <div class="chart-bar" style="left: 10%; height: 60%;"></div>
                    <div class="chart-label" style="left: 10%;">Mon</div>

                    <div class="chart-bar" style="left: 20%; height: 80%;"></div>
                    <div class="chart-label" style="left: 20%;">Tue</div>

                    <div class="chart-bar" style="left: 30%; height: 40%;"></div>
                    <div class="chart-label" style="left: 30%;">Wed</div>

                    <div class="chart-bar" style="left: 40%; height: 90%;"></div>
                    <div class="chart-label" style="left: 40%;">Thu</div>

                    <div class="chart-bar" style="left: 50%; height: 100%;"></div>
                    <div class="chart-label" style="left: 50%;">Fri</div>

                    <div class="chart-bar" style="left: 60%; height: 20%;"></div>
                    <div class="chart-label" style="left: 60%;">Sat</div>

                    <div class="chart-bar" style="left: 70%; height: 10%;"></div>
                    <div class="chart-label" style="left: 70%;">Sun</div>
                </div>
            </div>

            <!-- System Status -->
            <div class="dashboard-card p-5">
                <h3 class="text-lg font-medium text-gray-900">System Status</h3>
                <p class="mt-1 text-sm text-gray-500">All systems are functioning normally (we think)</p>

                <div class="mt-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="status-indicator status-online"></span>
                            <span class="text-sm font-medium text-gray-900">Web Server</span>
                        </div>
                        <span class="text-sm text-gray-500">Uptime: 42 days</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="status-indicator status-online"></span>
                            <span class="text-sm font-medium text-gray-900">Database</span>
                        </div>
                        <span class="text-sm text-gray-500">Uptime: 42 days</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="status-indicator status-warning"></span>
                            <span class="text-sm font-medium text-gray-900">Coffee Machine</span>
                        </div>
                        <span class="text-sm text-gray-500">Status: Low on beans</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="status-indicator status-offline"></span>
                            <span class="text-sm font-medium text-gray-900">Documentation Generator</span>
                        </div>
                        <span class="text-sm text-gray-500">Status: Never worked</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="status-indicator status-unknown"></span>
                            <span class="text-sm font-medium text-gray-900">Bug Fixer</span>
                        </div>
                        <span class="text-sm text-gray-500">Status: Does it exist?</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
            <p class="mt-1 text-sm text-gray-500">What's happening in the system (or what we want you to think is
                happening)</p>

            <div class="mt-4 space-y-2">
                <div class="notification notification-info">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">System is running smoothly (we think). All systems
                                operational (except the ones that aren't).</p>
                            <p class="mt-1 text-xs text-gray-500">Just now</p>
                        </div>
                    </div>
                </div>

                <div class="notification notification-warning">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">Coffee levels are critically low. Please refill the coffee
                                machine immediately.</p>
                            <p class="mt-1 text-xs text-gray-500">Just now</p>
                        </div>
                    </div>
                </div>

                <div class="notification notification-error">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">Critical error: Documentation generator has failed again.
                                No documentation was harmed in this process.</p>
                            <p class="mt-1 text-xs text-gray-500">Just now</p>
                        </div>
                    </div>
                </div>

                <div class="notification notification-success">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-gray-700">Bug fix successful! We've fixed one bug and introduced
                                three new ones.</p>
                            <p class="mt-1 text-xs text-gray-500">Just now</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900">Terminal Commands</h3>
            <p class="mt-1 text-sm text-gray-500">Try these commands in your terminal (they might do something)</p>

            <div class="mt-4 bg-gray-800 rounded-lg p-4 font-mono text-sm text-gray-200">
                <div class="flex">
                    <span class="text-green-400">$</span>
                    <span class="ml-2">productivity --boost</span>
                </div>
                <div class="mt-2 text-gray-400">Boosting productivity... Done! Your productivity has been increased by
                    0%.</div>

                <div class="mt-4 flex">
                    <span class="text-green-400">$</span>
                    <span class="ml-2">coffee --refill</span>
                </div>
                <div class="mt-2 text-gray-400">Refilling coffee machine... Error: Coffee machine not found. Did you
                    mean 'tea'?</div>

                <div class="mt-4 flex">
                    <span class="text-green-400">$</span>
                    <span class="ml-2">bug --fix --all</span>
                </div>
                <div class="mt-2 text-gray-400">Fixing all bugs... This may take a while... <span class="blink">_</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_text')
Dashboard last updated: Just now (or sometime in the last hour)
@endsection

@section('scripts')
<script>
    // Add some interactivity to the dashboard
    document.addEventListener('DOMContentLoaded', function() {
        // Simulate real-time updates
        setInterval(function() {
            // Update a random metric
            const metrics = document.querySelectorAll('.metric-value');
            const randomMetric = metrics[Math.floor(Math.random() * metrics.length)];

            if (randomMetric.textContent.includes('OVER 9000')) {
                randomMetric.textContent = 'OVER 9001!';
            } else if (randomMetric.textContent.includes('CRITICAL')) {
                randomMetric.textContent = 'EMPTY';
            } else if (randomMetric.textContent.includes('42,069')) {
                randomMetric.textContent = '42,070';
            } else if (randomMetric.textContent.includes('HIGH')) {
                randomMetric.textContent = 'VERY HIGH';
            }

            // Add a new notification
            const notifications = document.querySelector('.space-y-2');
            const notificationTypes = ['info', 'warning', 'error', 'success'];
            const randomType = notificationTypes[Math.floor(Math.random() * notificationTypes.length)];

            const messages = [
                'System is running smoothly (we think).',
                'Coffee levels are still critically low.',
                'New bug discovered: The system works perfectly.',
                'Documentation generator is still broken.',
                'Productivity levels are off the charts!',
                'Someone forgot to commit their changes again.',
                'The intern broke the production server.',
                'All systems operational (except the ones that aren\'t).'
            ];

            const randomMessage = messages[Math.floor(Math.random() * messages.length)];

            const notification = document.createElement('div');
            notification.className = `notification notification-${randomType}`;
            notification.innerHTML = `
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-${randomType === 'info' ? 'indigo' : randomType === 'warning' ? 'amber' : randomType === 'error' ? 'red' : 'green'}-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-700">${randomMessage}</p>
                        <p class="mt-1 text-xs text-gray-500">Just now</p>
                    </div>
                </div>
            `;

            notifications.insertBefore(notification, notifications.firstChild);

            // Remove old notifications if there are too many
            if (notifications.children.length > 5) {
                notifications.removeChild(notifications.lastChild);
            }
        }, 10000);
    });
</script>
@endsection