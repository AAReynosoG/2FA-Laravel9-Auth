@extends('layouts.notices_layout')
@section('title', 'McGrubberS Burger - Into the Sauce')

@section('content')
    <h1 class="text-3xl font-semibold mb-4">Registration Successful!</h1>
    <p class="text-lg mb-6">
        Welcome to <span class="text-[#4D6BFE] font-bold">McGrubberS Burger</span>! <br>
        A verification email has been sent to your inbox. Please check your email to complete your registration process.
    </p>
    <p class="text-sm text-gray-400 mb-6">
        The link sent to the email will expire in 60 minutes.
        If you do not see the email within a few minutes, please check your spam folder.
    </p>
    <a href="/sign_in" class="px-6 py-3 rounded-lg bg-[#4D6BFE] text-white font-semibold
    hover:cursor-pointer hover:bg-[#3D4FA9] focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]">
        Go to Sign In
    </a>
@endsection
