@extends('layouts.client')
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
{{-- Container with its own scroll for the whole page --}}
<div class="max-w-2xl mx-auto py-6 px-4 page-scrollbar">
    
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-4">
        <a href="{{ route('client.dashboard') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
            ←
        </a>
        <div>
            <h1 class="text-lg font-bold text-white leading-tight">Chat with {{ $assignedStaff->name ?? 'Staff' }}</h1>
            <p class="text-xs text-emerald-500 font-medium">Online</p>

            @if($assignedStaff)
                <p class="text-sm text-slate-500">You are now connected with {{ $assignedStaff->name }}. Feel free to ask any questions or share your concerns.</p>
            @else
                <p class="text-sm text-slate-500">No staff assigned yet. Please wait for a staff member to connect with you.</p>
            @endif
        </div>
    </div>

    {{-- Main Chat Panel with its own scroll bar --}}
    <div class="rounded-3xl bg-black shadow-sm ring-1 ring-slate-200/70 overflow-hidden chat-panel-scrollbar">
        
        {{-- Scrollable Message Area --}}
        <div id="chat-window" class="h-[500px] overflow-y-auto p-6 space-y-6 bg-slate-50/30 chat-scrollbar">
            @forelse($messages as $message)
                @php $isMe = $message->sender_id === auth()->id(); @endphp
                
                <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} items-end gap-2">
                    <div class="flex flex-col {{ $isMe ? 'items-start' : 'items-end' }} max-w-[85%]">
                        <div class="rounded-3xl px-6 py-3 text-sm shadow-sm {{ $isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-slate-900 border border-slate-200 rounded-tl-none' }}">
                            <p class="leading-relaxed">{{ $message->body ?? $message->content }}</p>
                            
                            @if($message->attachment_path)
                                <div class="mt-2 pt-2 border-t border-black/5">
                                    <a href="{{ Storage::url($message->attachment_path) }}" class="flex items-center gap-2 text-xs font-bold text-blue-700 hover:underline">
                                        <strong>Attachment:</strong> {{ basename($message->attachment_path) }}
                                    </a>
                                </div>
                            @endif
                        </div>
                        <span class="mt-1 text-[10px] text-slate-400 {{ $isMe ? 'text-right' : 'text-left' }}">
                            {{ $message->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="flex h-full items-center justify-center text-slate-400 italic">
                    No messages yet.
                </div>
            @endforelse
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-white border-t border-slate-100">
            <form action="{{ route('client.messages.send') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <textarea 
                    name="body" 
                    rows="2" 
                    class="w-full rounded-2xl border-slate-200 bg-slate-50 p-4 text-sm focus:bg-white focus:border-blue-500 focus:ring-0 transition-all resize-none" 
                    placeholder="Type your message..."
                    required
                ></textarea>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <label class="cursor-pointer inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100">
                            <input type="file" name="attachment" class="hidden" onchange="updateFileName(this)">
                            Attach File
                        </label>
                        <span id="file-name" class="text-[11px] text-slate-400 truncate max-w-[100px]">No file</span>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#003eb3] px-6 py-2 text-sm font-bold text-gray-800 shadow-md hover:bg-blue-800 transition-all">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<style>
    /* Custom Scrollbar for chat window */
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

    /* Custom Scrollbar for whole page */
    .page-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .page-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .page-scrollbar::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 10px;
    }
    .page-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>

<script>
    const chatWindow = document.getElementById('chat-window');
    // Ensure the scroll bar stays at the bottom on load
    window.onload = () => {
        chatWindow.scrollTop = chatWindow.scrollHeight;
    };

    function updateFileName(input) {
        const label = document.getElementById('file-name');
        label.textContent = input.files.length > 0 ? input.files[0].name : "No file";
    }
</script>
@endsection