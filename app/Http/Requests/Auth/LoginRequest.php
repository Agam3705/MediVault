<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Mail;
use App\Mail\MediVaultMail;
use Carbon\Carbon;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->email)->first();
        if ($user && $user->locked_until && Carbon::parse($user->locked_until)->isFuture()) {
            throw ValidationException::withMessages([
                'email' => 'This account is temporarily locked due to multiple failed login attempts. Please try again in ' . Carbon::parse($user->locked_until)->diffInMinutes() . ' minutes.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            AuditLog::create([
                'user_id'          => null,
                'action'           => 'Failed login attempt for user: ' . $this->email,
                'target_type'      => 'User',
                'target_id'        => $user ? $user->id : null,
                'ip_address'       => $this->ip(),
                'user_agent'       => $this->userAgent(),
                'session_id'       => null,
                'unusual_activity' => true,
            ]);

            if ($user) {
                $user->increment('failed_attempts');
                if ($user->failed_attempts >= 3 && $user->failed_attempts < 10) {
                    Mail::to($user->email)->queue(new MediVaultMail(
                        'emails.failed_login',
                        'Warning: Multiple Failed Login Attempts',
                        [
                            'ipAddress' => $this->ip(),
                            'timestamp' => now()->format('M d, Y H:i:s'),
                        ]
                    ));
                } elseif ($user->failed_attempts >= 10) {
                    $user->update([
                        'locked_until' => now()->addMinutes(15),
                        'failed_attempts' => 0
                    ]);

                    Mail::to($user->email)->queue(new MediVaultMail(
                        'emails.failed_login',
                        'SECURITY ALERT: Account Locked Out',
                        [
                            'ipAddress' => $this->ip(),
                            'timestamp' => now()->format('M d, Y H:i:s'),
                        ]
                    ));

                    throw ValidationException::withMessages([
                        'email' => 'This account has been locked due to too many failed attempts. A security alert email was sent to you.',
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if ($user) {
            $user->update([
                'failed_attempts' => 0,
                'locked_until' => null,
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
