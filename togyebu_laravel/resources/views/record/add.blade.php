@extends('layouts.common')
@section('title', '신규추가')
@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-semibold mb-6">신규 베팅 추가</h1>

    <form action="{{ route('record.store') }}" method="POST">
        @csrf

        <div class="bg-white border border-gray-200 rounded-2xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">날짜</label>
                    <input type="text" name="betting_date" value="{{ old('betting_date', now()->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm" maxlength="10" oninput="autoHyphenDate(this)">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">타이틀 / 메모</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm"
                           placeholder="예) 바르셀로나 vs 아스날 (아스날 승)">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">배당률</label>
                    <input type="text" step="0.01" name="odds" id="odds" value="{{ old('odds') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm" placeholder="2.35">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">베팅 금액</label>
                    <input type="text" name="bet_amount" id="bet_amount" value="{{ old('bet_amount') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm" placeholder="5000">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">폴더 수</label>
                    <input type="text" name="folder_count" value="{{ old('folder_count') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm" placeholder="3">
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 mt-6 max-w-3xl mx-auto">
            <p class="text-sm">예상 적중 금액: 
                <span id="expected" class="text-rose-700 text-xl font-bold">0</span> 원
            </p>
            <div class="flex gap-3 w-full sm:w-auto">
                <a href="{{ route('main.index') }}" class="flex-1 sm:flex-none text-center px-5 py-2.5 border rounded-xl text-sm hover:bg-gray-50">취소</a>
                <button type="submit" class="flex-1 sm:flex-none bg-rose-600 hover:bg-rose-700 text-white px-8 py-2.5 rounded-xl font-medium text-sm transition">
                    저장하기
                </button>
            </div>
        </div>
    </form>
</div>
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