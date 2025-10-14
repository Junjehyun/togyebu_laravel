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
            // 제목 관련 유효성검사 메세지 
            'title.required' => '제목을 입력하세요.',
            // 배팅 날짜 관련 유효성검사 메세지
            'betting_date.required' => '배팅 날짜을 입력하세요.',
            'betting_date.date' => '날짜 형식이 올바르지 않습니다.', 
            // 폴더 수 관련 유효성검사 메세지
            'folder_count.required' => '폴더 수을 입력하세요.',
            'folder_count.integer' => '폴더 수는 숫자만 입력 가능합니다.',
            'folder_count.min' => '폴더 수는 1 이상이어야 합니다.',
            // 배당 관련 유효성검사 메세지
            'odds.required' => '배당을 입력하세요.',
            'odds.numeric' => '배당은 숫자만 입력 가능합니다.',
            'odds.min' => '배당은 0 이상이어야 합니다.',
            // 배팅 금액 관련 유효성검사 메세지 
            'bet_amount.required' => '배팅 금액을 입력하세요.',
            'bet_amount.integer' => '배팅 금액은 숫자만 입력 가능합니다.',
            'bet_amount.min' => '배팅 금액은 1원 이상이어야 합니다'

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
