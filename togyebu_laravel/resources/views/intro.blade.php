@extends('layouts.common')
@section('title', ' Intro')
@section('content')
    <div class="flex flex-col items-center justify-center gap-8 py-10 px-4 min-h-screen">
        <p class="text-center text-lg leading-relaxed text-gray-800 ">
            연패에 분노벳, 무분별한 베팅으로 막대한 손실을 경험하셨나요?</br>
            적중보다 중요한것은 기록하고, 자신의 베팅습관을 리마인드하는 습관입니다.
            </br>
            </br>
            TGB는 베팅 기록을 통해 자신의 베팅 습관을 분석하고 개선할 수 있도록 도와주는 서비스입니다.
            </br>
            회원가입 후, 베팅 기록을 남기고, 통계와 분석을 통해 더 나은 수익 극대화를 실현하세요!
        </br></br>
            하단 배너를 누르시면, 로그인 페이지로 이동합니다!
        </p>
        <a href="{{ route('main.index') }}">
            <img src="{{ asset('images/intro.jpg') }}" alt="Intro" class="w-full max-w-lg h-auto rounded-3xl">
        </a>
    </div>
@endsection