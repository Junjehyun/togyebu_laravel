@extends('layouts.common')
@section('title', ' 님의 기록')
@section('content')
    <div class="w-2/3 mt-20 mx-auto flex justify-center items-start gap-6">
        <div class="w-full grid grid-cols-3 gap-3 text-center">
            <!-- 누적 수익 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">누적 수익</p>
                <p class="text-lg mt-1"> 
                    <span class="{{ $user->balance < 0 ? 'text-red-600 font-bold' : 'text-blue-600 font-bold' }}">
                        {{ $user->balance > 0 ? '+' . number_format($user->balance) : number_format($user->balance) }}원
                    </span>
                </p>
            </div>
            <!-- 최근 10경기 기록 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">최근 10경기</p>
                <p class="text-lg font-bold">
                    {{ $wins }}승 {{ $losses }}패 {{ $draws }}적특
                </p>
            </div>
            <!-- 환수율 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">환수율</p>
                <p class="text-lg font-bold">{{ $roi }}%</p>
            </div>
            <!-- 베팅총액 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">베팅총액</p>
                <p class="text-lg font-bold">{{ number_format($totalBetAmount) }}원</p>
            </div>
            <!-- 적중률 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">적중률</p>
                <p class="text-lg font-bold">{{ $winRate }}%</p>
            </div>
            <!-- 평균배당 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">평균배당</p>
                <p class="text-lg font-bold">{{ $avgOdds }}배</p>
            </div>
            <!-- 최다연승 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">최다연승</p>
                <p class="text-lg font-bold text-blue-600">{{ $maxWinStreak }}연승</p>
            </div>
            <!-- 최다연패 -->
            <div class="p-3 rounded border border-rose-50">
                <p class="text-sm text-gray-500">최다연패</p>
                <p class="text-lg font-bold text-red-600">{{ $maxLoseStreak }}연패</p>
            </div>
            <!-- 신규 추가 -->
            <div class="p-3 rounded bg-rose-50">
                <p class="text-md font-bold text-rose-500 hover:text-rose-600 mt-3">
                    @auth
                        <a href="{{ route('record.add') }}">신규 내역추가</a>
                    @endauth
                </p>
            </div>
        </div>
    </div>
    <div class="w-2/3 mx-auto mt-10 grid grid-cols-3 gap-6">
        {{-- ① 누적 수익 그래프 --}}
        <div class="bg-white border border-rose-50 rounded shadow-sm p-4">
            <h3 class="text-sm font-semibold mb-3 text-gray-600 text-center">누적 수익 추이</h3>
            <div style="height: 180px;">
                <canvas id="profitChart"></canvas>
            </div>
        </div>

        {{-- ② 결과 비율 (도넛형 차트) --}}
        <div class="bg-white border border-rose-50 rounded shadow-sm p-4 flex flex-col items-center justify-center">
            <div class="w-1/2">
                <canvas id="resultDonutChart"
                    data-wins="{{ $wins }}"
                    data-losses="{{ $losses }}"
                    data-draws="{{ $draws }}"></canvas>
            </div>
        </div>
        <div class="bg-white border border-rose-50 rounded shadow-sm p-4 flex flex-col items-center justify-center">
            <div style="height: 200px;">
                <canvas id="folderChart"></canvas>
            </div>
        </div>
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
                <th class="border px-2 py-1">확정</th>
                <th class="border px-2 py-1">잔고</td>
            </tr>
        </thead>
        <tbody>
            @foreach($userRecords as $record)
                <tr class="@if($record->result === 'win') bg-indigo-50 @elseif($record->result === 'lose') bg-fuchsia-50 @elseif($record->result === 'draw') bg-gray-100 @endif">
                    <td class="border px-2 py-1 text-center">{{ $record->id }}</td>
                    <td class="border px-2 py-1">{{ $record->betting_date->format('Y-m-d') }}</td>
                    <td class="border px-2 py-1">{{ $record->title }}</td>
                    <td class="border px-2 py-1 text-center">{{ $record->odds }}</td>
                    <td class="border px-2 py-1 text-center">{{ number_format($record->bet_amount) }}₩</td>
                    <td class="border px-2 py-1 text-center">{{ $record->folder_count }}</td>
                    <td class="border px-2 py-1">{{ number_format($record->win_amount) }}₩</td>
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
                    <td class="border px-2 py-1">
                        @if($record->result === 'pending')
                            <form class="record-form" action="{{ route('record.betConfirm') }}" method="POST" onsubmit="return confirm('베팅 결과를 확정하시겠습니까?');">
                                @csrf
                                <button type="submit" class="text-rose-600">확정</button>
                                <input type="hidden" name="id" value="{{ $record->id }}">
                                <input type="hidden" name="result" value="">
                            </form>
                        @else
                            <p class="text-gray-400">확정완료!</p>
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

