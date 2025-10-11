@extends('layouts.common')
@section('title', ' 개발일지')
@section('content')
    <div class="w-2/3 mx-auto mt-10 justify-center">
        <h1 class="text-2xl font-bold mb-4">개발일지</h1>
        <div class="space-y-6">
            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                <h2 class="text-xl font-semibold mb-2">2024-10-12</h2>
                <ul class="list-disc list-inside space-y-1">
                    <li>history page 삭제기능 추가</li>
                    <li>history page 검색기능 추가</li>
                    <li>history page 페이징처리 추가</li>
                    <li>add page 날짜 자동 하이픈기능 추가</li>
                    <li>history, main page 잡다한 버그 수정</li>
                </ul>
            </div>
        </div>
    </div>
@endsection