<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobStep1Request extends FormRequest
{
    /**
     * Set the default value of branch if empty, also add/overwrite the user ID
     */
    public function validationData(): array
    {
        return array_merge($this->all(), ['branch' => $this['branch'] ?? 'main', 'user_id' => $this->user()->id]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'owner' => 'required|string|max:255',
            'repository' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'user_id' => '',
        ];
    }
}
