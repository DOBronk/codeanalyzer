<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetBranchesRequest extends FormRequest
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
            'owner' => 'required|string|max:39',        // 39 character limit by GitHub
            'repository' => 'required|string|max:100'   // 100 char limit
        ];
    }
}
