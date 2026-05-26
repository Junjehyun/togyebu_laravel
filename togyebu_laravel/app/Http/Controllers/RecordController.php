<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRequest;
use App\Http\Requests\Record\UpdateRequest;
use App\Models\Record;
use App\Models\User;
use App\Models\UserStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{
    //

    public function history(Request $request) {

        $user = Auth::user();

        // 기록
        $userRecords = Record::where('user_id', $user->id ?? '')->orderBy('created_at', 'asc')->get();

        // 잔고 계산 로직
        $balance = 0;
        foreach($userRecords as $record) {
            $balance += $record->profit; // 누적 계산
            $record->balance = $balance; // 각 기록에 잔고 저장
        }
        
        // 유저의 승률 계산 
        $wins = $userRecords->where('result', 'win')->count();
        $losses = $userRecords->where('result', 'lose')->count();
        $draws = $userRecords->where('result', 'draw')->count();

        // 확정된 경기 수
        $confirmedRecords = $userRecords->whereIn('result', ['win', 'lose', 'draw'])->count();
        $winsRate = $confirmedRecords > 0 ? round(($wins / $confirmedRecords) * 100, 2) : 0;
        $userRecords = $userRecords->sortByDesc('created_at')->values();

        // 환수율 계산
        $confirmedRecordsForRoi = $userRecords->whereIn('result', ['win', 'lose', 'draw']);
        $totalBet = $confirmedRecordsForRoi->sum('bet_amount');
        $totalHit = $confirmedRecordsForRoi->where('result', 'win')->sum('win_amount');
        // 환수율 = (적중금액 합계 / 베팅금액 합계) * 100
        // ※ 100%면 본전, 100% 초과 시 수익, 100% 미만 시 손실
        $roi = $totalBet > 0 ? round(($totalHit / $totalBet) * 100) : 0;

        $latestRecords = $userRecords->sortByDesc('created_at')->take(10);

        // 베팅 총액 계산
        $totalBetAmount = $latestRecords->sum('bet_amount');

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


        return view('record.history', [
            'user' => $user,
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'winRate' => $winsRate,
            'balance' => $balance,
            'userRecords' => $userRecords,
            'roi' => $roi,
            'totalBetAmount' => $totalBetAmount,
            'avgOdds' => $avgOdds,
            'maxWinStreak' => $maxWinStreak,
            'maxLoseStreak' => $maxLoseStreak,
        ]);
    }
    /**
     * 신규 추가 화면
     */
    public function add() {

        return view('record.add');
    }

    /**
     * 신규등록 처리
     */
    public function store(AddRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 예상 적중금액 (pending 상태에서는 표시용)
        $expected = (float)($request->odds ?? 0) * (int)($request->bet_amount ?? 0);

        $user->records()->create([
            'betting_date' => $request->betting_date,
            'title' => $request->title,
            'folder_count' => $request->folder_count,
            'odds' => $request->odds,
            'bet_amount' => $request->bet_amount,
            'result' => 'pending',
            'win_amount' => (int) round($expected),
            'profit' => 0,
        ]);

        return redirect()->route('main.index')->with('success', '베팅 기록이 저장되었습니다.');
    }
    /**
     * 베팅 확정처리 (pending → win/lose/draw)
     * - 소유권 검증 필수
     * - UserStat 정확히 저장
     * - 이전 profit을 고려한 delta 방식으로 balance 조정
     * - DB 트랜잭션 적용 (금융 데이터 보호)
     */
    public function betConfirm(Request $request)
    {
        $result = $request->input('result');

        if (!in_array($result, ['win', 'lose', 'draw'], true)) {
            return back()->with('error', '잘못된 결과 값입니다.');
        }

        /** @var User $user */
        $user = Auth::user();

        // ★ 치명적 보안 버그 수정: 반드시 현재 로그인 사용자의 record만 조회
        $record = Record::where('user_id', $user->id)->findOrFail($request->id);

        // pending 상태가 아닌 경우 중복/오류 방지
        if ($record->result !== 'pending') {
            return back()->with('error', '이미 확정된 베팅입니다.');
        }

        return DB::transaction(function () use ($record, $user, $result) {
            $oldProfit = (int) $record->profit; // 항상 0 (pending)

            $bet = (int) $record->bet_amount;
            $odds = (float) $record->odds;

            // 결과에 따른 profit / win_amount 설정
            switch ($result) {
                case 'win':
                    $record->result = 'win';
                    $record->win_amount = (int) round($bet * $odds);
                    $record->profit = $record->win_amount - $bet;
                    break;

                case 'lose':
                    $record->result = 'lose';
                    $record->win_amount = (int) round($bet * $odds);
                    $record->profit = -$bet;
                    break;

                case 'draw':
                    $record->result = 'draw';
                    $record->win_amount = (int) round($bet * $odds);
                    $record->profit = 0;
                    break;
            }

            $newProfit = (int) $record->profit;
            $delta = $newProfit - $oldProfit;

            $record->save();

            // UserStat 안전하게 가져오거나 생성 후 저장
            $userStat = $user->userStats()->firstOrCreate(
                ['user_id' => $user->id],
                ['betting_total_win' => 0, 'betting_total_loss' => 0, 'betting_total_draw' => 0]
            );

            // 통계 증가
            if ($result === 'win') {
                $userStat->increment('betting_total_win');
            } elseif ($result === 'lose') {
                $userStat->increment('betting_total_loss');
            } else {
                $userStat->increment('betting_total_draw');
            }

            // ★ 핵심 수정: delta 방식으로 balance 조정 (이전 값 차감 고려)
            if ($delta !== 0) {
                $user->increment('balance', $delta);
            }

            return back()->with('success', '베팅 결과가 확정되었습니다.');
        });
    }
    /**
     * 수정 화면
     */
    public function edit($id)
    {
        /** @var User $user */
        $user = Auth::user();

        // 소유권 검증 + 조회 (치명적 보안 버그 수정)
        $record = Record::where('user_id', $user->id)->findOrFail($id);

        // 예상 적중금액 계산 (기존 bet/odds 기준)
        $expected = (float) $record->odds * (int) $record->bet_amount;

        return view('record.edit', [
            'record' => $record,
            'expected' => $expected,
        ]);
    }
    /**
     * 수정 처리
     * - 소유권 검증
     * - 이미 확정된 기록(bet result가 win/lose/draw)의 경우 bet/odds 변경 시 profit을 재계산하고
     *   balance에 delta를 반영 (치명적 데이터 무결성 버그 수정)
     */
    public function update(UpdateRequest $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();

        // ★ 소유권 검증 (findOrFail만 쓰던 치명적 버그 수정)
        $record = Record::where('user_id', $user->id)->findOrFail($id);

        $oldProfit = (int) $record->profit;
        $oldResult = $record->result;

        // 콤마 제거 + 숫자 변환
        $betAmount = (int) str_replace(',', '', $request->input('bet_amount'));
        $betting_date = $request->input('betting_date');
        $title = $request->input('title');
        $folder_count = (int) $request->input('folder_count');
        $odds = (float) $request->input('odds');

        $record->betting_date = $betting_date;
        $record->title = $title;
        $record->folder_count = $folder_count;
        $record->odds = $odds;
        $record->bet_amount = $betAmount;

        // 확정된 기록이라면 현재 result 기준으로 profit 재계산
        if (in_array($oldResult, ['win', 'lose', 'draw'], true)) {
            $newProfit = $this->calculateProfit($oldResult, $odds, $betAmount);
            $record->profit = $newProfit;
            $record->win_amount = (int) round($betAmount * $odds);
        } else {
            // pending인 경우 win_amount만 업데이트 (profit은 0 유지)
            $record->win_amount = (int) round($betAmount * $odds);
            $record->profit = 0;
        }

        $newProfit = (int) $record->profit;
        $delta = $newProfit - $oldProfit;

        return DB::transaction(function () use ($record, $user, $delta, $oldResult) {
            $record->save();

            if ($delta !== 0) {
                $user->increment('balance', $delta);
            }

            return redirect()
                ->route('record.history')
                ->with('success', '베팅 기록이 수정되었습니다.');
        });
    }
    /**
     * 누적 수익 그래프 ajax
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
    /**
     * 폴더별 통계 ajax
     */
    public function chartFolder() {
        $user = Auth::user();

        $records = Record::where('user_id', $user->id)
            ->whereIn('result', ['win', 'lose'])
            ->get();

        // 폴더 수별로 그룹핑
        $folderStats = $records->groupBy('folder_count')->map(function ($group) {
            $total = $group->count();
            $wins = $group->where('result', 'win')->count();
            $winRate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;

            return [
                'folder' => $group->first()->folder_count,
                'total' => $total,
                'wins' => $wins,
                'rate' => $winRate,
            ];
        })->values();

        return response()->json([
            'labels' => $folderStats->pluck('folder'),
            'rates' => $folderStats->pluck('rate'),
        ]);
    }
    
    /**
     * 입출금 내역기록 페이지
     */
    public function transaction() {
        return view('record.transaction');
    }

    /**
     * 기록 삭제 처리
     * - 소유권 검증 필수
     * - 삭제 시 해당 profit을 balance에서 차감 (delta = -profit)
     * - UserStat 카운트도 감소 (확정된 기록인 경우)
     * - 트랜잭션 적용
     */
    public function delete($id)
    {
        /** @var User $user */
        $user = Auth::user();

        // ★ 소유권 검증
        $record = Record::where('user_id', $user->id)->findOrFail($id);

        $profitToReverse = (int) $record->profit;
        $resultToDecrement = $record->result;

        return DB::transaction(function () use ($record, $user, $profitToReverse, $resultToDecrement) {
            $record->delete();

            if ($profitToReverse !== 0) {
                $user->decrement('balance', $profitToReverse);
            }

            // UserStat도 일관되게 감소 (pending은 카운트 없음)
            if (in_array($resultToDecrement, ['win', 'lose', 'draw'], true)) {
                $userStat = $user->userStats()->firstOrCreate(
                    ['user_id' => $user->id],
                    ['betting_total_win' => 0, 'betting_total_loss' => 0, 'betting_total_draw' => 0]
                );

                if ($resultToDecrement === 'win') {
                    $userStat->decrement('betting_total_win');
                } elseif ($resultToDecrement === 'lose') {
                    $userStat->decrement('betting_total_loss');
                } else {
                    $userStat->decrement('betting_total_draw');
                }
            }

            return redirect()->route('record.history')
                ->with('success', '베팅 기록이 삭제되었습니다.');
        });
    }

    // ============================================================
    // 내부 헬퍼 메서드 (금융 로직 중앙화)
    // ============================================================

    /**
     * 결과 + 배당 + 베팅금액으로 profit 계산
     */
    private function calculateProfit(string $result, float $odds, int $betAmount): int
    {
        return match ($result) {
            'win'  => (int) round($betAmount * $odds - $betAmount),
            'lose' => -$betAmount,
            'draw' => 0,
            default => 0,
        };
    }
}
