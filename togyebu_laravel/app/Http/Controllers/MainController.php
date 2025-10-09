<?php

namespace App\Http\Controllers;

use App\Http\Requests\Main\IndexRequest;
use App\Models\Record;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{
    /**
     * $userRecords, $latestRecords 쪽 변수 조정 해야함.
     * 2025.10.09
     * @Author Jeon Je Hyun
     */
    public function index(IndexRequest $request) {

        // 로그인 한 유저 정보
        $users = Auth::user();
        // 유저의 모든 기록
        $userRecords = Record::where('user_id', $users->id ?? '')->orderBy('created_at', 'asc')->get();
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
        // 베팅 총액 계산
        $totalBetAmount = $latestRecords->sum('bet_amount');
        // 승률 계산 (확정된 기록이 있을 때만 계산)
        $winRate = $confirmedRecords > 0 ? round(($wins / $confirmedRecords) * 100, 2) : 0;
        // 평균 배당 계산
        $avgOdds = $userRecords->avg('odds');
        $avgOdds = $avgOdds ? round($avgOdds, 2) : 0;
        // 최다 연승 계산
        $maxWinStreak = 0;
        $currentWinStreak = 0;
        foreach($userRecords as $record) {
            if($record->result === 'win') {
                // 이긴 경우 연승 카운트 증가
                $currentWinStreak++;
                // 최대값 갱신
                $maxWinStreak = max($maxWinStreak, $currentWinStreak);
            } else {
                // 지거나 적특시 연승 초기화
                $currentWinStreak = 0;
            }
        }
        // 최다 연패 계산
        $maxLoseStreak = 0;
        $currentLoseStreak = 0;
        foreach($userRecords as $record) {
            if($record->result === 'lose') {
                // 진 경우 연패 카운트 증가
                $currentLoseStreak++;
                // 최대값 갱신
                $maxLoseStreak = max($maxLoseStreak, $currentLoseStreak);
            } else {
                // 이기거나 적특시 연패 초기화
                $currentLoseStreak = 0;
            }
        }
        // 환수율 계산
        $totalBet = $userRecords->sum('bet_amount');
        $totalProfit = $userRecords->sum(function ($record) {
            return $record->win_amount - $record->bet_amount;
        });
        // 환수율 = (총 수익 / 총 베팅액) * 100
        $roi = $totalBet > 0 ? round(($totalProfit / $totalBet) * 100) : 0;

        // 잔고 계산 로직
        $balance = 0;
        foreach($userRecords as $record) {
            $balance += $record->profit; // 누적 계산
            $record->balance = $balance; // 각 기록에 잔고 저장
        }

        $userRecords = $userRecords->sortByDesc('created_at')->values();

        return view('main.index', [
            'users' => $users,
            'records' => $latestRecords,
            'userRecords' => $userRecords,
            'confirmedRecords' => $confirmedRecords,
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'winRate' => $winRate,
            'totalBetAmount' => $totalBetAmount,
            'avgOdds' => $avgOdds,
            'maxWinStreak' => $maxWinStreak,
            'maxLoseStreak' => $maxLoseStreak,
            'roi' => $roi,
        ]);
    }
}
