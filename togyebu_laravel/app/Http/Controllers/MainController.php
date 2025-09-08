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

        // 최근 10개 기록 
        $latestRecords = Record::where('user_id', $users->id ?? '')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 확정된 경기 수 (win, lose, draw만 카운트함)
        $confirmedRecords = $latestRecords->whereIn('result', ['win', 'lose', 'draw'])->count();

        // 유저의 승률 계산 
        $wins = $latestRecords->where('result', 'win')->count();
        $losses = $latestRecords->where('result', 'lose')->count();
        $draws = $latestRecords->where('result', 'draw')->count();

        $winRate = $confirmedRecords > 0 ? round(($wins / $confirmedRecords) * 100, 2) : 0;

        return view('main.index', [
            'users' => $users,
            'records' => $latestRecords,
            'confirmedRecords' => $confirmedRecords,
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'winRate' => $winRate,
        ]);
    }
}
