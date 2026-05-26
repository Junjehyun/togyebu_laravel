@php
    $isMobile = $mobile ?? false;
@endphp

@if (!$isMobile)
    {{-- Desktop Sidebar Version --}}
    <aside class="w-52 h-screen text-gray-700 flex flex-col border-r border-gray-300 bg-white shrink-0">
        <div class="h-16 flex items-center justify-center border-b border-gray-200">
            <a href="{{ route('main.index') }}" class="text-lg font-bold tracking-tight">TGB</a>
        </div>

        <nav class="flex-1 overflow-y-auto py-2">
            <ul class="flex flex-col space-y-1 px-2 text-sm">
                @include('layouts._menu_links')
            </ul>
        </nav>
    </aside>
@else
    {{-- Mobile Drawer Version (simpler padding, no fixed width) --}}
    <nav class="py-2">
        <ul class="flex flex-col space-y-1 px-2 text-sm">
            @include('layouts._menu_links')
        </ul>
    </nav>
@endif
