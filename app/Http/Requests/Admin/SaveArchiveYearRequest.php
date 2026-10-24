<?php

namespace App\Http\Requests\Admin;

use App\Models\ArchiveYear;
use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class SaveArchiveYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Page::class);
    }

    public function rules(): array
    {
        $archiveYear = $this->route('archiveYear');

        return [
            'year' => ['required', 'integer', 'between:1900,2200',
                Rule::unique('archive_years', 'year')->ignore($archiveYear instanceof ArchiveYear ? $archiveYear->id : null)],
            'description' => ['nullable', 'string', 'max:10000'],
            'bands' => ['present', 'array', 'list', 'max:500'],
            'bands.*' => ['required', 'string', 'max:1000'],
            'is_published' => ['required', 'boolean'],
        ];
    }
}
