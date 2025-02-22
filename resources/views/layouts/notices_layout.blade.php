<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'McGrubberS Burger - Into the Sauce')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>
<body class="flex justify-center items-center min-h-screen bg-[#292A2D] text-white font-sans">
<main class="flex flex-col items-center text-center space-y-6">
    <div class="bg-[#1f2327] p-8 rounded-2xl shadow-lg max-w-md w-full">
        @yield('content')
    </div>
</main>
</body>
</html>
