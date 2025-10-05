<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livewire DataTable</title>

    <!-- Tailwind for quick styling -->
    <script src="https://cdn.tailwindcss.com"></script>

    @livewireStyles
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto mt-10 bg-white p-6 rounded shadow">
       @yield('content')
    </div>

    @livewireScripts
</body>
</html>
