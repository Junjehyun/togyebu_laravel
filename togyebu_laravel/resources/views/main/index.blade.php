@extends('layouts.common')
@section('title', 'MAIN')
@section('content')
    <div>
        <h1 class="text-2xl font-semi-bold mb-4">토계부(가칭)에 오신걸 환영합니다.</h1>
        <p>로그인 후에 이용해주세요.</p>
    </div>
    <div class="mt-20">
        님의 최근 10경기 기록
    </div>
    <div>
        <div></div>
    </div>
    <table class="w-full text-sm border-collapse mt-5">
        <td class="">순번</td>
        <td class="">날짜</td>
        <td class="">내역</td>
        <td class="">배당</td>
        <td class="">베팅금</td>
        <td class="">폴더수</td>
        <td class="">적중금액</td>
        <td class="">적중유무</td>
        <td class="">수익</td>
        <td class="">잔고</td>
    </table>
@endsection