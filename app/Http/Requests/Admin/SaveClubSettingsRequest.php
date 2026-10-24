<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SaveClubSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Page::class);
    }

    public function rules(): array
    {
        return [
            'booking_title' => ['required', 'string', 'max:255'],
            'booking_text' => ['nullable', 'string', 'max:5000'],
            'contacts' => ['present', 'array', 'list', 'max:30'],
            'contacts.*' => ['required', 'array:name,phone,email'],
            'contacts.*.name' => ['required', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+() .-]+$/'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
            'location_title' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:50', 'regex:/^[0-9+() .-]+$/'],
            'contact_url' => ['nullable', 'url', 'max:2000', 'regex:~^https?://~i'],
            'facebook_url' => ['nullable', 'url', 'max:2000', 'regex:~^https?://~i'],
            'instagram_url' => ['nullable', 'url', 'max:2000', 'regex:~^https?://~i'],
        ];
    }
}
