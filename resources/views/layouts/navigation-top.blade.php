<header class="h-16 border-b border-slate-200 bg-slate-800 backdrop-blur-md sticky top-0 z-30">
    <div class="flex h-full items-center justify-between px-8">
        
        <div class="flex-1"></div>

        <div class="flex items-center gap-6">
            
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative rounded-xl p-2 text-slate-500 hover:bg-slate-100 transition-all duration-200">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        <span class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-rose-500 border-2 border-white"></span>
                    @endif
                </button>

                <div x-show="open" @click.away="open = false" x-cloak
                    x-transition:enter="transition ease-out duration-200" 
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    class="absolute right-0 mt-3 w-80 origin-top-right rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200 overflow-hidden z-50">
                    
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-xs font-bold uppercase tracking-widest text-slate-500">Recent Messages</h3>
                    </div>

                    <div class="max-h-[400px] overflow-y-auto">
                        @forelse($recentActivities ?? [] as $activity)
                            @php
                                $link = auth()->user()->role === 'Staff' 
                                    ? route('staff.client-messages.conversation', $activity->sender_id) 
                                    : route('client.messages');
                                
                                $messagePreview = $activity->content ?? $activity->body ?? 'No message text';
                            @endphp
                            
                            <a href="{{ $link }}" class="group block px-6 py-4 hover:bg-blue-50/50 transition-colors border-b border-slate-50 last:border-0">
                                <div class="flex gap-3">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 font-bold text-xs uppercase">
                                        {{ substr($activity->sender->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-slate-900 group-hover:text-blue-700 truncate">
                                            {{ $activity->sender->name ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-slate-500 truncate italic">
                                            "{{ $messagePreview }}"
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-1 uppercase font-bold">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="px-6 py-10 text-center text-slate-400 italic text-xs">
                                No recent messages
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ auth()->user()->role === 'Staff' ? route('staff.client-messages') : route('client.messages') }}" 
                       class="block py-3 text-center bg-slate-50 text-xs font-bold text-blue-600 hover:bg-slate-100 transition-colors uppercase tracking-widest">
                        View all Inbox
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-3 pl-6 border-l border-slate-800">
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-sm font-bold text-white">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ auth()->user()->role }}</p>
                </div>

                <a href="{{ auth()->user()->role === 'Staff' ? route('staff.profile') : route('client.profile') }}" class="group block">
                    <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-black text-sm shadow-lg shadow-blue-900/40 uppercase transition-transform group-hover:scale-105">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                </a>
            </div>
        </div>
    </div>
</header>