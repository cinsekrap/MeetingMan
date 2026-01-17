<nav x-data="{ open: false }" class="brand-gradient">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('people.index') }}" class="flex items-center">
                        @if($logoUrl = \App\Models\SiteSetting::logoUrl())
                            <img src="{{ $logoUrl }}" alt="{{ \App\Models\SiteSetting::siteName() }}" class="h-8 max-w-[180px] object-contain">
                        @else
                            <span class="text-white font-bold text-xl">{{ \App\Models\SiteSetting::siteName() }}</span>
                        @endif
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-white text-white' : 'border-transparent text-white/70 hover:text-white hover:border-white/50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('people.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('people.*') || request()->routeIs('meetings.*') ? 'border-white text-white' : 'border-transparent text-white/70 hover:text-white hover:border-white/50' }}">
                        People
                    </a>
                    <a href="{{ route('actions.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('actions.*') || request()->routeIs('people.actions.*') ? 'border-white text-white' : 'border-transparent text-white/70 hover:text-white hover:border-white/50' }}">
                        Actions
                    </a>
                    <a href="{{ route('objectives.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('objectives.*') || request()->routeIs('people.objectives.*') ? 'border-white text-white' : 'border-transparent text-white/70 hover:text-white hover:border-white/50' }}">
                        Objectives
                    </a>
                    @if(Auth::user()->isSuperAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out {{ request()->routeIs('admin.*') ? 'border-white text-white' : 'border-transparent text-white/70 hover:text-white hover:border-white/50' }}">
                            Admin
                        </a>
                    @endif
                </div>
            </div>

            <!-- Right side: Company Switcher + User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">
                <!-- Company Switcher (icon only) -->
                @if(Auth::user()->companies->count() > 0)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center justify-center px-2.5 py-2 border border-white/30 text-sm leading-4 font-medium rounded-md text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150" title="{{ Auth::user()->currentCompany()?->name ?? 'Select Company' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs font-medium text-gray-400 uppercase tracking-wider">Switch Company</div>
                            @foreach(Auth::user()->companies as $company)
                                @if($company->id === Auth::user()->currentCompany()?->id)
                                    <div class="block px-4 py-2 text-sm text-gray-700 bg-gray-50 font-medium">
                                        {{ $company->name }}
                                        <span class="text-xs text-gray-500">(current)</span>
                                    </div>
                                @else
                                    <form method="POST" action="{{ route('company.switch', $company) }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-start px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            {{ $company->name }}
                                        </button>
                                    </form>
                                @endif
                            @endforeach
                            <div class="border-t border-gray-100"></div>
                            <a href="{{ route('company.create') }}" class="block px-4 py-2 text-sm text-primary-600 hover:bg-gray-100">
                                + Create new company
                            </a>
                            @if(Auth::user()->currentCompany() && Auth::user()->isCompanyAdmin(Auth::user()->currentCompany()))
                                <div class="px-3 py-2 border-t border-gray-100">
                                    <a href="{{ route('company.settings') }}" class="block w-full px-3 py-2 text-sm font-medium text-center text-white bg-purple-600 rounded-md hover:bg-purple-700">
                                        Company Settings
                                    </a>
                                </div>
                            @endif
                        </x-slot>
                    </x-dropdown>
                @endif

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-white/30 text-sm leading-4 font-medium rounded-md text-white bg-white/10 hover:bg-white/20 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <x-dropdown-link :href="route('settings.index')">
                            {{ __('Settings') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white/70 hover:text-white hover:bg-white/10 focus:outline-none focus:bg-white/10 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
            <a href="{{ route('dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('dashboard') ? 'border-white text-white bg-white/10' : 'border-transparent text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50' }}">
                Dashboard
            </a>
            <a href="{{ route('people.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('people.*') || request()->routeIs('meetings.*') ? 'border-white text-white bg-white/10' : 'border-transparent text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50' }}">
                People
            </a>
            <a href="{{ route('actions.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('actions.*') || request()->routeIs('people.actions.*') ? 'border-white text-white bg-white/10' : 'border-transparent text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50' }}">
                Actions
            </a>
            <a href="{{ route('objectives.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('objectives.*') || request()->routeIs('people.objectives.*') ? 'border-white text-white bg-white/10' : 'border-transparent text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50' }}">
                Objectives
            </a>
            @if(Auth::user()->isSuperAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out {{ request()->routeIs('admin.*') ? 'border-white text-white bg-white/10' : 'border-transparent text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50' }}">
                    Admin
                </a>
            @endif
        </div>

        <!-- Responsive Company Switcher -->
        @if(Auth::user()->companies->count() > 0)
            <div class="pt-4 pb-2 border-t border-white/20">
                <div class="px-4 mb-2">
                    <div class="font-medium text-xs text-white/50 uppercase tracking-wider">Company</div>
                </div>
                @foreach(Auth::user()->companies as $company)
                    @if($company->id === Auth::user()->currentCompany()?->id)
                        <div class="block w-full ps-3 pe-4 py-2 border-l-4 border-white text-start text-base font-medium text-white bg-white/10">
                            {{ $company->name }}
                        </div>
                    @else
                        <form method="POST" action="{{ route('company.switch', $company) }}">
                            @csrf
                            <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50 transition duration-150 ease-in-out">
                                {{ $company->name }}
                            </button>
                        </form>
                    @endif
                @endforeach
                @if(Auth::user()->currentCompany() && Auth::user()->isCompanyAdmin(Auth::user()->currentCompany()))
                    <div class="px-3 py-2 border-t border-white/20">
                        <a href="{{ route('company.settings') }}" class="block w-full px-3 py-2 text-sm font-medium text-center text-white bg-purple-600 rounded-md hover:bg-purple-700">
                            Company Settings
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/20">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-white/70">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50 transition duration-150 ease-in-out">
                    Profile
                </a>

                <a href="{{ route('settings.index') }}" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50 transition duration-150 ease-in-out">
                    Settings
                </a>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-white/70 hover:text-white hover:bg-white/5 hover:border-white/50 transition duration-150 ease-in-out">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
