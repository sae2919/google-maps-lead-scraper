<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md">

    <!-- TITLE -->
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold text-white">🔐 Reset Password</h1>
        <p class="text-gray-400 text-sm">Enter your email to receive reset link</p>
    </div>

    <!-- CARD -->
    <div class="bg-gray-800/70 backdrop-blur-lg p-8 rounded-2xl shadow-xl border border-gray-700">

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- EMAIL -->
            <div class="mb-4">
                <label class="text-gray-300 text-sm">Email</label>
                <input type="email" name="email" required
                    class="w-full mt-1 p-3 rounded-lg bg-gray-900 border border-gray-700 text-white focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <button type="submit"
                class="w-full bg-blue-500 py-3 rounded-lg font-semibold hover:bg-blue-600 transition">
                📩 Send Reset Link
            </button>

        </form>

        <p class="text-center text-gray-400 text-sm mt-6">
            <a href="{{ route('login') }}" class="text-blue-400 hover:underline">
                Back to Login
            </a>
        </p>

    </div>

</div>

</body>
</html>