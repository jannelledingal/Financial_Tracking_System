<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Show the chat between the logged-in user and another user
    public function showChat($otherUserId)
{
    $me = auth()->user();
    $otherUser = \App\Models\User::findOrFail($otherUserId);

    // 1. Logic Check: If I am Staff, I want to see ALL messages involving this Client
    $messages = \App\Models\Message::where(function($q) use ($me, $otherUserId) {
        // Normal User Logic: Messages between ME and the OTHER USER
        $q->where('sender_id', $me->id)->where('receiver_id', $otherUserId)
          ->orWhere('sender_id', $otherUserId)->where('receiver_id', $me->id);
          
    })->orWhere(function($q) use ($me, $otherUserId) {
        // Staff/Admin Logic: If I'm Staff, show me the Client's messages even if I didn't send them
        if ($me->role === 'Staff' || $me->role === 'Admin') {
            $q->where('sender_id', $otherUserId)
              ->orWhere('receiver_id', $otherUserId);
        }
    })->orderBy('created_at', 'asc')->get();

    // 2. Mark as read only if the message was actually sent TO the logged-in user
    \App\Models\Message::where('receiver_id', $me->id)
        ->where('sender_id', $otherUserId)
        ->update(['is_read' => true]);

    return view('messages.chat', compact('messages', 'otherUser'));
}

    // Save a new message
    public function send(Request $request) 
{
    $request->validate([
        'receiver_id' => 'required',
        'content' => 'nullable|string',
        'attachment' => 'nullable|file|max:10240',
    ]);

    if (!$request->filled('content') && !$request->hasFile('attachment')) {
        return redirect()->back()->withErrors(['content' => 'Please add a message or attach a file.'])->withInput();
    }

    $attachmentPath = null;
    $attachmentName = null;

    if ($request->hasFile('attachment')) {
        $file = $request->file('attachment');
        $attachmentName = $file->getClientOriginalName();
        $attachmentPath = $file->store('message-attachments', 'public');
    }

    try {
        $message = \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content ?? '',
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Database Error: ' . $e->getMessage());
    }

    return redirect()->back()->with('success', 'Sent!');
}

    public function downloadAttachment(Message $message)
    {
        $userId = auth()->id();
        if ($message->sender_id !== $userId && $message->receiver_id !== $userId) {
            abort(403);
        }

        if (!$message->attachment_path || !Storage::disk('public')->exists($message->attachment_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($message->attachment_path, $message->attachment_name ?? basename($message->attachment_path));
    }
}