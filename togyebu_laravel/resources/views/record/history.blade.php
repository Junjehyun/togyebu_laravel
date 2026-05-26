@extends('layouts.common')
@section('title', ' 님의 기록')
@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold">{{ Auth::user()->name }}님의 기록</h1>
            <a href="{{ route('record.add') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-rose-500 hover:bg-rose-600 text-white text-sm font-medium rounded-lg transition">
                + 신규 추가
            </a>
        </div>

        {{-- Stats Cards - Fully Responsive --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-9 gap-3 mb-8">
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">누적 수익</p>
                <p class="text-xl mt-1 font-bold {{ $user->balance < 0 ? 'text-red-600' : 'text-blue-600' }}">
                    {{ $user->balance > 0 ? '+' . number_format($user->balance) : number_format($user->balance) }}원
                </p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">통산 전적</p>
                <p class="text-base mt-1 font-semibold">{{ $wins }}승 {{ $losses }}패 {{ $draws }}적특</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">환수율</p>
                <p class="text-xl mt-1 font-bold">{{ $roi }}%</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">베팅총액</p>
                <p class="text-lg mt-1 font-semibold">{{ number_format($totalBetAmount) }}원</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">적중률</p>
                <p class="text-xl mt-1 font-semibold">{{ $winRate }}%</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">평균배당</p>
                <p class="text-xl mt-1 font-semibold">{{ $avgOdds }}배</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">최다연승</p>
                <p class="text-xl mt-1 font-bold text-blue-600">{{ $maxWinStreak }}연승</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white">
                <p class="text-xs text-gray-500">최다연패</p>
                <p class="text-xl mt-1 font-bold text-red-600">{{ $maxLoseStreak }}연패</p>
            </div>
            <div class="p-3 rounded-xl border border-rose-100 bg-white col-span-2 sm:col-span-1">
                <p class="text-xs text-gray-500 mb-1">바로가기</p>
                <a href="{{ route('record.add') }}" class="text-rose-600 font-semibold hover:underline">신규 내역추가 →</a>
            </div>
        </div>
    {{-- Charts Section - Responsive --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-6xl mx-auto mb-8">
        <div class="bg-white border border-rose-100 rounded-2xl p-4">
            <h3 class="text-sm font-semibold mb-3 text-gray-600">누적 수익 추이</h3>
            <div class="h-[170px]">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-2xl p-4 flex flex-col items-center justify-center">
            <h3 class="text-sm font-semibold mb-2 text-gray-600">결과 비율</h3>
            <div class="w-4/5 max-w-[160px]">
                <canvas id="resultDonutChart"
                        data-wins="{{ $wins }}"
                        data-losses="{{ $losses }}"
                        data-draws="{{ $draws }}"></canvas>
            </div>
        </div>
        <div class="bg-white border border-rose-100 rounded-2xl p-4">
            <h3 class="text-sm font-semibold mb-3 text-gray-600">폴더수별 승률</h3>
            <div class="h-[170px]">
                <canvas id="folderChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Record Table --}}
    <div class="max-w-6xl mx-auto">
        <h2 class="text-lg font-semibold mb-3">전체 베팅 레코드</h2>

        <div class="overflow-x-auto border border-gray-200 rounded-2xl bg-white shadow-sm">
            <table class="min-w-full text-sm border-collapse">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-2 text-center hidden md:table-cell">순번</th>
                        <th class="px-3 py-2 text-left">날짜</th>
                        <th class="px-3 py-2 text-left">내역</th>
                        <th class="px-3 py-2 text-center hidden sm:table-cell">배당</th>
                        <th class="px-3 py-2 text-right">베팅금</th>
                        <th class="px-3 py-2 text-center hidden lg:table-cell">폴더</th>
                        <th class="px-3 py-2 text-right hidden md:table-cell">적중금</th>
                        <th class="px-3 py-2 text-center">결과</th>
                        <th class="px-3 py-2 text-right">수익</th>
                        <th class="px-3 py-2 text-center w-16">확정</th>
                        <th class="px-3 py-2 text-right hidden xl:table-cell">잔고</th>
                        <th class="px-3 py-2 w-12"></th>
                        <th class="px-3 py-2 w-12"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($userRecords as $record)
                        <tr class="@if($record->result === 'win') bg-indigo-50/60 @elseif($record->result === 'lose') bg-red-50/50 @elseif($record->result === 'draw') bg-gray-100/70 @endif">
                            <td class="px-3 py-2 text-center text-xs text-gray-500 hidden md:table-cell">
                                {{ $userRecords->count() - $loop->index }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-sm">{{ $record->betting_date->format('m-d') }}</td>
                            <td class="px-3 py-2 text-sm max-w-[140px] truncate">{{ $record->title }}</td>
                            <td class="px-3 py-2 text-center text-xs hidden sm:table-cell">{{ $record->odds }}</td>
                            <td class="px-3 py-2 text-right font-medium">{{ number_format($record->bet_amount) }}</td>
                            <td class="px-3 py-2 text-center text-xs hidden lg:table-cell">{{ $record->folder_count }}</td>
                            <td class="px-3 py-2 text-right text-xs hidden md:table-cell">{{ number_format($record->win_amount) }}</td>
                            <td class="px-3 py-2">
                                @if($record->result === 'pending')
                                    <select class="result-select text-xs border rounded px-1.5 py-0.5 w-full">
                                        <option value="pending">진행중</option>
                                        <option value="win">적중</option>
                                        <option value="lose">미적중</option>
                                        <option value="draw">적특</option>
                                    </select>
                                @else
                                    <span class="inline-block text-xs px-2 py-0.5 rounded-full
                                        @if($record->result === 'win') bg-blue-100 text-blue-700
                                        @elseif($record->result === 'lose') bg-red-100 text-red-600
                                        @else bg-gray-200 text-gray-600 @endif">
                                        {{ $record->result === 'win' ? '적중' : ($record->result === 'lose' ? '미적중' : '적특') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right font-semibold profit-cell">
                                @if($record->result === 'pending')
                                    <span class="text-gray-400">?</span>
                                @else
                                    <span class="@if($record->profit > 0) text-blue-600 @elseif($record->profit < 0) text-red-600 @else text-gray-500 @endif">
                                        {{ number_format($record->profit) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                @if($record->result === 'pending')
                                    <form class="record-form inline" action="{{ route('record.betConfirm') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $record->id }}">
                                        <input type="hidden" name="result" value="">
                                        <button type="submit" class="text-rose-600 text-xs font-semibold px-2 py-1">확정</button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-gray-400">완료</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-right text-xs hidden xl:table-cell text-gray-500">
                                {{ number_format($record->balance) }}
                            </td>
                            <td class="px-2 py-2 text-center">
                                <a href="{{ route('record.edit', $record->id) }}" class="text-indigo-500 text-xs">수정</a>
                            </td>
                            <td class="px-2 py-2 text-center">
                                <form action="{{ route('record.delete', $record->id) }}" method="POST" onsubmit="return confirm('삭제할까요?');">
                                    @csrf
                                    <button type="submit" class="text-rose-500 text-xs">삭제</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="max-w-6xl mx-auto mt-6 flex justify-end">
        <a href="{{ route('main.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← 메인으로</a>
    </div>

    <style>
        .profit-win { color: #2563eb; }
        .profit-lose { color: #dc2626; }
        .profit-draw { color: #6b7280; }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll(".result-select").forEach(select => {
                select.addEventListener("change", function() {
                    const row = this.closest("tr");
                    const bet = parseFloat(row.dataset.bet);
                    const odds = parseFloat(row.dataset.odds);
                    const profitCell = row.querySelector(".profit-cell");
                    let cssClass = "";
                    let result = "";

                    switch(this.value) {
                        case "win": result = (bet * odds) - bet; cssClass = "profit-win"; break;
                        case "lose": result = -bet; cssClass = "profit-lose"; break;
                        case "draw":
                            alert('2폴더 이상 부분 적중특례인 경우, 배당값을 직접 편집 부탁드립니다.');
                            result = 0; cssClass = "profit-draw"; break;
                        case "pending": result = "?"; break;
                    }

                    profitCell.classList.remove("profit-win", "profit-lose", "profit-draw");
                    if (this.value === "pending") {
                        profitCell.textContent = "?";
                    } else {
                        profitCell.textContent = result.toLocaleString("ko-KR") + "₩";
                        if (cssClass) profitCell.classList.add(cssClass);
                    }
                });
            });

            document.querySelectorAll(".record-form").forEach(form => {
                const row = form.closest("tr");
                const select = row?.querySelector(".result-select");
                const hiddenResult = form.querySelector("input[name='result']");
                if (!select || !hiddenResult) return;
                form.addEventListener("submit", () => { hiddenResult.value = select.value; });
            });
        });
    </script>
@endsection

