<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecordController extends Controller
{
    //

    public function history() {

        return view('record.history');
    }

    public function add() {

        return view('record.add');
    }
}
