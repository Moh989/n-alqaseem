<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    protected const MAX_ATTEMPTS_PER_ACCOUNT = 5;

    protected const MAX_ATTEMPTS_PER_IP = 20;

    public function show(): View
    {
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email', 'max:191'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $accountKey = 'admin-login:'.Str::lower($credentials['email']).'|'.$request->ip();
        $ipKey = 'admin-login-ip:'.$request->ip();

        foreach ([$accountKey => self::MAX_ATTEMPTS_PER_ACCOUNT, $ipKey => self::MAX_ATTEMPTS_PER_IP] as $key => $max) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                throw ValidationException::withMessages([
                    'email' => __('admin.auth.throttle', ['seconds' => RateLimiter::availableIn($key)]),
                ]);
            }
        }

        if (! Auth::attempt([...$credentials, 'is_active' => true], $request->boolean('remember'))) {
            RateLimiter::hit($accountKey, 60);
            RateLimiter::hit($ipKey, 60);

            throw ValidationException::withMessages(['email' => __('admin.auth.failed')]);
        }

        RateLimiter::clear($accountKey);
        $request->session()->regenerate();

        $request->user()->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ])->save();

        AuditLog::record('login');

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        AuditLog::record('logout');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
