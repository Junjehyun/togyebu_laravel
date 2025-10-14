<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRequest;
use App\Http\Requests\Record\UpdateRequest;
use App\Models\Record;
use App\Models\User;
use App\Models\UserStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $totalBet = $userRecords->sum('bet_amount');
        // $totalProfit = $userRecords->sum(function ($record) {
        //     return $record->win_amount - $record->bet_amount;
        // });
        $totalHit = $userRecords->where('result', 'win')->sum('win_amount');

        // 환수율 = (순수익 ÷ 총 베팅금) × 100
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
    public function store(AddRequest $request) {

        /** @var User $user */
        $user = Auth::user();
        $record = $user->records;
        // 예상 적중금액 계산
        $expected = ($request->odds ?? 0) * ($request->bet_amount ?? 0); 
        // 새로운 기록 생성
        $user->records()->create([
            'betting_date' => $request->betting_date,
            'title' => $request->title,
            'folder_count' => $request->folder_count,
            'odds' => $request->odds,
            'bet_amount' => $request->bet_amount,
            'result' => 'pending',
            'win_amount' => $expected,
            'profit' => 0,
        ]);
        return redirect()->route('main.index')->with('success', '베팅 기록이 저장되었습니다.');
        
    }
    /**
     * 베팅 확정처리 ajax
     */
    public function betConfirm(Request $request) {

        // record 조회
        $records = Record::findOrFail($request->id);
        $users = Auth::user();

        $userStats = $records->user->userStats ?? new UserStat(['user_id' => $records->user->id]);

        // 수익 계산 
        $bet = $records->bet_amount;
        $odds = $records->odds;

        switch($request->input('result')) {
            case 'win':
                $records->result = 'win';
                $records->win_amount = $bet * $odds;
                $records->profit = $records->win_amount - $bet;
                $userStats->betting_total_win++;
                break;
            case 'lose':
                $records->result = 'lose';
                $records->win_amount = $bet * $odds;;
                $records->profit = -$bet;
                $userStats->betting_total_loss++;
                break;
            case 'draw':
                $records->result = 'draw';
                $records->win_amount = $bet * $odds;;
                $records->profit = 0;
                $userStats->betting_total_draw++;
                break;
        }
        $records->save();

        $user = $records->user;
        $user->balance += $records->profit;
        $user->save();

        return back()->with('success', '베팅 결과가 확정되었습니다.');

    }
    /**
     * 수정 화면
     */
    public function edit($id) {

        $record = Record::findOrFail($id);
        //dd($record);

        // 예상 적중금액 계산
        $expected = ($request->odds ?? 0) * ($request->bet_amount ?? 0);

        return view('record.edit', [
            'record' => $record,
            'expected' => $expected
        ]);
    }
    /**
     * 수정 처리
     */
    public function update(UpdateRequest $request, $id) {

        $record = Record::findOrFail($id);
        // 금액에서 콤마 제거 후 숫자 변환
        $betAmount = (int) str_replace(',', '', $request->input('bet_amount'));
        $betting_date = $request->input('betting_date');
        // 기록 업데이트
        $betting_date = $request->input('betting_date');
        $title = $request->input('title');
        $folder_count = $request->input('folder_count');
        $odds = $request->input('odds');
        // 업데이트 실행 (기록 수정)
        $record->update([
            'betting_date' => $betting_date,
            'title' => $title,
            'folder_count' => $folder_count,
            'odds' => $odds,
            'bet_amount' => $betAmount,
        ]);
        // 리다이렉트 및 성공 메시지 처리
        return redirect()
            ->route('record.history')
            ->with('success', '베팅 기록이 수정되었습니다.');
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
     */
    public function delete($id) {
        // record 조회 및 삭제
        $record = Record::findOrFail($id);
        $record->delete();

        // 유저 잔고 수정
        $user = $record->user;
        $user->balance -= $record->profit;
        $user->save();
        
        return redirect()->route('record.history')->with('success', '베팅 기록이 삭제되었습니다.');
    }
}
