<!DOCTYPE html>
<html lang="en" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Invento Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-full">
    <div class="flex min-h-full">
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                            <i class="fas fa-cubes text-xl"></i>
                        </div>
                        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Invento Market</h1>
                    </div>
                    <h2 class="mt-8 text-3xl font-extrabold tracking-tight text-gray-900">Create your account</h2>
                </div>

                <div class="mt-10">
                    <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                        @csrf
                        <x-input label="Full Name" name="name" id="name" :value="old('name')" placeholder="John Doe" required autofocus />
                        <x-input label="Email Address" type="email" name="email" id="email" :value="old('email')" placeholder="name@company.com" required />
                        <x-input label="Password" type="password" name="password" id="password" placeholder="••••••••" required />
                        <x-input label="Confirm Password" type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" required />
                        <div class="pt-2">
                            <x-button class="w-full justify-center py-3 text-base shadow-lg shadow-blue-500/20">Create Account</x-button>
                        </div>
                    </form>
                    <div class="mt-10 text-center">
                        <p class="text-sm text-gray-600">Already have an account? <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-blue-500">Sign in instead</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative hidden w-0 flex-1 lg:block">
            <div class="absolute inset-0 h-full w-full bg-blue-600">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-700/80 to-blue-900 mix-blend-multiply"></div>
                <img class="h-full w-full object-cover" src="https://images.unsplash.com/photo-1553413077-190dd305871c?ixlib=rb-4.0.3&auto=format&fit=crop&q=80&w=2000" alt="Warehouse Operations">
            </div>
        </div>
    </div>
</body>
</html>
