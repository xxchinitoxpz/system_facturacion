<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Generar token JWT y almacenarlo en sesión
        $credentials = $request->only(['email', 'password']);
        if ($token = JWTAuth::attempt($credentials)) {
            $request->session()->put('jwt_token', $token);
            
            // Log para verificar que el token se genera (revisar en storage/logs/laravel.log)
            Log::info('Token JWT generado', [
                'user_id' => Auth::id(),
                'token' => $token
            ]);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        // Eliminar token JWT de la sesión
        $request->session()->forget('jwt_token');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
