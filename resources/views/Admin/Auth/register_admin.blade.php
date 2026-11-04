@extends('Admin.Layouts.admin-auth-master')

@section('title', 'Daftar')

@section('content')
    <div class="flex min-h-screen relative bg-ivory">
        
        <!-- Illustration Background -->
        <div class="hidden lg:block absolute top-0 left-0 w-[55%] h-full">
            <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover">
                <source src="{{ asset('Assests/Login page/Login Page Illsutration (motion).webm') }}" type="video/webm">
            </video><div class="absolute inset-0 bg-brand-950/50 pointer-events-none" aria-hidden="true"></div>
        </div>

        <!-- Right Side (Form) -->
        <div class="w-full lg:w-1/2 ml-auto flex items-center justify-center p-6 sm:p-12 md:p-20 bg-white lg:rounded-l-[3rem] lg:shadow-[-20px_0_40px_rgba(0,0,0,0.04)] z-10 relative">
            <div class="w-full max-w-md">
                
                <x-hellenic-emblem class="h-12 w-12 mb-5" />
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account ✨</h2>
                    <p class="text-gray-500 text-sm">Sign up to get started</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-gold-600 focus:ring-2 focus:ring-gold-600/20 transition-colors placeholder-gray-400 text-gray-900"
                                placeholder="Enter your full name">
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-gold-600 focus:ring-2 focus:ring-gold-600/20 transition-colors placeholder-gray-400 text-gray-900"
                                placeholder="Enter your email">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Password -->
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" :type="showPassword ? 'text' : 'password'" type="password" name="password" required autocomplete="new-password"
                                class="w-full pl-11 pr-11 py-3 rounded-xl border border-gray-200 focus:border-gold-600 focus:ring-2 focus:ring-gold-600/20 transition-colors placeholder-gray-400 text-gray-900"
                                placeholder="Create a password">
                            <button type="button" @click="showPassword = !showPassword" aria-label="Lihat kata sandi"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Confirm Password -->
                    <div x-data="{ showConfirmPassword: false }">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <input id="password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" type="password" name="password_confirmation" required autocomplete="new-password"
                                class="w-full pl-11 pr-11 py-3 rounded-xl border border-gray-200 focus:border-gold-600 focus:ring-2 focus:ring-gold-600/20 transition-colors placeholder-gray-400 text-gray-900"
                                placeholder="Confirm your password">
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword" aria-label="Lihat konfirmasi kata sandi"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg x-show="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <svg x-show="showConfirmPassword" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Register Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full flex items-center justify-center px-5 py-3.5 text-white font-medium bg-gold-600 rounded-xl hover:bg-gold-700 transition-colors shadow-lg shadow-[#34714c]/30">
                            Create Account
                        </button>
                    </div>
                </form>

                <!-- Footer Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="font-medium text-gold-600 hover:text-gold-700">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
