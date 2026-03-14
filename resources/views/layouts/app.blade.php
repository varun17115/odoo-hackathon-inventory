<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Invento Market - Inventory Management')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-blue-900 to-blue-800 text-white shadow-lg">
            <div class="p-6 border-b border-blue-700">
                <h1 class="text-2xl font-bold">Invento Market</h1>
                <p class="text-blue-200 text-sm">Inventory Management</p>
            </div>

            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('dashboard') ? 'bg-blue-700 border-l-4 border-blue-400' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 11l4-4m0 0l4 4m-4-4v4"></path>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('products.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('products.*') ? 'bg-blue-700 border-l-4 border-blue-400' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m-8-4v10l8 4m0-10l8 4m-8-4v10"></path>
                    </svg>
                    Products
                </a>

                <a href="{{ route('categories.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('categories.*') ? 'bg-blue-700 border-l-4 border-blue-400' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    Categories
                </a>

                <a href="{{ route('inventory.index') }}" class="flex items-center px-6 py-3 hover:bg-blue-700 transition {{ request()->routeIs('inventory.*') ? 'bg-blue-700 border-l-4 border-blue-400' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Inventory
                </a>
            </nav>

            <div class="absolute bottom-0 w-64 p-6 border-t border-blue-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-400 rounded-full flex items-center justify-center">
                        <span class="text-white font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-xs text-blue-200 hover:text-white">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-8 py-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="text-sm text-gray-600">
                        {{ now()->format('l, F j, Y') }}
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 overflow-auto">
                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h3 class="text-red-800 font-semibold mb-2">Errors:</h3>
                            <ul class="text-red-700 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-800">
                            {{ session('error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</body>
</html>
