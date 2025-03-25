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

<body class="flex justify-center min-h-screen bg-[#292A2D]">
    <main>
        <div class="w-full max-w-md mt-10 rounded-2xl p-6 text-white">

            <h1 class="text-2xl font-bold mb-4 mt-10 text-center text-[#4D6BFE]">McGrubber's Burgers</h1>

            @if(session('error_message'))
                <div class="border border-red-500 bg-red-500/15 text-red-500 p-4 rounded-lg">
                    <p class="text-sm">{{ session('error_message') }}</p>
                </div>
            @endif

            @if(session('message'))
                <div class="border border-green-500 bg-green-500/15 text-green-500 p-4 rounded-lg">
                    <p class="text-sm">{{ session('message') }}</p>
                </div>
            @endif


            @yield('main_content')
        </div>
    </main>

    <script>
        /**
         * Disables the submit button, changes its text to "Processing...",
         * and updates the cursor style to "not-allowed".
         * @param event - The event object.
         */
        function disableButton(event) {
            const submitButton = document.getElementById('submit_button');
            const recaptchaResponse = grecaptcha.getResponse();

            if (recaptchaResponse.length === 0) {
                alert('Please complete the reCaptcha challenge');
                event.preventDefault();
                return;
            }

            submitButton.disabled = true;
            submitButton.innerText = 'Processing...';
            submitButton.style.cursor = 'not-allowed';

            /*setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerText = 'Submit';
                submitButton.style.cursor = 'pointer';
            }, 5000);*/
        }


        /**
        *  This function is called when the page is loaded.
        *  It enables the submit button, changes its text to "Submit",
        * */
        document.addEventListener('DOMContentLoaded', () => {
            const submitButton = document.getElementById('submit_button');
            submitButton.disabled = false;
            submitButton.style.cursor = 'pointer';
            submitButton.textContent = 'Submit';
        });


        /**
         * Validates the length of the TOTP input field.
         *
         * @param {string} submitBtnId - The ID of the submit button element.
         * @param {string} totpInputId - The ID of the TOTP input field.
         */
        function validateTotpLength(submitBtnId, totpInputId) {
            const submitButton = document.getElementById(submitBtnId);
            const totpInput = document.getElementById(totpInputId);

            totpInput.value = totpInput.value.slice(0, 6);

            if (totpInput.value.length < 6) {
                submitButton.disabled = true;
                submitButton.textContent = 'TOTP must be 6 digits';
                submitButton.style.cursor = 'not-allowed';
            } else {
                submitButton.disabled = false;
                submitButton.style.cursor = 'pointer';
                submitButton.textContent = 'Check Code';
            }
        }


    </script>
</body>
</html>
