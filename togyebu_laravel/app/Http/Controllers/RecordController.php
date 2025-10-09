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

        $users = Auth::user();

        // 기록
        $userRecords = Record::where('user_id', $users->id ?? '')->orderBy('created_at', 'asc')->get();

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

        return view('record.history', [
            'users' => $users,
            'wins' => $wins,
            'losses' => $losses,
            'draws' => $draws,
            'winRate' => $winsRate,
            'balance' => $balance,
            'userRecords' => $userRecords,
        ]);
    }

    public function add() {

        return view('record.add');
    }

    public function store(AddRequest $request) {

        $user = User::find(1); // 예시로 ID가 1인 사용자 조회
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
     * 입출금 내역기록 페이지
     */
    public function transaction($id) {
        return view('record.transaction');
    }

    public function delete($id) {
        return redirect()->route('record.history')->with('success', '베팅 기록이 삭제되었습니다.');
    }
}
