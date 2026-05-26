{{-- Reusable menu links for both desktop sidebar and mobile drawer --}}
@auth
    <li>
        <a href="{{ route('record.history') }}"
           class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition text-[15px]">
            <span class="text-lg">📋</span>
            <span>{{ Auth::user()->name }}님의 배팅기록</span>
        </a>
    </li>
    <li>
        <a href="{{ route('record.transaction') }}"
           class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition text-[15px]">
            <span class="text-lg">💰</span>
            <span>입출금 내역기록</span>
        </a>
    </li>
    <li>
        <a href="#"
           class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition text-[15px] text-gray-400 pointer-events-none">
            <span class="text-lg">📝</span>
            <span>메모장 <span class="text-[10px]">(준비중)</span></span>
        </a>
    </li>
@else
    <li>
        <a href="{{ route('login') }}"
           class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 transition text-[15px]">
            로그인
        </a>
    </li>
@endauth

<li>
    <a href="#"
       class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition text-[15px] text-gray-400 pointer-events-none">
        <span class="text-lg">💬</span>
        <span>토론게시판 <span class="text-[10px]">(준비중)</span></span>
    </a>
</li>
<li>
    <a href="#"
       class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition text-[15px] text-gray-400 pointer-events-none">
        <span class="text-lg">✉️</span>
        <span>문의 / 건의 <span class="text-[10px]">(준비중)</span></span>
    </a>
</li>
<li>
    <a href="#"
       class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition text-[15px]">
        <span class="text-lg">📢</span>
        <span>공지사항</span>
    </a>
</li>

@auth
    <li class="pt-2 mt-2 border-t">
        <a href="{{ route('admin.devlog') }}"
           class="flex items-center gap-2 px-3 py-2.5 rounded-lg hover:bg-rose-50 text-rose-600 font-semibold transition text-[15px]">
            <span class="text-lg">🛠️</span>
            <span>개발일지</span>
        </a>
    </li>
@endauth