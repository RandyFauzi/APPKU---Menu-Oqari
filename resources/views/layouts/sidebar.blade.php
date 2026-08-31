<div 
    :class="sidebarOpen ? 'w-64' : 'w-20'" 
    class="flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300 relative h-screen z-20"
>
    <!-- Toggle Button (Absolute to hover over edge) -->
    <button 
        @click="sidebarOpen = !sidebarOpen"
        class="absolute -right-3 top-8 bg-white border border-gray-200 dark:bg-gray-800 dark:border-gray-700 rounded-full p-1 shadow-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none"
    >
        <svg x-show="sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        <svg x-show="!sidebarOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </button>

    <!-- Header / Logo -->
    <div class="flex items-center h-20 px-6">
        <div class="flex items-center justify-center bg-gray-900 dark:bg-gray-100 rounded-xl w-10 h-10 shrink-0">
            <div class="w-3 h-3 bg-white dark:bg-gray-900 rounded-full"></div>
        </div>
        <span x-show="sidebarOpen" class="ml-4 font-bold text-xl tracking-tight text-gray-900 dark:text-white transition-opacity duration-300">Brand</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
        <!-- Dashboard -->
        <a href="#" class="flex items-center px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white group">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Dashboard</span>
        </a>

        <!-- Audience -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white group transition-colors">
                <div class="flex items-center">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Audience</span>
                </div>
                <svg x-show="sidebarOpen" :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>

        <!-- Posts -->
        <a href="#" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white group transition-colors">
            <div class="flex items-center">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Posts</span>
            </div>
            <span x-show="sidebarOpen" class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded-md dark:bg-green-900/30 dark:text-green-400">8</span>
        </a>

        <!-- Schedules -->
        <a href="#" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white group transition-colors">
            <div class="flex items-center">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Schedules</span>
            </div>
            <span x-show="sidebarOpen" class="bg-orange-100 text-orange-800 text-xs font-semibold px-2 py-0.5 rounded-md dark:bg-orange-900/30 dark:text-orange-400">+ 3</span>
        </a>

        <!-- Income (Dropdown) -->
        <div x-data="{ open: true }" class="relative">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-gray-900 bg-gray-50 dark:bg-gray-800 dark:text-white group transition-colors">
                <div class="flex items-center">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Income</span>
                </div>
                <svg x-show="sidebarOpen" :class="{'rotate-180': !open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
            </button>
            
            <!-- Sub-menu (Only visible when sidebar is open) -->
            <div x-show="open && sidebarOpen" x-collapse class="pl-10 pr-3 py-1 space-y-1">
                <a href="#" class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">Earnings</a>
                <a href="#" class="flex justify-between items-center px-3 py-2 rounded-lg text-sm text-gray-900 bg-gray-50 dark:bg-gray-800 dark:text-white font-medium transition-colors">
                    Refunds
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                <a href="#" class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">Declines</a>
                <a href="#" class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white transition-colors">Payouts</a>
            </div>

            <!-- Flyout for collapsed state -->
            <div x-show="!sidebarOpen" class="absolute left-full top-0 ml-2 hidden group-hover:block z-50">
                <div class="bg-gray-900 text-white text-xs rounded px-2 py-1 mb-1 shadow-lg whitespace-nowrap">Income</div>
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl shadow-xl w-40 py-2">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white">Earnings</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-900 bg-gray-50 dark:bg-gray-700 dark:text-white font-medium">Refunds</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white">Declines</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-gray-900 dark:hover:text-white">Payouts</a>
                </div>
            </div>
        </div>

        <!-- Promote -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800 dark:hover:text-white group transition-colors">
                <div class="flex items-center">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    <span x-show="sidebarOpen" class="ml-3 font-medium text-sm">Promote</span>
                </div>
                <svg x-show="sidebarOpen" :class="{'rotate-180': open}" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
        </div>
    </nav>

    <!-- Bottom Actions -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        <!-- Upload Box -->
        <div x-show="sidebarOpen" class="border border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-4 flex flex-col items-center justify-center text-center mb-4 bg-gray-50 dark:bg-gray-800/50">
            <button class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-2 mb-2 shadow-sm shadow-blue-500/30 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
            <span class="text-sm font-semibold text-gray-900 dark:text-white">Upload new image</span>
            <span class="text-xs text-gray-400 mt-1">Drag and drop</span>
        </div>

        <!-- Upload Button (Collapsed) -->
        <div x-show="!sidebarOpen" class="flex justify-center mb-4">
            <button class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-3 shadow-sm shadow-blue-500/30 transition-colors" title="Upload new image">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </button>
        </div>

        <!-- Light / Dark Toggle -->
        <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-1 flex relative">
            <button @click="darkMode = false" :class="!darkMode ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'" class="flex-1 flex items-center justify-center py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-1.5" x-show="sidebarOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg class="w-5 h-5" x-show="!sidebarOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-show="sidebarOpen">Light</span>
            </button>
            <button @click="darkMode = true" :class="darkMode ? 'bg-white dark:bg-gray-700 shadow-sm text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400'" class="flex-1 flex items-center justify-center py-2 rounded-lg text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-1.5" x-show="sidebarOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <span x-show="sidebarOpen">Dark</span>
            </button>
        </div>
    </div>
</div>
