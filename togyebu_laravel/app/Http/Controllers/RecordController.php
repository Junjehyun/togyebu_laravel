<?php

namespace App\Http\Controllers;

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

    public function addStore() {

        $user = User::find(1); // 예시로 ID가 1인 사용자 조회
        $record = $user->records;

        

        return view('main.index', [
        ]);
    }
}
