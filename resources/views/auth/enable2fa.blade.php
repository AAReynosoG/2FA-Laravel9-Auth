@extends('layouts.notices_layout')
@section('title', 'McGrubberS Burger - Into the Sauce')

@section('content')
    <div class="max-w-md mx-auto text-center">
        <h1 class="text-3xl font-semibold mb-4">Enable 2FA Authentication</h1>
        <p class="text-lg mb-6">
            Scan this QR code with your Google Authenticator app:
        </p>

        @if(session('QRImageUrl') && session('secretKey'))
            <div class="flex justify-center mb-6">
                <div class="p-4 bg-white rounded-lg shadow-md">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate(session('QRImageUrl')) !!}
                </div>
            </div>

            <p class="text-sm text-gray-400 mb-6">
                Or enter this key manually: <span class="font-bold"> {{ session('secretKey') }} </span>
            </p>
        @else
            <p class="text-sm text-gray-400 mb-6">
                Missing data. Try again later or contact the support team.
            </p>
        @endif

        <a href="/totp/link" class="inline-block px-6 py-3 rounded-lg bg-[#4D6BFE] text-white font-semibold
        hover:bg-[#3D4FA9] focus:outline-none focus:ring-2 focus:ring-[#4D6BFE] transition duration-200">
            Done
        </a>
    </div>

    <script>
        function enableInternalRedirection(){
            @php
                session(['internal_redirect' => true])
            @endphp
        }
    </script>
@endsection
