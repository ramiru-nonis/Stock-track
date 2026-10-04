<nav x-data="{ open: false }" class="bg-white dark:bg-[#1F2A3A] border-b border-[#E2E8F0] dark:border-slate-700/60 shadow-sm">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="font-black text-xl text-indigo-600 dark:text-indigo-400 tracking-tight flex items-center">
                        <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        StockTracker
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index')">
                        {{ __('Products') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.index')">
                        {{ __('Categories') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('stock.in') }}" :active="request()->routeIs('stock.in')">
                        {{ __('Stock In') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('stock.out') }}" :active="request()->routeIs('stock.out')">
                        {{ __('Stock Out') }}
                    </x-nav-link>
                    <x-nav-link href="{{ route('low-stock') }}" :active="request()->routeIs('low-stock')">
                        {{ __('Low Stock') }}
                    </x-nav-link>

                    @if(Auth::user()->isOwner())
                        <x-nav-link href="{{ route('stock.history') }}" :active="request()->routeIs('stock.history')">
                            {{ __('Stock History') }}
                        </x-nav-link>
                        <x-nav-link href="{{ route('staff.index') }}" :active="request()->routeIs('staff.index')">
                            {{ __('Staff Management') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    {{ Auth::user()->name }}
                                    <span class="ms-2 px-2 py-0.5 text-xs rounded-full font-bold uppercase {{ Auth::user()->isOwner() ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ Auth::user()->role }}
                                    </span>
                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Account Management -->
                            <div class="block px-4 py-2 text-xs text-gray-400">
                                {{ __('Manage Account') }}
                            </div>

                            <x-dropdown-link href="{{ route('profile.show') }}">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <div class="border-t border-gray-200 dark:border-gray-700"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}" x-data>
                                @csrf
                                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('products.index') }}" :active="request()->routeIs('products.index')">
                {{ __('Products') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('categories.index') }}" :active="request()->routeIs('categories.index')">
                {{ __('Categories') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('stock.in') }}" :active="request()->routeIs('stock.in')">
                {{ __('Stock In') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('stock.out') }}" :active="request()->routeIs('stock.out')">
                {{ __('Stock Out') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="{{ route('low-stock') }}" :active="request()->routeIs('low-stock')">
                {{ __('Low Stock') }}
            </x-responsive-nav-link>

            @if(Auth::user()->isOwner())
                <x-responsive-nav-link href="{{ route('stock.history') }}" :active="request()->routeIs('stock.history')">
                    {{ __('Stock History') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('staff.index') }}" :active="request()->routeIs('staff.index')">
                    {{ __('Staff Management') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center px-4">
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-white">{{ Auth::user()->name }} ({{ strtoupper(Auth::user()->role) }})</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link href="{{ route('profile.show') }}">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
