<nav class="bg-[#0f172a] border-r border-slate-800 w-64 min-h-screen flex flex-col shadow-2xl">
    <div class="flex flex-col h-full">
        <div class="shrink-0 flex items-center px-6 py-8">
            <a href="{{ route('dashboard') }}" class="transition-transform duration-300 hover:scale-105">
                 <h1 class="text-4xl font-black tracking-tighter transition-all duration-300 hover:scale-105" 
                        style="
                            background: linear-gradient(to right, #818cf8, #60a5fa, #22d3ee);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            display: block;
                            filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.2)) drop-shadow(0 5px 10px rgba(0,0,0,0.9));
                        ">
                        FinTrack
                </h1>
            </a>
        </div>

        <div class="flex-1 px-4 space-y-2">
            <p class="px-4 text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-4">Main Menu</p>
            
            <a href="{{ route('dashboard') }}" 
               class="group flex items-center px-4 py-3 text-sm font-semibold rounded-xl transition-all duration-200 
               {{ request()->routeIs('dashboard') 
                  ? 'bg-gradient-to-r from-[#6366f1]/20 to-[#a855f7]/20 text-indigo-400 border border-indigo-500/30' 
                  : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200' }}">
                
                {{-- Dashboard Icon --}}
                <svg class="w-5 h-4 me-3 {{ request()->routeIs('dashboard') ? 'text-indigo-400' : 'text-slate-500 group-hover:text-slate-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                
                {{ __('Dashboard') }}

                @if(request()->routeIs('dashboard'))
                    <span class="ms-auto w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.6)]"></span>
                @endif
            </a>
        </div>

        <div class="px-4 py-6 border-t border-slate-800 bg-slate-900/50">
            <x-dropdown align="top" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center w-full px-3 py-2 text-sm font-medium text-slate-300 rounded-xl hover:bg-slate-800 transition duration-150 ease-in-out focus:outline-none">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#818cf8] to-[#a855f7] flex items-center justify-center text-white font-bold me-3">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="text-start flex-1 overflow-hidden">
                            <div class="truncate text-xs font-bold text-slate-200">{{ Auth::user()->name }}</div>
                            <div class="truncate text-[10px] text-slate-500 uppercase tracking-tighter">Premium User</div>
                        </div>
                        <svg class="ms-2 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4 4 4-4"/>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <div class="px-4 py-2 text-[10px] font-bold text-slate-500 uppercase tracking-widest border-b border-slate-100 dark:border-slate-700 mb-1">
                        Manage Account
                    </div>
                    
                    <x-dropdown-link :href="route('profile.edit')" class="text-sm">
                        {{ __('Profile Settings') }}
                    </x-dropdown-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="text-sm text-red-500 hover:text-red-600">
                            {{ __('Sign Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>