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

    /* Custom Scrollbar for Chat Window */
    .chat-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .chat-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .chat-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    .chat-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

@section('content')
<div class="page-bg-custom">
    <div class="py-6 px-4">
        
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('staff.client-messages') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                ←
            </a>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-sky-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr($otherUser->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white leading-tight">Chat with {{ $otherUser->name }}</h1>
                    <p class="text-xs text-emerald-400 font-medium flex items-center gap-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Active Conversation
                    </p>
                </div>
            </div>
        </div>

        {{-- Main Chat Panel --}}
        <div class="max-w-4xl mx-auto rounded-3xl bg-black shadow-2xl ring-1 ring-slate-200/70 overflow-hidden border border-slate-100">
            
            {{-- Message Area --}}
            <div class="relative">
                {{-- Floating Scroll Button --}}
                <button id="scrollToBottomBtn" 
                        onclick="scrollToBottom()"
                        class="hidden absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-xs font-bold text-white shadow-2xl hover:bg-blue-700 transition-all z-50 border border-white/20">
                    <span class="animate-bounce">↓</span>
                    <span>New messages below</span>
                </button>

                <div id="chat-window" class="h-[500px] overflow-y-auto p-6 space-y-6 bg-slate-50/50 chat-scrollbar">
                    @forelse($messages as $message)
                        @php $isMe = $message->sender_id === auth()->id(); @endphp
                        
                        <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                            <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }} max-w-[75%]">
                                <div class="rounded-2xl px-4 py-2.5 text-sm shadow-sm {{ $isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-slate-900 border border-slate-200 rounded-tl-none' }}">
                                    <p class="leading-relaxed whitespace-pre-wrap">{{ $message->content }}</p>
                                    
                                    @if($message->attachment_path)
                                        <div class="mt-2 pt-2 border-t {{ $isMe ? 'border-white/20' : 'border-slate-100' }}">
                                            <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="flex items-center gap-2 text-xs font-bold {{ $isMe ? 'text-blue-100 hover:text-white' : 'text-blue-600 hover:text-blue-800' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                {{ $message->attachment_name ?? 'View Attachment' }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <span class="mt-1 text-[10px] text-slate-400">
                                    {{ $message->created_at->format('g:i A') }} • {{ $message->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col h-full items-center justify-center text-slate-400 space-y-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <p class="italic">No message history with this client.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Input Area --}}
            <div class="p-4 bg-white border-t border-slate-100">
                <form action="{{ route('staff.client-messages.send') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <input type="hidden" name="receiver_id" value="{{ $otherUser->id }}">
                    
                    <textarea 
                        name="content" 
                        rows="2" 
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 p-4 text-sm focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all resize-none" 
                        placeholder="Write your reply to {{ $otherUser->name }}..."
                        required
                    ></textarea>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <label class="cursor-pointer inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                                <input type="file" name="attachment" class="hidden" onchange="updateFileName(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Attach File
                            </label>
                            <span id="file-name" class="text-[11px] text-slate-400 truncate max-w-[150px]">No file selected</span>
                        </div>

                        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-blue-600 px-8 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chatWindow = document.getElementById('chat-window');
    const scrollBtn = document.getElementById('scrollToBottomBtn');

    // Function to scroll down
    function scrollToBottom() {
        chatWindow.scrollTo({
            top: chatWindow.scrollHeight,
            behavior: 'smooth'
        });
    }

    // Function for file names
    function updateFileName(input) {
        const label = document.getElementById('file-name');
        label.textContent = input.files.length > 0 ? input.files[0].name : "No file selected";
        label.classList.add('text-blue-600', 'font-medium');
    }

    // Monitor scrolling to show/hide button
    chatWindow.addEventListener('scroll', () => {
        const distanceToBottom = chatWindow.scrollHeight - chatWindow.scrollTop - chatWindow.clientHeight;
        
        // Show button if user scrolls up more than 150px
        if (distanceToBottom > 150) {
            scrollBtn.classList.remove('hidden');
        } else {
            scrollBtn.classList.add('hidden');
        }
    });

    // Auto-scroll on load
    window.onload = () => {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    };
</script>
@endsection