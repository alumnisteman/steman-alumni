<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'jurusan' => 'nullable|string',
            'tahun_lulus' => 'nullable|integer',
            'pekerjaan_sekarang' => 'nullable|string',
            'alamat' => 'nullable|string',
            'bio' => 'nullable|string',
            'mentor_bio' => 'nullable|string',
            'mentor_expertise' => 'nullable|string|max:255',
        ];
    }
}
