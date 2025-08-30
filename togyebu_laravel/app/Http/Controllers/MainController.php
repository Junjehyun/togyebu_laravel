<?php

namespace App\Http\Controllers;

use App\Http\Requests\Main\IndexRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MainController extends Controller
{
    //
    public function index(IndexRequest $request) {
        $users = User::all();
        return view('main.index', [
            'users' => $users
        ]);
    }
}
