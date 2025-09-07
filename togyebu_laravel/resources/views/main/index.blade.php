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
            <p class="font-bold text-sky-800">
                @auth 
                    {{ $users->name }}</p>님의 최근 10경기 기록 ( 승 패 승률 ) 잔고: 
                @else 
                    <p class="font-bold">로그인 후, 이용 부탁드립니다.</p> 
                @endauth
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
                    <th class="border px-2 py-1">예상수익</th>
                    <th class="border px-2 py-1"></th>
                    <th class="border px-2 py-1"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                    <tr data-bet="{{ $record->bet_amount }}" data-odds="{{ $record->odds }}">
                        <td class="border px-2 py-1 text-center">{{ $record->id }}</td>
                        <td class="border px-2 py-1">{{ $record->betting_date->format('y/m/d') }}</td>
                        <td class="border px-2 py-1">{{ $record->title }}</td>
                        <td class="border px-2 py-1">{{ rtrim(rtrim(number_format($record->odds, 2, '.', ''), '0'), '.') }}</td>
                        <td class="border px-2 py-1">{{ number_format($record->bet_amount) }}₩</td>
                        <td class="border px-2 py-1 text-center">{{ $record->folder_count }}</td>
                        <td class="border px-2 py-1">{{ number_format($record->win_amount) }}₩</td>
                        <td class="border px-2 py-1">
                            <select class="result-select border rounded px-2 py-1 text-sm appearance-none pr-7 bg-white">
                                <option value="pending">진행중</option>
                                <option value="win">적중</option>
                                <option value="lose">미적중</option>
                                <option value="draw">적특</option>
                            </select>
                        </td>
                        <td class="border px-2 py-1 text-center profit-cell">?</td>
                        <th class="border px-2 py-1">
                            <button class="text-rose-600">확정</button>
                        </td>
                        <th class="border px-2 py-1">
                            <button class="text-indigo-400">편집</button>
                        </th>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="w-2/3 flex justify-end mt-3 mx-auto">
                <a href="{{ route('record.history') }}" class="text-sm text-blue-400 hover:text-blue-600">전체 기록 보기</a>
        </div>
        <style>
            .profit-win { color: #2563eb; font-weight: bold;}   /* Tailwind의 text-blue-600 정도 */
            .profit-lose { color: #dc2626; font-weight: bold;}  /* Tailwind의 text-red-600 정도 */
            .profit-draw { color: #6b7280; font-weight: bold;}  /* 선택: 회색 */
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
            });
        </script>
    @endauth
@endsection