<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth middleware guards the route
    }

    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'current_password' => ['nullable', 'string'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Validasi kustom: jika user mengisi field password baru,
     * maka current_password wajib ada dan harus cocok.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (filled($this->password)) {
                if (empty($this->current_password)) {
                    $validator->errors()->add(
                        'current_password', 'Password saat ini wajib diisi jika ingin mengganti password.'
                    );
                } elseif (! Hash::check($this->current_password, $this->user()->password)) {
                    $validator->errors()->add(
                        'current_password', 'Password saat ini tidak sesuai.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Nama tidak boleh kosong.',
            'email.required'            => 'Email tidak boleh kosong.',
            'email.unique'              => 'Email sudah digunakan akun lain.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ];
    }

    /**
     * Trim nama dan email sebelum validasi berjalan.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => trim($this->name ?? ''),
            'email' => trim(strtolower($this->email ?? '')),
        ]);
    }
}
