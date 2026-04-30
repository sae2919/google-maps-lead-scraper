<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | Lead Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        .input-field {
            width: 100%;
            padding: 11px 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
            background: #fff;
        }

        .input-field:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .input-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 7px;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
            padding: 11px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-primary:hover { background: #1d4ed8; transform: translateY(-1px); }
        .btn-primary:active { transform: scale(0.98); }

        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
            padding: 11px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
        }

        .btn-danger:hover { background: #dc2626; color: #fff; }

        .section-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
        }
    </style>
</head>

<body class="bg-[#f8fafc] text-slate-800">

<div class="flex min-h-screen">

    {{-- ── SIDEBAR ── --}}
    <div class="w-64 bg-white border-r border-slate-200 p-4 flex flex-col shadow-sm fixed h-full z-30">
        <div class="flex items-center gap-3 px-2 mb-8">
            <div class="w-10 h-10 bg-[#2563eb] rounded-lg flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-100">LG</div>
            <span class="font-bold text-slate-700 tracking-tight text-lg">Lead Generator</span>
        </div>

        <nav class="space-y-1 flex-1">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Home
            </a>
            <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Dashboard
            </a>
            <a href="/history" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                History
            </a>
            <a href="/generate" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-500 hover:bg-slate-50 hover:text-slate-700 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                AI Generator
            </a>

            {{-- Active: Profile --}}
            <a href="/profile" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#eff6ff] text-[#2563eb] font-semibold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Profile
            </a>
        </nav>

        <div class="pt-4 border-t border-slate-100">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all font-medium w-full text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- ── MAIN ── --}}
    <div class="flex-1 ml-64 flex flex-col">

        {{-- TOPBAR --}}
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 sticky top-0 z-40">
            <div>
                <h1 class="text-lg font-extrabold text-slate-800 tracking-tight">My Profile</h1>
                <p class="text-xs text-slate-400">Manage your account settings</p>
            </div>
            <a href="/dashboard"
                class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-blue-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </header>

        <main class="p-8 max-w-3xl mx-auto w-full space-y-6">

            {{-- ── AVATAR + NAME HEADER ── --}}
            <div class="flex items-center gap-5 bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-black text-2xl flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-800">{{ auth()->user()->name }}</h2>
                    <p class="text-sm text-slate-400">{{ auth()->user()->email }}</p>
                    <span class="mt-1 inline-block text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-lg
                        {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-blue-50 text-blue-600' }}">
                        {{ auth()->user()->role ?? 'user' }}
                    </span>
                </div>
            </div>

            {{-- ── UPDATE PROFILE ── --}}
            <div class="section-card">
                <div class="mb-6">
                    <h3 class="text-base font-extrabold text-slate-800">Profile Information</h3>
                    <p class="text-sm text-slate-400 mt-1">Update your name and email address.</p>
                </div>

                @if(session('status') === 'profile-updated')
                <div class="alert-success mb-5">
                    ✓ Profile updated successfully.
                </div>
                @endif

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="input-label">Full Name</label>
                        <input id="name" type="text" name="name"
                            class="input-field @error('name') border-red-400 @enderror"
                            value="{{ old('name', auth()->user()->name) }}"
                            required autofocus>
                        @error('name')
                            <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="input-label">Email Address</label>
                        <input id="email" type="email" name="email"
                            class="input-field @error('email') border-red-400 @enderror"
                            value="{{ old('email', auth()->user()->email) }}"
                            required>
                        @error('email')
                            <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                        && !auth()->user()->hasVerifiedEmail())
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700 font-medium">
                        ⚠ Your email address is unverified.
                        <form method="POST" action="{{ route('verification.send') }}" class="inline">
                            @csrf
                            <button type="submit" class="underline font-bold hover:text-amber-900 ml-1">
                                Resend verification email
                            </button>
                        </form>
                    </div>
                    @endif

                    <div class="flex items-center gap-3 pt-1">
                        <button type="submit" class="btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>

            {{-- ── UPDATE PASSWORD ── --}}
            <div class="section-card">
                <div class="mb-6">
                    <h3 class="text-base font-extrabold text-slate-800">Change Password</h3>
                    <p class="text-sm text-slate-400 mt-1">Use a strong password with at least 8 characters.</p>
                </div>

                @if(session('status') === 'password-updated')
                <div class="alert-success mb-5">
                    ✓ Password updated successfully.
                </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="input-label">Current Password</label>
                        <input id="current_password" type="password" name="current_password"
                            class="input-field @error('current_password', 'updatePassword') border-red-400 @enderror"
                            autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="input-label">New Password</label>
                        <input id="password" type="password" name="password"
                            class="input-field @error('password', 'updatePassword') border-red-400 @enderror"
                            autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="input-label">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            class="input-field"
                            autocomplete="new-password">
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <button type="submit" class="btn-primary">Update Password</button>
                    </div>
                </form>
            </div>

            {{-- ── DELETE ACCOUNT ── --}}
            <div class="section-card border-red-100">
                <div class="mb-6">
                    <h3 class="text-base font-extrabold text-red-600">Delete Account</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Once deleted, all your data will be permanently removed. This cannot be undone.
                    </p>
                </div>

                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                    class="btn-danger">
                    Delete My Account
                </button>
            </div>

        </main>
    </div>
</div>

{{-- ── DELETE CONFIRMATION MODAL ── --}}
<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8">
        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h3 class="text-lg font-extrabold text-slate-800 mb-2">Are you absolutely sure?</h3>
        <p class="text-sm text-slate-500 mb-6">
            This will permanently delete your account and all associated data including searches and leads. There is no way to recover this.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label class="input-label">Confirm your password to continue</label>
                <input type="password" name="password"
                    class="input-field"
                    placeholder="Enter your password"
                    required>
                @error('password', 'userDeletion')
                    <p class="text-red-500 text-xs font-semibold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 pt-1">
                <button type="button"
                    onclick="document.getElementById('deleteModal').classList.add('hidden')"
                    class="flex-1 py-3 rounded-xl border border-slate-200 font-bold text-slate-600 hover:bg-slate-50 transition-all text-sm">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-all text-sm">
                    Yes, Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>