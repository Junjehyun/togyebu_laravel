@extends('layouts.common')
@section('title', '신규추가')
@section('content')
    <div class="mt-5">
        <h1 class="text-xl flex justify-center">신규 추가</h1>
    </div>
    <form action="{{ route('record.addStore') }}" method="POST">
        @csrf
        <div class="flex justify-center">
            <!-- 타이틀 행 -->
            <table class="w-2/3 border border-gray-300 text-sm mt-10">
                <thead class="bg-zinc-50">
                    <tr>
                        <th class="border px-3 py-2">제목</th>
                        <th class="border px-3 py-2">날짜</th>
                        <th class="border px-3 py-2">폴더수</th>
                        <th class="border px-3 py-2">배당</th>
                        <th class="border px-3 py-2">베팅금</th>
                    </tr>
                </thead>
                <!-- 입력 행 -->
                <tbody>
                    <tr>
                        <td class="border px-3 py-2">
                            <input type="text" name="description"
                                    class="w-full border rounded px-2 py-1 text-sm"
                                    placeholder="자유롭게 입력">
                        </td>
                        <td class="border px-3 py-2">
                            <input type="text" name="date"
                                    class="w-full border rounded px-2 py-1 text-sm" placeholder="ex)19001212">
                        </td>
                        <td class="border px-3 py-2">
                            <input type="text" name="folder_count"
                                    class="w-full border rounded px-2 py-1 text-sm"
                                    placeholder="3">
                        </td>
                        <td class="border px-3 py-2">
                            <input type="text" step="0.01" name="odds" id="odds"
                                    class="w-full border rounded px-2 py-1 text-sm"
                                    placeholder="2.35">
                        </td>
                        <td class="border px-3 py-2">
                            <input type="text" name="bet_amount" id="bet_amount"
                                    class="w-full border rounded px-2 py-1 text-sm"
                                    placeholder="100,000">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="w-2/3 flex justify-between items-center mx-auto mt-4">
            <p>예상적중금액: <span id="expected" class="text-rose-800 text-lg font-bold">0 </span> 원</p>
            <div class="">
                <!-- 버튼 -->
                <a href="{{ route('main.index') }}" class="inline-block bg-pink-300 hover:bg-pink-400 text-white rounded px-3 py-1">
                    뒤로
                </a>
                <button type="submit" class="bg-purple-300 text-white px-3 py-1 rounded hover:bg-purple-400">
                    저장
                </button>
            </div>
        </div>
    </form>
    <script>
        const oddsInput = document.getElementById('odds');
        const betInput = document.getElementById('bet_amount');
        const expectedSpan = document.getElementById('expected');

        function updateExpected() {
            const odds = parseFloat(oddsInput.value) || 0;
            const bet = parseFloat(betInput.value) || 0;
            const expected = odds * bet;
            expectedSpan.textContent = expected.toLocaleString(); // 1000 단위 콤마
        }

        oddsInput.addEventListener('input', updateExpected);
        betInput.addEventListener('input', updateExpected);

        // 입력할 때는 숫자만 유지
        betInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, ''); // 숫자만 허용
        });

        // 입력 끝나고 포커스 벗어날 때 콤마 추가
        betInput.addEventListener('blur', function () {
            let value = this.value.replace(/,/g, ''); // 기존 콤마 제거
            if (value) {
                this.value = Number(value).toLocaleString(); // 천 단위 콤마 추가
            }
        });

        // 다시 focus되면 콤마 제거해서 편집 가능하게
        betInput.addEventListener('focus', function () {
            this.value = this.value.replace(/,/g, '');
        });
    </script>
@endsection