<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Invento Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-800 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-900">Invento Market</h1>
                <p class="text-gray-600 text-sm mt-2">Reset Your Password</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <p class="text-gray-600 text-sm mb-6">Enter your email address and we'll send you an OTP to reset your password.</p>

            <form method="POST" action="{{ route('password.send-otp') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Send OTP
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">Remember your password? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Login here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
