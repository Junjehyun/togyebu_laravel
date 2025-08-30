<?php

namespace App\Http\Controllers;

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
}
