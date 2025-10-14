@extends('layouts.common')
@section('title', ' 개발일지')
@section('content')
    <div class="w-2/3 mx-auto mt-10 justify-center">
        <h1 class="text-2xl font-bold mb-4">개발일지</h1>
        <div class="space-y-6">
            <div class="p-4 border border-rose-200 rounded-lg bg-rose-50">
                <h2 class="text-xl font-semibold mb-2">TO DO LIST (2024년 10월 시점)</h2>
                <ul class="list-disc list-inside space-y-1">
                    <li class="text_linethrough">history page 삭제기능 추가</li>
                    <li>history page 검색기능 추가</li>
                    <li>history page 페이징처리 추가</li>
                    <li class="text_linethrough">add page 날짜 자동 하이픈기능 추가</li>
                    <li>edit page 날짜 자동 하이픈기능 추가</li>
                    <li>add page, 여러개 등록할 수 있도록 추가등록버튼</li>
                </ul>
            </div>
        </div>
    </div>
@endsection