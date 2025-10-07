<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'selections' => ['required', 'array', 'min:1', 'max:500'],
            'selections.*.path' => ['required', 'string', 'max:500'],
            'selections.*.sha' => ['required', 'string', 'max:40'],
            'owner' => ['required', 'string', 'max:39', 'regex:/^[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}$/i'],
            'repository' => ['required', 'string', 'max:100'],
            'branch' => ['required', 'string', 'max:250'],
        ];
    }
}
