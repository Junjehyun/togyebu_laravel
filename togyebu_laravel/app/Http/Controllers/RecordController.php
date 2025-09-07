<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRequest;
use App\Models\Record;
use App\Models\User;
use Illuminate\Http\Request;


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
        return redirect()->route('main.index')->with('success', '기록이 저장되었습니다.');
        
    }

    public function betConfirm(Request $request) {

        //dd($request->all());

        // record 조회
        $record = Record::findOrFail($request->id);

        // 이미 확정된 경우 차단 
        if($record->result !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => '이미 확정된 기록입니다.'
            ]);
        }

        // 수익 계산 
        $bet = $record->bet_amount;
        $odds = $record->odds;

        switch($request->input('result')) {
            case 'win':
                $record->result = 'win';
                $record->win_amount = $bet * $odds;
                $record->profit = $record->win_amount - $bet;
                break;
            case 'lose':
                $record->result = 'lose';
                $record->win_amount = 0;
                $record->profit = -$bet;
                break;
            case 'draw':
                $record->result = 'draw';
                $record->win_amount = 0;
                $record->profit = 0;
                break;
        }
        $record->save();

        $user = $record->user;
        $user->balance += $record->profit;
        $user->save();

        return back()->with('success', '베팅 결과가 확정되었습니다.');
    }
    
}
