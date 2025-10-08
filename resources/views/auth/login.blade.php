<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-50 via-white to-blue-50">
        <!-- Header con Logo y Título -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-4">
                <div class="w-20 h-20 bg-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl font-bold text-indigo-800 mb-2">Sistema de Facturación</h1>
            <p class="text-gray-600">Ingresa tus credenciales para acceder al sistema</p>
        </div>

        <!-- Card de Login -->
        <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-xl rounded-2xl border border-gray-100">
            <!-- Session Status -->
            <x-auth-session-status class="mb-6" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Correo Electrónico')" class="text-gray-700 font-medium" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <x-text-input id="email" 
                                     class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" 
                                     type="email" 
                                     name="email" 
                                     :value="old('email')" 
                                     required 
                                     autofocus 
                                     autocomplete="username" 
                                     placeholder="tu@email.com" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Contraseña')" class="text-gray-700 font-medium" />
                    <div class="relative mt-2">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-text-input id="password" 
                                     class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                     type="password"
                                     name="password"
                                     required 
                                     autocomplete="current-password" 
                                     placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" 
                               type="checkbox" 
                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" 
                               name="remember">
                        <span class="ms-2 text-sm text-gray-600">{{ __('Recordarme') }}</span>
                    </label>

                    @php
                        $hasPasswordReset = app('router')->has('password.request');
                    @endphp
                    @if ($hasPasswordReset)
                        <a class="text-sm text-indigo-600 hover:text-indigo-500 font-medium transition-colors" href="{{ route('password.request') }}">
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <div class="pt-4">
                    <button type="submit" 
                            class="w-full flex justify-center items-center px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        {{ __('Iniciar Sesión') }}
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-500">
                    Sistema de Gestión Empresarial
                </p>
                <p class="text-xs text-gray-400 mt-1">
                    © {{ date('Y') }} Todos los derechos reservados
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
