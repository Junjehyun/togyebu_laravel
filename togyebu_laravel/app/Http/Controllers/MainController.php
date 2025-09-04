<?php

namespace App\Http\Controllers;

use App\Http\Requests\Main\IndexRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{
    //
    public function index(IndexRequest $request) {

        $users = Auth::user();

        return view('main.index', [
            'users' => $users,
            //'records' => $records
        ]);
    }
}
