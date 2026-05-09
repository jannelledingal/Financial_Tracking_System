<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>FinTrack | Financial Tracking App</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="bg-[#0f172a] text-white">
    <nav class="flex justify-between items-center p-8 max-w-7xl mx-auto">
        <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-400 to-cyan-400 bg-clip-text text-transparent">FinTrack</h1>
        <div class="space-x-4 flex items-center">
            @auth
                <a href="{{ route('dashboard') }}" class="bg-emerald-600 hover:bg-emerald-700 px-6 py-3 rounded-full font-bold transition shadow-lg shadow-emerald-500/50 hover:shadow-emerald-500/80 transform hover:scale-105">Back to Workplace</a>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="bg-slate-700 hover:bg-slate-600 px-6 py-3 rounded-full font-bold transition shadow-lg shadow-slate-500/50 hover:shadow-slate-500/80 transform hover:scale-105 border border-slate-600">Log in</a>
                <a href="{{ route('register') }}" class="bg-indigo-600 hover:bg-indigo-700 px-6 py-3 rounded-full font-bold transition shadow-lg shadow-indigo-500/50 hover:shadow-indigo-500/80 transform hover:scale-105">Get Started</a>
            @endguest
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-8 py-20 grid lg:grid-cols-2 gap-12">
        <div class="space-y-8">
            <h2 class="text-6xl font-extrabold leading-tight">
                Control your <span class="text-indigo-500">Money</span>, <br>
                not the other way around.
            </h2>
            <p class="text-gray-400 text-xl max-w-md">
                A professional system for tracking deposits, withdrawals with the guidance of financial experts.
            </p>
            <div class="flex gap-4">
                <a href="{{ route('register') }}" class="bg-white text-black px-8 py-4 rounded-2xl font-bold text-lg hover:bg-gray-100 transition shadow-lg hover:shadow-xl transform hover:scale-105">Open Free Account</a>
            </div>
        </div>

        <div class="relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-cyan-500 rounded-3xl blur opacity-30"></div>
            <div class="relative bg-slate-800 border border-slate-700 p-8 rounded-3xl">
                <div class="flex justify-between items-center mb-10">
                    <div>
                        <p class="text-slate-400 text-sm">Main Account Balance</p>
                        <p class="text-4xl font-bold mt-2">$18,450.00</p>
                    </div>
                    <div class="h-12 w-12 bg-indigo-500/20 rounded-full flex items-center justify-center">
                        <div class="h-6 w-6 border-2 border-indigo-400 rounded-full border-t-transparent animate-spin"></div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="bg-slate-900/50 p-4 rounded-xl flex justify-between items-center border border-slate-700">
                        <span class="text-indigo-400">Salary Deposit</span>
                        <span class="text-emerald-400 font-bold">+$4,200.00</span>
                    </div>
                    <div class="bg-slate-900/50 p-4 rounded-xl flex justify-between items-center border border-slate-700">
                        <span class="text-red-400">Rent Withdrawal</span>
                        <span class="text-red-400 font-bold">-$1,200.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>