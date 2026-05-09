@php
    // Dynamically choose the layout based on the user's role
    $layout = auth()->user()->role === 'Staff' ? 'layouts.staff' : 'layouts.client';
@endphp

@extends($layout)

@section('content')
    {{-- Internal CSS for Theme Consistency --}}
    <style>
        .profile-bg {
            background-image: linear-gradient(rgba(15, 23, 42, 0.9), rgba(15, 23, 42, 0.9)), 
                              url('https://images.unsplash.com/photo-1590283603385-17ffb3a7f29f?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            border-radius: 2rem;
            margin-top: 1rem;
        }

        .glass-panel {
            background-color: rgba(30, 41, 59, 0.7) !important;
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }
        
        /* Ensure form labels and text are readable on dark glass */
        .glass-panel label, 
        .glass-panel h2, 
        .glass-panel p, 
        .glass-panel span {
            color: #f8fafc !important;
        }
        
        .glass-panel input {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.2);
            color: white;
        }
    </style>

    <div class="py-12 px-4 profile-bg min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex items-center justify-between mb-6">
                <h2 class="font-bold text-3xl text-white leading-tight">
                    {{ __('Account Settings') }}
                </h2>
                <span class="text-3xl font-black tracking-tighter bg-gradient-to-r from-[#818cf8] via-[#60a5fa] to-[#22d3ee] bg-clip-text text-transparent">
                    FinTrack
                </span>
            </div>

            {{-- Profile Information Section --}}
            <div class="p-6 sm:p-10 glass-panel">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Password Update Section --}}
            <div class="p-6 sm:p-10 glass-panel">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Danger Zone Section --}}
            <div class="p-6 sm:p-10 border border-red-500/20 bg-red-500/5 rounded-[1.5rem] backdrop-blur-md">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection