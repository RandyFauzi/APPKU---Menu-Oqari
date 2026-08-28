<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brewly - Coffee Shop Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f4f0ec',
                            100: '#e5ddd5',
                            500: '#8c6b5d',
                            600: '#6f5247',
                            900: '#2c1e16',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-900 antialiased selection:bg-brand-500 selection:text-white">
    <div class="min-h-screen flex">
        <!-- Left Side: Login Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white shadow-2xl z-10 relative">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-10">
                    <div class="w-10 h-10 bg-brand-900 rounded-xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-coffee text-xl"></i>
                    </div>
                    <span class="text-2xl font-black tracking-tight text-brand-900">Brewly<span class="text-brand-500">.</span></span>
                </div>

                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Welcome back</h2>
                    <p class="mt-2 text-sm text-gray-500 font-medium">
                        Log in to manage your coffee shop dashboard.
                    </p>
                </div>

                <div class="mt-8">
                    <form action="/admin/dashboard" method="GET" class="space-y-6">
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-1">
                                Email address
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input id="email" name="email" type="email" autocomplete="email" required value="admin@bitten.com"
                                    class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all font-medium">
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-1">
                                Password
                            </label>
                            <div class="mt-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input id="password" name="password" type="password" autocomplete="current-password" required value="password123"
                                    class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm transition-all font-medium">
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember-me" name="remember-me" type="checkbox"
                                    class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-gray-300 rounded cursor-pointer">
                                <label for="remember-me" class="ml-2 block text-sm text-gray-700 font-medium cursor-pointer">
                                    Remember me
                                </label>
                            </div>
                            <div class="text-sm">
                                <a href="#" class="font-bold text-brand-600 hover:text-brand-500 transition-colors">
                                    Forgot your password?
                                </a>
                            </div>
                        </div>

                        <div>
                            <button type="submit"
                                class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-brand-900 hover:bg-brand-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-900 transition-all active:scale-[0.98]">
                                Sign in to Dashboard
                            </button>
                        </div>
                    </form>

                    <div class="mt-8">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-2 bg-white text-gray-400 font-medium">Or continue with</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3">
                            <a href="#" class="w-full inline-flex justify-center py-2.5 px-4 border border-gray-200 rounded-xl shadow-sm bg-white text-sm font-bold text-gray-500 hover:bg-gray-50 transition-colors">
                                <i class="fab fa-google text-lg text-red-500"></i>
                            </a>
                            <a href="#" class="w-full inline-flex justify-center py-2.5 px-4 border border-gray-200 rounded-xl shadow-sm bg-white text-sm font-bold text-gray-500 hover:bg-gray-50 transition-colors">
                                <i class="fab fa-apple text-lg text-gray-900"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-auto pt-10 mx-auto w-full max-w-sm lg:w-96 text-center lg:text-left">
                <p class="text-xs text-gray-400 font-medium">&copy; 2026 Brewly SaaS Platform. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side: Image / Promo -->
        <div class="hidden lg:block relative w-0 flex-1 bg-brand-900">
            <img class="absolute inset-0 h-full w-full object-cover opacity-60 mix-blend-overlay" src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=2047&q=80" alt="Coffee shop background">
            
            <!-- Overlay Content -->
            <div class="absolute inset-0 flex flex-col justify-center px-20">
                <div class="max-w-xl">
                    <span class="inline-block px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold tracking-widest uppercase mb-6 border border-white/20">Platform Update v2.0</span>
                    <h1 class="text-5xl font-black text-white leading-tight mb-6 drop-shadow-lg">
                        Empowering independent coffee shops.
                    </h1>
                    <p class="text-lg text-brand-100 font-medium mb-10 leading-relaxed drop-shadow">
                        Manage your live orders, customize your digital menu, and analyze daily revenue from one centralized dashboard. Designed for speed, built for scale.
                    </p>
                    
                    <div class="flex items-center gap-4">
                        <div class="flex -space-x-3">
                            <img class="w-10 h-10 rounded-full border-2 border-brand-900" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=100&q=80" alt="">
                            <img class="w-10 h-10 rounded-full border-2 border-brand-900" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=100&q=80" alt="">
                            <img class="w-10 h-10 rounded-full border-2 border-brand-900" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=100&q=80" alt="">
                        </div>
                        <p class="text-sm font-bold text-white">Trusted by 500+ cafes nationwide</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
