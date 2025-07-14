<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIssueRequest extends FormRequest
{
    /**
     * Add standard values to the validation request, this will
     * overwrite any values from the request to prevents malformed data.
     */
    public function validationData()
    {
        return array_merge($this->all(), [
            'user_id' => $this->user()->id,
            'job_id' => $this->jobitems->job->id,
            'jobitem_id' => $this->jobitems->id
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text' => 'required|string',
            'title' => 'required|string|max:1024',
            'user_id' => '',
            'job_id' => '',
            'jobitem_id' => '',
        ];
    }
}
