<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chiron+Sung+HK:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Bet Log System TGB</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Mobile Drawer Styles */
        .mobile-drawer {
            transition: transform 0.3s ease-out;
        }
        .mobile-drawer.open {
            transform: translateX(0);
        }
        .drawer-overlay {
            transition: opacity 0.3s ease-out;
        }
        @media (min-width: 1024px) {
            .desktop-sidebar {
                display: flex;
            }
            .mobile-header {
                display: none;
            }
        }
        @media (max-width: 1023px) {
            .desktop-sidebar {
                display: none !important;
            }
            .mobile-header {
                display: flex;
            }
            .main-content {
                padding-top: 4rem; /* space for mobile header */
            }
        }
    </style>
</head>
<body class="lg:flex min-h-screen bg-gray-50">

    {{-- ========== DESKTOP SIDEBAR (lg 이상에서만 보임) ========== --}}
    <div class="desktop-sidebar">
        @include('layouts.menu')
    </div>

    {{-- ========== MOBILE TOP HEADER + HAMBURGER ========== --}}
    <div class="mobile-header fixed top-0 left-0 right-0 z-40 bg-white border-b border-gray-200 h-14 items-center px-4 flex lg:hidden">
        <button id="mobile-menu-btn"
                class="p-2 -ml-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition"
                aria-label="메뉴 열기">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <div class="flex-1 text-center">
            <a href="{{ route('main.index') }}" class="font-bold text-lg text-gray-800 tracking-tight">TGB</a>
        </div>

        <div class="flex items-center gap-1 text-sm">
            @auth
                <a href="{{ route('profile.show') }}" class="px-2 py-1 text-gray-600 hover:text-gray-900 text-xs">
                    {{ Auth::user()->name }}
                </a>
            @else
                <a href="{{ route('login') }}" class="text-xs text-gray-600">로그인</a>
            @endauth
        </div>
    </div>

    {{-- ========== MAIN CONTENT AREA ========== --}}
    <main class="flex-1 w-full min-w-0 main-content lg:p-6 p-4 pb-12">
        {{-- Top right actions (desktop only) --}}
        <div class="hidden lg:flex justify-end items-center space-x-2 mb-4">
            @auth
                <a href="{{ route('profile.show') }}"
                   class="inline-block px-4 py-1.5 text-sm text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm leading-normal">
                    {{ Auth::user()->name }}
                </a>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="inline-block px-4 py-1.5 text-sm text-[#1b1b18] border border-transparent hover:border-[#19140035] rounded-sm leading-normal">
                    로그아웃
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-[#1b1b18]">로그인</a>
                <a href="{{ route('register') }}" class="text-sm border px-3 py-1 rounded">회원가입</a>
            @endauth
        </div>

        {{-- Alerts --}}
        @if ($errors->any())
            <div id="alert-error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div id="alert-success" class="max-w-3xl mx-auto mt-4 p-3 rounded bg-green-100 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    {{-- ========== MOBILE DRAWER (HAMBURGER MENU) ========== --}}
    <div id="mobile-drawer"
         class="mobile-drawer fixed inset-y-0 left-0 z-50 w-72 bg-white shadow-2xl transform -translate-x-full lg:hidden border-r border-gray-200">
        <div class="flex items-center justify-between h-14 px-4 border-b">
            <span class="font-bold text-lg">TGB 메뉴</span>
            <button id="mobile-close-btn" class="p-2 text-gray-500 hover:text-gray-700" aria-label="닫기">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="overflow-y-auto h-[calc(100%-3.5rem)]">
            @include('layouts.menu', ['mobile' => true])
        </div>
    </div>

    {{-- Backdrop --}}
    <div id="mobile-drawer-backdrop"
         class="hidden fixed inset-0 bg-black/40 z-40 lg:hidden"
         onclick="closeMobileDrawer()"></div>

</body>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Alert fade out
    function fadeOut(id) {
        const el = document.getElementById(id);
        if (el) {
            setTimeout(() => {
                el.classList.add("opacity-0");
                setTimeout(() => el.remove(), 600);
            }, 2800);
        }
    }
    fadeOut("alert-error");
    fadeOut("alert-success");

    // Mobile Drawer Controls
    const menuBtn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('mobile-close-btn');
    const drawer = document.getElementById('mobile-drawer');
    const backdrop = document.getElementById('mobile-drawer-backdrop');

    function openMobileDrawer() {
        if (!drawer) return;
        drawer.classList.add('open');
        drawer.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    window.closeMobileDrawer = function() {
        if (!drawer) return;
        drawer.classList.remove('open');
        drawer.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    };

    if (menuBtn) menuBtn.addEventListener('click', openMobileDrawer);
    if (closeBtn) closeBtn.addEventListener('click', window.closeMobileDrawer);

    // Close drawer on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && drawer && !drawer.classList.contains('-translate-x-full')) {
            window.closeMobileDrawer();
        }
    });
});
</script>

</html> 