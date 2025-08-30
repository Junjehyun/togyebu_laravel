@extends('layouts.common')
@section('title', 'MAIN')
@section('content')
    <div>
        <h1 class="text-2xl font-semi-bold mb-4">토계부(가칭)에 오신걸 환영합니다.</h1>
        @auth
            <p>{{ Auth::user()->name }}님, 환영합니다.</p>
        @else
            <p>로그인 후, 이용해주세요.</p>
        @endauth
    </div>
    <div class="w-2/3 flex justify-between mt-20 mx-auto">
        <h3 class="flex">
            <p class="font-bold text-sky-800">@auth {{ $users[0]->name }}</p>님의 최근 10경기 기록 ( 승 패 승률 ) @else <p class="font-bold">로그인 후, 이용 부탁드립니다.</p> @endauth
        </h3>
        @auth
            <a href="{{ route('record.add') }}" class="text-sm text-rose-400 hover:text-rose-600 mt-1">신규추가</a>
        @endauth
    </div>
    @auth
        <table class="w-2/3 text-sm border-collapse mt-5 mx-auto">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border px-2 py-1">순번</th>
                    <th class="border px-2 py-1">날짜</th>
                    <th class="border px-2 py-1">내역</th>
                    <th class="border px-2 py-1">배당</th>
                    <th class="border px-2 py-1">베팅금</th>
                    <th class="border px-2 py-1">폴더수</th>
                    <th class="border px-2 py-1">적중금액</th>
                    <th class="border px-2 py-1">적중유무</th>
                    <th class="border px-2 py-1">수익</th>
                    <th class="border px-2 py-1">잔고</th>
                    <th class="border px-2 py-1"></th>
                    <th class="border px-2 py-1"></th>
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
                    <th class="border px-2 py-1"></td>
                    <th class="border px-2 py-1"></th>
                </tr>
            </tbody>
        </table>
        <div class="w-2/3 flex justify-end mt-3 mx-auto">
                <a href="{{ route('record.history') }}" class="text-sm text-blue-400 hover:text-blue-600">전체 기록 보기</a>
        </div>
    @endauth
@endsection