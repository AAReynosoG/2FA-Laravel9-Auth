<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>McGrubberS Burger - Into the Sauce</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>

<body class="flex justify-center min-h-screen bg-[#292A2D]">
<main>
    <div class="w-full max-w-md mt-10 rounded-2xl p-6 text-white">

        <h1 class="text-2xl font-bold mb-4 mt-10 text-center text-[#4D6BFE]">Welcome!!!</h1>
        <p class="text-center text-gray-400 text-sm"> <span class="font-bold">You logged IN...</span> Sadly this page is under construction. Please check back later.</p>

        <form action="/logout" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-center font-bold text-sm text-gray-400 mt-4 hover:underline hover:text-green-500 bg-transparent border-none p-0 cursor-pointer">
                Logout
            </button>
        </form>
    </div>
</main>
</body>
</html>
