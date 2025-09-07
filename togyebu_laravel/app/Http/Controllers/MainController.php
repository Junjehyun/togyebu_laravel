<?php

namespace App\Http\Controllers;

use App\Http\Requests\Main\IndexRequest;
use App\Models\Record;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{
    //
    public function index(IndexRequest $request) {

        // 로그인 한 유저 정보
        $users = Auth::user();
        // 로그인 한 유저의 기록들
        $records = Record::where('user_id', $users->id ?? '')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('main.index', [
            'users' => $users,
            'records' => $records,
        ]);
    }
}
