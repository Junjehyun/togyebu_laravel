<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddRequest;
use App\Models\Record;
use App\Models\User;


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

        //dd($request->all());

        $user = User::find(1); // 예시로 ID가 1인 사용자 조회
        $record = $user->records;
        //dd($record);

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
        

     //return redirect()->route('main.index');
    }
}
