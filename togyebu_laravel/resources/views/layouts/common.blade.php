<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chiron+Sung+HK:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - TOGYEBU(가칭)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex">
    @include('layouts.menu')
    <main class="flex-1 p-6">
        <div class="flex justify-end space-x-2">
            @auth
                <a href="{{ route('profile.show') }}" class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm leading-normal">
                    {{ Auth::user()->name }}
                </a>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm leading-normal">
                    로그아웃
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="inline-block px-5 py-1.5 text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm text-sm leading-normal">
                    로그인
                </a>
                <a href="{{ route('register') }}" class="inline-block px-5 py-1.5 border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] rounded-sm text-sm leading-normal">
                    회원가입
                </a>
            
            @endauth
        </div>
        @if ($errors->any())
            <div id="alert-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 transition-opacity duration-1000">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div id="alert-success" class="w-2/3 mx-auto mt-4 p-3 rounded bg-green-100 text-green-800 text-sm transition-opacity duration-1000">
                {{ session('success') }}
            </div>
        @endif
        @yield('content')
    </main>
</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // 공통 처리 함수
        function fadeOut(id) {
            const el = document.getElementById(id);
            if (el) {
                setTimeout(() => {
                    el.classList.add("opacity-0"); // 투명화
                    // 애니메이션 끝난 후 DOM에서 제거하고 싶으면 아래 추가
                    setTimeout(() => el.remove(), 1000);
                }, 3000); // 3초 뒤 시작
            }
        }

        fadeOut("alert-error");
        fadeOut("alert-success");
    });
</script>

</html> 