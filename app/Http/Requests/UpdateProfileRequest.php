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
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
            'major' => 'nullable|string',
            'graduation_year' => 'nullable|integer',
            'current_job' => 'nullable|string',
            'address' => 'nullable|string',
            'bio' => 'nullable|string',
            'mentor_bio' => 'nullable|string',
            'mentor_expertise' => 'nullable|string|max:255',
            'show_social' => 'nullable|boolean',
            'socials' => 'nullable|array',
            'socials.*' => 'nullable|string|max:255',
        ];
    }
}
