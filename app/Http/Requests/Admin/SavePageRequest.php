<?php

namespace App\Http\Requests\Admin;

use App\Models\Page;
use App\Support\RichText;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class SavePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        $page = $this->route('page');

        return $page instanceof Page
            ? Gate::allows('update', $page)
            : Gate::allows('create', Page::class);
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        $page = $this->route('page');
        $reserved = ['register'];
        foreach (Route::getRoutes()->getRoutes() as $route) {
            $segment = explode('/', $route->uri())[0];
            if ($segment !== '' && ! str_contains($segment, '{')) {
                $reserved[] = $segment;
            }
        }

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::notIn(array_unique($reserved)), Rule::unique('pages', 'slug')->ignore($page instanceof Page ? $page->id : null)],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'sections' => ['required', 'array', 'max:50'],
            'sections.*' => ['required', 'array:type,data'],
            'sections.*.type' => ['required', Rule::in(['hero', 'rich_text'])],
            'sections.*.data' => ['required', 'array'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $sections = $this->input('sections');
            if (! is_array($sections) || ! array_is_list($sections)) {
                $validator->errors()->add('sections', 'Sections must be an ordered list.');

                return;
            }

            foreach ($sections as $index => $section) {
                if (! is_array($section) || ! in_array($section['type'] ?? null, ['hero', 'rich_text'], true) || ! is_array($section['data'] ?? null)) {
                    continue;
                }
                $rules = match ($section['type']) {
                    'hero' => ['heading' => ['required', 'string', 'max:255'], 'text' => ['nullable', 'string', 'max:5000']],
                    'rich_text' => ['body' => ['required', 'string', 'max:20000']],
                };
                $allowed = array_keys($rules);
                foreach (array_keys($section['data']) as $key) {
                    if (! in_array($key, $allowed, true)) {
                        $validator->errors()->add("sections.$index.data.$key", 'This field is not allowed.');
                    }
                }
                $part = validator($section['data'], $rules);
                if ($section['type'] === 'rich_text' && is_string($section['data']['body'] ?? null)
                    && trim(strip_tags(RichText::clean($section['data']['body']))) === '') {
                    $validator->errors()->add("sections.$index.data.body", 'Rich text must contain visible text.');
                }
                foreach ($part->errors()->messages() as $field => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add("sections.$index.data.$field", $message);
                    }
                }
            }
        }];
    }

    /** @return array<string, mixed> */
    public function pageData(): array
    {
        return $this->validated();
    }
}
