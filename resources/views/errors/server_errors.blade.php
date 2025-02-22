@extends('layouts.notices_layout')
@section('title', 'McGrubberS Burger - Into the Sauce')

@section('content')
    <h1 class="text-3xl font-semibold mb-4 text-red-600">Oops!</h1>
    <p class="text-lg mb-6">
        {{ $user_message }}.
    </p>
    <p class="text-sm text-gray-400 mb-6">
        Don't worry, you can always go back to the previous page. <br/>
        Error Identifier: {{ $error_identifier }}
    </p>

    <a href="/sign_in" class="px-6 py-3 rounded-lg bg-[#4D6BFE] text-white font-semibold
    hover:cursor-pointer hover:bg-[#3D4FA9] focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]">
        Go Back
    </a>
@endsection
