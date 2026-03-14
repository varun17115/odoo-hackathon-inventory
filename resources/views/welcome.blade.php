<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - Invento Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700 min-h-screen">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-blue-900">Invento Market</h1>
                </div>
                <div class="flex gap-4">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-blue-600 hover:text-blue-700 font-semibold">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">Register</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h2 class="text-5xl font-bold text-white mb-6">Inventory Management System</h2>
            <p class="text-xl text-blue-100 mb-8">Manage your products, categories, and inventory with ease</p>
            
            <div class="flex justify-center gap-4">
                <a href="{{ route('login') }}" class="px-8 py-3 bg-white text-blue-600 rounded-lg hover:bg-gray-100 font-semibold transition">Login</a>
                <a href="{{ route('register') }}" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition border-2 border-white">Get Started</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-20">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-3xl mb-4">📦</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Product Management</h3>
                <p class="text-gray-600">Easily manage your products with categories, pricing, and descriptions</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-3xl mb-4">📊</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Inventory Tracking</h3>
                <p class="text-gray-600">Track stock levels, set min/max quantities, and monitor inventory value</p>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-3xl mb-4">📈</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Analytics Dashboard</h3>
                <p class="text-gray-600">View real-time insights and activity logs for better decision making</p>
            </div>
        </div>
    </div>
</body>
</html>
