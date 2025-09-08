<?php

namespace App\Http\Controllers;

use App\Http\Requests\Main\IndexRequest;
use App\Models\Record;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{
    //
    public function index(IndexRequest $request) {

        // 로그인 한 유저 정보
        $users = Auth::user();
        // 로그인 한 유저의 기록들
        $records = Record::where('user_id', $users->id ?? '')
            ->orderBy('created_at', 'desc')
            ->get();

        // 확정된 경기 수 (win, lose, draw만 카운트함)
        $confirmedRecords = $records->whereIn('result', ['win', 'lose', 'draw'])->count();

        // 유저의 승률 계산 
        $wins = $records->where('result', 'win')->count();
        $losses = $records->where('result', 'lose')->count();
        $draws = $records->where('result', 'draw')->count();

        $winRate = $confirmedRecords > 0 ? round(($wins / $confirmedRecords) * 100, 2) : 0;

        return view('main.index', [
            'users' => $users,
            'records' => $records,
            'confirmedRecords' => $confirmedRecords,
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'winRate' => $winRate,
        ]);
    }
}
