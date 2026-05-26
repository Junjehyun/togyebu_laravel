@extends('layouts.common')
@section('title', 'MAIN')
@section('content')
    @auth
        <p><span class="text-sky-800">{{ Auth::user()->name }}</span>님, 오늘 하루도 건승입니다.</p>
    @else
        <p>로그인 후, 이용해주세요.</p>
    @endauth
    <div class="max-w-6xl mx-auto mt-2 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="w-1/2 bg-white border border-rose-50 rounded p-4 shadow-sm">
            <h2 class="text-sm font-semibold mb-2 text-gray-600 text-center">손익 그래프</h2>
            <div style="height: 182px;">
                <canvas id="profitChart"></canvas>
            </div>
        </div>
        <div class="w-1/2 grid grid-cols-3 gap-3 text-center">
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
    <div class="max-w-6xl mx-auto mt-8">
        <h2 class="text-lg font-semibold mb-3">최근 10회분 베팅레코드</h2>
    </div>
    @auth
        <div class="max-w-6xl mx-auto overflow-x-auto border border-gray-200 rounded-2xl bg-white">
        <table class="min-w-full text-sm border-collapse">
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
                    <th class="border px-2 py-1">확정</th>
                    <th class="border px-2 py-1">잔고</th>
                    <th class="border px-2 py-1"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($userRecords as $record)
                    <tr data-bet="{{ $record->bet_amount }}" data-odds="{{ $record->odds }}" class="@if($record->result === 'win') bg-indigo-50 @elseif($record->result === 'lose') bg-fuchsia-50 @elseif($record->result === 'draw') bg-gray-100 @endif">
                        <td class="border px-2 py-1 text-center">{{ $userRecords->count() - $loop->index }}</td>
                        <td class="border px-2 py-1">{{ $record->betting_date->format('Y-m-d') }}</td>
                        <td class="border px-2 py-1 text-xs">{{ $record->title }}</td>
                        <td class="border px-2 py-1">{{ rtrim(rtrim(number_format($record->odds, 2, '.', ''), '0'), '.') }}</td>
                        <td class="border px-2 py-1">{{ number_format($record->bet_amount) }}₩</td>
                        <td class="border px-2 py-1 text-center">{{ $record->folder_count }}</td>
                        <td class="border px-2 py-1">{{ number_format($record->win_amount) }}₩</td>
                        <td class="border px-2 py-1">
                            @if($record->result === 'pending')
                                <select class="result-select border rounded px-2 py-1 text-sm appearance-none pr-7 bg-white">
                                    <option value="pending" {{ $record->result === 'pending' ? 'selected' : '' }}>진행중</option>
                                    <option value="win" {{ $record->result === 'win' ? 'selected' : '' }}>적중</option>
                                    <option value="lose" {{ $record->result === 'lose' ? 'selected' : '' }}>미적중</option>
                                    <option value="draw" {{ $record->result === 'draw' ? 'selected' : '' }}>적특</option>
                                </select>
                            @else
                                <span class="
                                    @if($record->result === 'win') text-blue-600
                                    @elseif($record->result === 'lose') text-red-600
                                    @elseif($record->result === 'draw') text-gray-500
                                    @endif
                                ">
                                    {{ $record->result === 'win' ? '적중' : ($record->result === 'lose' ? '미적중' : '적특') }}
                                </span>
                            @endif
                        </td>
                        <td class="border px-2 py-1 text-center profit-cell">
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
                        <td class="border px-2 py-1">
                            @if($record->balance >= 0)
                                <span class="text-blue-600">{{ number_format($record->balance) }}₩</span>
                            @else
                                <span class="text-red-600">{{ number_format($record->balance) }}₩</span>
                            @endif
                        </td>
                        <td class="border px-2 py-1">
                            <form action="{{ route('record.edit', ['id' => $record->id]) }}" method="GET">
                                <button class="text-indigo-400">편집</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="w-2/3 flex justify-end mt-3 mx-auto">
                <a href="{{ route('record.history', ['id' => $record->id ?? '']) }}" class="text-sm text-blue-400 hover:text-blue-600">전체 기록 보기</a>
        </div>
        <style>
            .profit-win { color: #2563eb; }   /* Tailwind의 text-blue-600 정도 */
            .profit-lose { color: #dc2626; }  /* Tailwind의 text-red-600 정도 */
            .profit-draw { color: #6b7280; }  /* 선택: 회색 */
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
                        // 적중, 미적중, 적특에 따른 수익 계산 & 색상 적용
                        switch(this.value) {
                            case "win":
                                result = (bet * odds) - bet;
                                cssClass = "profit-win";
                                break;
                            case "lose":
                                result = -bet;
                                cssClass = "profit-lose";
                                break;
                            case "draw":
                                alert('2폴더 이상 부분 적중특례인 경우, 배당값을 직접 편집 부탁드립니다.')
                                result = 0;
                                cssClass = "profit-draw";
                                break;
                            case "pending":
                                result = "?";
                                break;
                        }
                        // 초기화
                        profitCell.classList.remove("profit-win", "profit-lose", "profit-draw");
                        // 값 넣기 + 색상 적용
                        if(this.value === "pending") {
                            profitCell.textContent = "?";
                        } else {
                            profitCell.textContent = result.toLocaleString("ko-KR") + "₩";
                            if(cssClass) profitCell.classList.add(cssClass);
                        }
                    });
                });
                document.querySelectorAll(".record-form").forEach(form => {
                    const row = form.closest("tr");
                    const select = row?.querySelector(".result-select");
                    const hiddenResult = form.querySelector("input[name='result']");
                    if (!select || !hiddenResult) return;

                    form.addEventListener("submit", () => {
                        hiddenResult.value = select.value;
                    });
                });
            });
        </script>
    @endauth
@endsection