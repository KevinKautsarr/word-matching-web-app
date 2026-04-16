<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pengecekan dasar otorisasi bisa di sini, atau kita tetap lakukan di layer Controller
        // berdasarkan $lesson->unlockedFor()
        return true;
    }

    public function rules(): array
    {
        return [
            'attempt_token' => ['required', 'string'],
            'score'         => ['required', 'numeric', 'min:0', 'max:500'],
            'correct'       => ['required', 'numeric', 'min:0', 'max:50'],
            'total'         => ['required', 'numeric', 'min:0', 'max:50'],
            'time_spent'    => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'attempt_token.required' => 'Token sesi tidak valid atau hilang.',
            'score.max' => 'Skor melampaui batas wajar.',
        ];
    }
}
