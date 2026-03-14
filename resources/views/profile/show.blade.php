@extends('layouts.app')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Profile Info --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-5 border-b pb-3">
            <i class="fas fa-user mr-2 text-blue-600"></i>Account Information
        </h3>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-400 @enderror" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-400 @enderror" required>
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="pt-2">
                    <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-5 border-b pb-3">
            <i class="fas fa-lock mr-2 text-orange-500"></i>Change Password
        </h3>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('current_password') border-red-400 @enderror" required>
                    @error('current_password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="password"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-400 @enderror" required>
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="pt-2">
                    <button type="submit" class="px-5 py-2 bg-orange-600 text-white rounded-lg text-sm font-semibold hover:bg-orange-700">
                        <i class="fas fa-key mr-2"></i>Update Password
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Account Meta --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4 border-b pb-3">
            <i class="fas fa-info-circle mr-2 text-gray-400"></i>Account Details
        </h3>
        <div class="text-sm space-y-2 text-gray-600">
            <div class="flex justify-between">
                <span>Member since</span>
                <span class="font-medium text-gray-800">{{ $user->created_at->format('M j, Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Last updated</span>
                <span class="font-medium text-gray-800">{{ $user->updated_at->format('M j, Y') }}</span>
            </div>
        </div>
    </div>

</div>
@endsection
