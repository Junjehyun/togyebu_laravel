<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddRequest extends FormRequest
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
            'title'        => ['required', 'string', 'max:255'],
            'betting_date' => ['required', 'date'],
            'folder_count' => ['required', 'integer', 'min:1'],
            'odds'         => ['required', 'numeric', 'min:0'],
            'bet_amount'   => ['required', 'integer', 'min:1']
        ];
    }
}
