@extends('layouts.auth_layout')
@section('title', 'McGrubberS Burger - Into the Sauce')

@section('main_content')

    @if($errors->any())
        <div class="bg-red-500/20 border-red-500 text-red-500 text-sm border p-4 rounded-lg mt-2 mb-2">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form onsubmit="disableButton(event)" class="flex flex-col" id="totp_form" action="{{ route('user.link.2fa')  }}" method="POST">
        @csrf

        <div class="relative mt-2">
            <input required type="number" placeholder="Your TOTP" id="totp_validate" name="totp"
                   class="p-2 pl-10 w-full bg-[#292A2D] text-gray-400 placeholder:text-gray-500
           border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]"
                   oninput="validateTotpLength('submit_button', 'totp_validate')">

            <x-totp_input_svg />
        </div>

        <div class="relative mt-6">
            <input required type="password" placeholder="Password" id="sign_in_password" name="password"
                   class="p-2 pl-10 w-full bg-[#292A2D] text-gray-400 placeholder:text-gray-500
                   border border-gray-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]">

            <x-totp_input_svg />
        </div>

        <div class="relative mt-6">
            <p class="text-xl font-bold text-gray-500 mb-4">Google reCaptcha</p>
            <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        </div>
        <x-error_message field="g-recaptcha-response" />

        <button type="submit" id="submit_button"
                class="bg-[#4D6BFE] text-white py-2 mt-6 rounded-lg
                    hover:cursor-pointer hover:bg-[#3D4FA9] focus:outline-none focus:ring-2 focus:ring-[#4D6BFE]">
            Check Code
        </button>
    </form>
@endsection
