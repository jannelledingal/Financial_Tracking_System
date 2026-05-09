@extends('layouts.staff')
<style>
    .page-bg-custom {
        background-image: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), 
                          url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=2070&auto=format&fit=crop');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        border-radius: 2.5rem;
        margin-top: 1rem;
        padding: 2.5rem;
        min-height: 85vh;
    }
</style>

@section('content')
<div class="page-bg-custom">
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.24em] text-slate-500">Client messages</p>
            <h1 class="mt-2 text-3xl font-semibold text-white">Inbox</h1>
            <p class="mt-2 text-sm text-slate-500">Review communication from your assigned clients.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-emerald-50 p-4 text-emerald-800 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200/70">
            <h3 class="text-lg font-semibold text-slate-950 mb-4">Recent Conversations</h3>
            <div class="space-y-3">
                @forelse($messages as $clientId => $conversationGroup)
                    @php 
                        // Get the latest message to determine client info and preview text
                        $lastMessage = $conversationGroup->first(); 
                        
                        // Identify the client (it's whoever is NOT the logged-in staff)
                        $client = ($lastMessage->sender_id === Auth::id()) 
                                   ? $lastMessage->receiver 
                                   : $lastMessage->sender;
                    @endphp
                    
                    <a href="{{ route('staff.client-messages.conversation', $client->id) }}" 
                       class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 transition border border-slate-100 shadow-sm">
                        
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-sky-500 text-sm font-semibold text-white shrink-0">
                            {{ strtoupper(substr($client->name, 0, 2)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="font-bold text-slate-950 truncate">{{ $client->name }}</p>
                                <p class="text-xs text-slate-400">{{ $lastMessage->created_at->diffForHumans() }}</p>
                            </div>
                            
                            <p class="text-sm text-slate-500 truncate mt-0.5">
                                @if($lastMessage->sender_id === Auth::id())
                                    <span class="text-slate-400 font-medium">You: </span>
                                @endif
                                {{ $lastMessage->content }}
                            </p>
                            
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                    {{ $conversationGroup->count() }} Messages
                                </span>
                                @if(!$lastMessage->is_read && $lastMessage->receiver_id === Auth::id())
                                    <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                                    <span class="text-[10px] text-sky-600 font-bold uppercase">New</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-slate-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-12">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <p class="text-slate-500 font-medium">No conversations found yet.</p>
                        <p class="text-sm text-slate-400">When your assigned clients message you, they will appear here.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="xl:col-span-1 space-y-6">
            <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl">
                <h4 class="font-semibold text-lg mb-2">Messaging Tips</h4>
                <ul class="text-sm text-slate-400 space-y-3">
                    <li class="flex gap-2">
                        <span class="text-sky-400">●</span>
                        Respond to client inquiries within 24 hours.
                    </li>
                    <li class="flex gap-2">
                        <span class="text-sky-400">●</span>
                        You can only see messages from clients assigned to you.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
@endsection