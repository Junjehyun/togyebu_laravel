<?php

namespace App\Http\Requests\Record;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            //
            //'betting_date' => ['required', 'date'],   // 날짜 형식 검증
            'betting_date' => ['required', 'date_format:Y-m-d'], // 날짜 형식 검증 (YYYY-MM-DD)
            'title'        => ['required', 'string', 'max:50'],
            'folder_count' => ['required', 'integer', 'min:0'],
            'odds'         => ['required', 'numeric', 'min:0'],
            'bet_amount'   => ['required'], // 숫자형 처리 (콤마는 후처리)
        ];
    }

    public function messages() {
        return [
            'betting_date.required' => '날짜를 입력해주세요.',
            'betting_date.date_format' => '올바른 날짜 형식이 아닙니다.',
            'title.required' => '제목을 입력해주세요.',
            'title.max' => '제목은 최대 50자까지 입력 가능합니다.',
            'folder_count.required' => '폴더 수를 입력해주세요.',
            'folder_count.integer' => '폴더 수는 숫자만 입력해주세요.',
            'odds.required' => '배당률을 입력해주세요.',
            'odds.numeric' => '배당률은 숫자만 입력해주세요.',
            'bet_amount.required' => '베팅 금액을 입력해주세요.',
        ];
    }
}
