<aside class="w-52 h-screen text-gray-700 flex flex-col border-r border-gray-300">
    <!-- 로고/브랜드 영역 -->
    <div class="h-16 flex items-center justify-center border-b border-gray-300">
        <a href="{{ route('main.index') }}" class="text-lg font-bold">TGB</a>
    </div>

    <!-- 메뉴 리스트 -->
    <nav class="flex-1 overflow-y-auto">
        <ul class="flex flex-col space-y-1 p-3 text-sm">
            @auth
                <li>
                    <a href="{{ route('record.history') }}"
                    class="block px-3 py-2 rounded-md hover:bg-gray-100 transition">
                        {{ Auth::user()->name }}님의 배팅기록
                    </a>
                </li>
                <li>
                    <a href="#"
                    class="block px-3 py-2 rounded-md hover:bg-gray-100 transition">
                        입출금 내역기록
                    </a>
                </li>
                <li>
                    <a href="#"
                    class="block px-3 py-2 rounded-md hover:bg-gray-100 transition">
                        메모장
                    </a>
                </li>
            @endauth
            <li>
                <a href="#"
                   class="block px-3 py-2 rounded-md hover:bg-gray-100 transition">
                    토론게시판
                </a>
            </li>
            <li>
                <!-- <a href="#"
                   class="block px-3 py-2 rounded-md hover:bg-gray-700 transition">
                    회원정보수정
                </a> -->
            </li>
            <!-- 필요하면 계속 추가 -->
        </ul>
    </nav>
</aside>
