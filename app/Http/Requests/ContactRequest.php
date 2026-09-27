<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    /**
     * Inquiry types offered in the form.
     *
     * @var list<string>
     */
    public const INQUIRY_TYPES = ['general', 'quote', 'tender', 'supply', 'partnership', 'other'];

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Trim whitespace and normalise the email address before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'organization' => trim((string) $this->input('organization')),
            'email' => mb_strtolower(trim((string) $this->input('email'))),
            'message' => trim((string) $this->input('message')),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'organization' => ['nullable', 'string', 'max:160'],
            'email' => ['required', 'string', 'email:rfc', 'max:191'],
            'inquiry_type' => ['required', Rule::in(self::INQUIRY_TYPES)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'consent' => ['accepted'],
        ];

        if (Turnstile::enabled()) {
            $rules['cf-turnstile-response'] = ['required', 'string', new Turnstile($this->ip())];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return trans('contact.fields');
    }
}
