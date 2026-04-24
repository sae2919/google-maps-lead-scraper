<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800 min-h-screen flex flex-col">

<nav class="flex justify-between items-center px-8 py-4 bg-white border-b border-slate-200 shadow-sm">

    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">
            LG
        </div>
        <span class="font-bold text-lg text-slate-700 tracking-tight">Lead Generator</span>
    </div>

    <a href="/" class="text-sm font-semibold text-slate-500 hover:text-[#2563eb] transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Back to Home
    </a>

</nav>

<div class="flex flex-1 items-center justify-center px-4 py-12">

    <div class="bg-white w-full max-w-md rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 md:p-10">

        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">
                Welcome Back
            </h2>
            <p class="text-slate-500 font-medium">
                Enter your details to access your leads
            </p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all text-slate-700">
            </div>

            <div>
                <label class="block mb-2 text-sm font-bold text-slate-700 uppercase tracking-wider">Password</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition-all text-slate-700">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-slate-600 group-hover:text-slate-900 transition-colors">Remember me</span>
                </label>

                <a href="{{ route('password.request') }}" class="font-bold text-blue-600 hover:text-blue-700 transition-colors">
                    Forgot password?
                </a>
            </div>

            <button type="submit"
                class="w-full bg-[#2563eb] text-white py-4 rounded-2xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all active:scale-[0.98]">
                Sign In
            </button>

        </form>

        <div class="flex items-center my-8">
            <div class="flex-1 border-t border-slate-100"></div>
            <span class="px-4 text-slate-400 text-xs font-bold uppercase tracking-widest">or</span>
            <div class="flex-1 border-t border-slate-100"></div>
        </div>

        <a href="/auth/google"
            class="flex items-center justify-center gap-3 w-full border border-slate-200 rounded-2xl py-3.5 hover:bg-slate-50 transition-all font-bold text-slate-700 shadow-sm">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5 h-5">
            Continue with Google
        </a>

        <p class="text-sm mt-8 text-center text-slate-500 font-medium">
            New to Lead Generator?
            <a href="{{ route('register') }}" class="text-blue-600 font-bold hover:underline ml-1">
                Create an account
            </a>
        </p>

    </div>

</div>

</body>
</html>