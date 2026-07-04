<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Mendukung login dengan:
     * 1. Nomor WhatsApp (langsung dari tabel users)
     * 2. Email (jika diisi)
     * 3. NIS santri (resolve ke user wali)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = $this->input('identifier');
        $password = $this->input('password');

        // 1. Coba cari user langsung via WhatsApp di tabel users
        $user = \App\Models\User::where('whatsapp', $identifier)->first();

        // 2. Coba via email
        if (!$user && filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $user = \App\Models\User::where('email', $identifier)->first();
        }

        // 3. Coba via NIS santri atau No. Pendaftaran → resolve ke user wali
        if (!$user && !empty($identifier)) {
            $student = \App\Models\Student::where('nis', $identifier)
                ->orWhere('registration_number', $identifier)
                ->first();
            if ($student && $student->guardian && $student->guardian->user) {
                $user = $student->guardian->user;
            }
        }

        // 4. Fallback: coba via WhatsApp guardian (backward compat)
        if (!$user && !empty($identifier)) {
            $guardian = \App\Models\Guardian::where('whatsapp', $identifier)->first();
            if ($guardian && $guardian->user) {
                $user = $guardian->user;
            }
        }

        if (!$user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        // Cek status aktif user
        if (isset($user->is_active) && !$user->is_active) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => 'Akun Anda telah dinonaktifkan. Silakan hubungi admin.',
            ]);
        }

        // Attempt auth with the resolved user's email or whatsapp
        $authField = $user->email ? 'email' : 'whatsapp';
        $credentials = [
            $authField => $user->$authField,
            'password' => $password,
        ];

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'identifier' => trans('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->string('identifier')).'|'.$this->ip());
    }
}
