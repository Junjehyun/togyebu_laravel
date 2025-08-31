<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    //
    
    // 테이블명 (Eloquent 기본 규칙대로 'records'라서 생략 가능)
    protected $table = 'records';

    // 대량할당 허용 필드
    protected $fillable = [
        'user_id',
        'betting_date',
        'title',
        'odds',
        'bet_amount',
        'folder_count',
        'result',
        'win_amount',
        'profit'
    ];

    // 캐스트 설정 (데이터 타입 변환)
    protected $casts = [
        'betting_date' => 'date',
        'odds' => 'decimal:2',
        'bet_amount' => 'integer',
        'folder_count' => 'integer',
        'win_amount' => 'integer',
        'profit' => 'integer',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
