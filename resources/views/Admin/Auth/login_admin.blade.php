@extends('Admin.Layouts.admin-auth-master')

@section('title', 'Login')

@section('content')
    <div class="flex min-h-screen relative bg-[#Fdfbf7]">
        
        <!-- Illustration Background -->
        <!-- Extends past 50% so it shows underneath the rounded corner of the right panel -->
        <div class="hidden lg:block absolute top-0 left-0 w-[55%] h-full">
            <video autoplay loop muted playsinline class="absolute inset-0 w-full h-full object-cover">
                <source src="{{ asset('Assests/Login page/Login Page Illsutration (motion).webm') }}" type="video/webm">
            </video>
        </div>

        <!-- Right Side (Form) -->
        <div class="w-full lg:w-1/2 ml-auto flex items-center justify-center p-6 sm:p-12 md:p-20 bg-white lg:rounded-l-[3rem] lg:shadow-[-20px_0_40px_rgba(0,0,0,0.04)] z-10 relative">
            <div class="w-full max-w-md">
                
                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back! 👋</h2>
                    <p class="text-gray-500 text-sm">Sign in to continue to your account</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                                class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 focus:border-[#A67B5B] focus:ring-2 focus:ring-[#A67B5B]/20 transition-colors placeholder-gray-400 text-gray-900"
                                placeholder="Enter your email">
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#A67B5B] hover:text-[#8a664b]">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full pl-11 pr-11 py-3 rounded-xl border border-gray-200 focus:border-[#A67B5B] focus:ring-2 focus:ring-[#A67B5B]/20 transition-colors placeholder-gray-400 text-gray-900"
                                placeholder="Enter your password">
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center cursor-pointer text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="w-4 h-4 text-[#A67B5B] bg-gray-100 border-gray-300 rounded focus:ring-[#A67B5B]">
                        <label for="remember_me" class="ml-2 text-sm font-medium text-gray-600">Remember me</label>
                    </div>

                    <!-- Login Button -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex items-center justify-center px-5 py-3.5 text-white font-medium bg-[#A67B5B] rounded-xl hover:bg-[#8a664b] transition-colors shadow-lg shadow-[#A67B5B]/30">
                            Log In 
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="mt-8 mb-6 flex items-center justify-center space-x-4">
                    <div class="flex-1 border-t border-gray-200"></div>
                    <span class="text-sm text-gray-400">or continue with</span>
                    <div class="flex-1 border-t border-gray-200"></div>
                </div>

                <!-- Social Logins -->
                <div>
                    <button class="w-full flex items-center justify-center py-2.5 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/><path fill="none" d="M1 1h22v22H1z"/></svg>
                        <span class="text-sm font-medium text-gray-700">Continue with Google</span>
                    </button>
                </div>

                <!-- Footer Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Don't have an account? 
                        <a href="{{ route('register') }}" class="font-medium text-[#A67B5B] hover:text-[#8a664b]">Sign up</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
