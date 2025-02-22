@extends('layouts.auth_layout')
@section('title', 'McGrubberS Burger - Into the Sauce')

@section('main_content')
    <p class="text-white text-sm mt-6">
        Make sure you use a valid email, you will be notified with a confirmation email.
    </p>

    <form onsubmit="disableButton('submit_button')" class="flex flex-col" id="sign_up_form" action="{{ route('user.store') }}" method="POST">
        @csrf

        <div class="relative mt-2">
            <input required type="email" placeholder="Email address" id="sign_up_email" name="email" value="{{ old('email') }}"
                   class="p-2 pl-10 w-full bg-[#292A2D] text-gray-400 placeholder:text-gray-500 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]
                   @error('email')
                   bg-red-500/10 border border-red-500 focus:ring-red-400
                   @enderror">

            <x-email_input_svg />

        </div>
        <x-error_message field="email" />


        <div class="relative mt-6">
            <input required type="password" placeholder="Password" id="sign_up_password" name="password"
                   class="p-2 pl-10 w-full bg-[#292A2D] text-gray-400 placeholder:text-gray-500 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]
                   @error('password')
                   bg-red-500/10 border border-red-500 focus:ring-red-400
                   @enderror">

            <!-- SVG Icon -->
            <x-password_input_svg />
        </div>
        <x-error_message field="password" />


        <div class="relative mt-6">
            <input required type="password" placeholder="Confirm Password" id="sign_up_password_confirmation" name="password_confirmation"
                   class="p-2 pl-10 w-full bg-[#292A2D] text-gray-400 placeholder:text-gray-500 border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]
                   @error('password_confirmation')
                   bg-red-500/10 border border-red-500 focus:ring-red-400
                   @enderror">

            <!-- SVG Icon -->
            <x-password_input_svg />
        </div>
        <x-error_message field="password_confirmation" />

        <div class="relative mt-6">
            <p class="text-xl font-bold text-gray-500 mb-4">Google reCaptcha</p>
            <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        </div>
        <x-error_message field="g-recaptcha-response" />


        <button type="submit" id="submit_button"
                class="bg-[#4D6BFE] text-white py-2 mt-6 rounded-lg
                    hover:cursor-pointer hover:bg-[#3D4FA9] focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]">
            Sign up
        </button>

        <a href="/sign_in" class="text-sm text-[#4D6BFE] mt-4 hover:underline hover:cursor-pointer">Sign In</a>
    </form>
@endsection
