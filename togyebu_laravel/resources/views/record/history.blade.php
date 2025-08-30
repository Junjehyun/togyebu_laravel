@extends('layouts.common')
@section('title', ' 님의 기록')
@section('content')
    <div class="w-2/3 flex flex-col justify-center items-center mt-20 mx-auto">
        <h1 class="text-2xl">{{ $users[0]->name }}님의 베팅기록</h1>
        <h2 class="mt-5 text-xl">총 승  패  승률 </h2>
    </div>
    <table class="w-2/3 text-sm border-collapse mt-5 mx-auto">
        <thead class="bg-gray-50">
            <tr>
                <th class="border px-2 py-1">순번</td>
                <th class="border px-2 py-1">날짜</td>
                <th class="border px-2 py-1">내역</td>
                <th class="border px-2 py-1">배당</td>
                <th class="border px-2 py-1">베팅금</td>
                <th class="border px-2 py-1">폴더수</td>
                <th class="border px-2 py-1">적중금액</td>
                <th class="border px-2 py-1">적중유무</td>
                <th class="border px-2 py-1">수익</td>
                <th class="border px-2 py-1">잔고</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
                <td class="border px-2 py-1"></td>
            </tr>
        </tbody>
    </table>
    <div class="w-2/3 flex justify-end mx-auto mt-4">
        <a href="{{ route('main.index') }}" class="inline-block bg-pink-300 hover:bg-pink-400 text-white rounded px-3 py-1">
            뒤로
        </a>
    </div>
@endsection