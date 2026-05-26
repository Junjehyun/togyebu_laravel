<?php

namespace App\Http\Controllers;

use App\Http\Requests\Main\IndexRequest;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    public function index(IndexRequest $request)
    {
        // 라우트가 이제 auth 미들웨어로 보호되므로 수동 체크 제거 (과거 잔재)
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // 모든 기록 한 번에 가져오기
        $userRecords = Record::where('user_id', $user->id ?? '')
            ->orderBy('created_at', 'asc')
            ->get();
        // 최근 10경기
        $latestRecords = $userRecords->sortByDesc('created_at')->take(10);
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
        // 평균 배당 (전체 기록 기준, 최근 10경기 아님)
        $avgOdds = $userRecords->avg('odds');
        $avgOdds = $avgOdds ? round($avgOdds, 2) : 0;
        // 최다 연승 계산
        $maxWinStreak = 0;
        $currentWinStreak = 0;
        // 연승 로직
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
        // 연패 로직
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
        $confirmedRecordsForRoi = $userRecords->whereIn('result', ['win', 'lose', 'draw']);
        $totalBet = $confirmedRecordsForRoi->sum('bet_amount');
        $totalHit = $confirmedRecordsForRoi->where('result', 'win')->sum('win_amount');
        // 환수율 = (적중금액 합계 / 베팅금액 합계) * 100
        // ※ 100%면 본전, 100% 초과 시 수익, 100% 미만 시 손실
        $roi = $totalBet > 0 ? round(($totalHit / $totalBet) * 100) : 0;
        // 잔고 계산 로직
        $balance = 0;
        foreach($userRecords as $record) {
            $balance += $record->profit; // 누적 계산
            $record->balance = $balance; // 각 기록에 잔고 저장
        }
        // 최근 기록을 최신순으로 정렬
        $userRecords = $userRecords->sortByDesc('created_at')->values();
        // 뷰로 데이터 전달
        return view('main.index', [
            'user' => $user,
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

    /**
     * 차트 데이터 (ajax)
     * @return \Illuminate\Http\JsonResponse
     */
    public function chartData() {
        // 로그인 한 유저 정보
        $user = Auth::user();
        // 유저의 확정된 경기만 가져오기 (적중, 미적중, 적특)
        $records = Record::where('user_id', $user->id)
                ->whereIn('result', ['win', 'lose', 'draw'])
                ->OrderBy('id', 'asc')
                ->get();
        // id 기준 수익 배열 구성
        $chartData = $records->map(function ($record) {
            return [
                'id' => $record->id,
                'profit' => $record->profit,
            ];
        });
        // Json 형태로 반환
        return response()->json([
            'labels' => $chartData->pluck('id'),
            'profits' => $chartData->pluck('profit'),
        ]);
    }
}
