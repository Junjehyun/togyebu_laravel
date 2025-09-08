<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStat extends Model
{
    //
    protected $fillable = [
        'user_id',
        'betting_total_win',
        'betting_total_loss',
        'betting_total_draw',
    ];

    // User 모델과의 관계 설정
    public function user() {
        return $this->belongsTo(User::class);
    }
}
