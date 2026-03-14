<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Invento Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-800 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-900">Invento Market</h1>
                <p class="text-gray-600 text-sm mt-2">Verify OTP</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p class="text-red-700 text-sm">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-700 text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <p class="text-gray-600 text-sm mb-6">Enter the 6-digit OTP sent to your email.</p>

            <form method="POST" action="{{ route('password.verify-otp.post') }}">
                @csrf
                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ session('email') ?? old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('email') border-red-500 @enderror" required>
                </div>

                <div class="mb-6">
                    <label for="otp" class="block text-gray-700 text-sm font-semibold mb-2">OTP Code</label>
                    <input type="text" id="otp" name="otp" maxlength="6" placeholder="000000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 text-center text-2xl tracking-widest @error('otp') border-red-500 @enderror" required>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Verify OTP
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">Didn't receive OTP? <a href="{{ route('password.forgot') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Request again</a></p>
            </div>
        </div>
    </div>
</body>
</html>
