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

    public function messages() {
        return [
            'title.required' => '제목을 입력하세요.',
            'betting_date.required' => '배팅 날짜을 입력하세요.',
            'folder_count.required' => '폴더 수을 입력하세요.',
            'odds.required' => '배당률을 입력하세요.',
            'bet_amount.required' => '배팅 금액을 입력하세요.'
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('bet_amount')) {
            $this->merge([
                'bet_amount' => str_replace(',', '', $this->bet_amount),
            ]);
        }
    }
}
