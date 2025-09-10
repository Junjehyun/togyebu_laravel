<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRequest;
use App\Models\Record;
use App\Models\User;
use App\Models\UserStat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RecordController extends Controller
{
    //

    public function history() {
        // 모든 사용자 조회
        $users = User::all();

        return view('record.history', [
            'users' => $users
        ]);
    }

    public function add() {

        return view('record.add');
    }

    public function addStore(AddRequest $request) {

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

        return view('record.edit', []);
    }

    public function update($id) {

        return view('record.history', []);
    }
    
}
