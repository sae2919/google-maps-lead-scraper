<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
    @keyframes float {
        0%, 100% { transform: translateY(0);}
        50% { transform: translateY(-6px);}
    }
    .animate-float {
        animation: float 3s ease-in-out infinite;
    }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-900 via-black to-gray-900 min-h-screen flex items-center justify-center">

<div class="w-full max-w-md">

    <!-- LOGO -->
    <div class="text-center mb-6">
        <img src="{{ asset('logo.png') }}" 
             class="w-16 h-16 mx-auto mb-3 rounded-lg shadow-lg animate-float">

        <h1 class="text-3xl font-bold text-white">Lead Generator</h1>
    </div>

    <!-- CONTENT -->
    <div class="bg-gray-800/70 backdrop-blur-lg p-8 rounded-2xl shadow-xl border border-gray-700">
        @yield('content')
    </div>

</div>

</body>
</html>