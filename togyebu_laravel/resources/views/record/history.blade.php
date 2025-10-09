@extends('layouts.common')
@section('title', ' 님의 기록')
@section('content')
    <div class="w-2/3 flex flex-col justify-center items-center mt-20 mx-auto">
        <h1 class="text-2xl">{{ auth()->user()->name }}님의 베팅기록</h1>
        <h2 class="mt-5 text-xl">{{ $wins }}승  {{ $losses }}패  {{ $draws }}적특 승률 {{ $winRate }}% </h2>
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
            @foreach($userRecords as $record)
                <tr class="@if($record->result === 'win') bg-indigo-50 @elseif($record->result === 'lose') bg-fuchsia-50 @elseif($record->result === 'draw') bg-gray-100 @endif">
                    <td class="border px-2 py-1">{{ $record->id }}</td>
                    <td class="border px-2 py-1">{{ $record->betting_date->format('Y-m-d') }}</td>
                    <td class="border px-2 py-1">{{ $record->title }}</td>
                    <td class="border px-2 py-1">{{ $record->odds }}</td>
                    <td class="border px-2 py-1">{{ $record->bet_amount }}</td>
                    <td class="border px-2 py-1">{{ $record->folder_count }}</td>
                    <td class="border px-2 py-1">{{ $record->win_amount }}</td>
                    <td class="border px-2 py-1">
                        <span class="
                                @if($record->result === 'win') text-blue-600 font-bold
                                @elseif($record->result === 'lose') text-red-600 font-bold
                                @elseif($record->result === 'draw') text-gray-500 font-bold
                                @endif
                            ">
                                {{ $record->result === 'win' ? '적중' : ($record->result === 'lose' ? '미적중' : '적특') }}
                            </span>
                    </td>
                    <td class="border px-2 py-1">
                        @if($record->result === 'pending')
                            ?
                        @else
                            <span class="
                                @if($record->profit > 0) profit-win
                                @elseif($record->profit < 0) profit-lose
                                @else profit-draw
                                @endif
                            ">
                                {{ number_format($record->profit) }}₩
                            </span>
                        @endif
                    </td>
                    <td class="border px-2 py-1">{{ number_format($record->balance) }}₩</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="w-2/3 flex justify-end mx-auto mt-4">
        <a href="{{ route('main.index') }}" class="inline-block bg-pink-300 hover:bg-pink-400 text-white rounded px-3 py-1">
            뒤로
        </a>
    </div>
@endsection

