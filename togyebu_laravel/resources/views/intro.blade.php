@extends('layouts.common')
@section('title', ' Intro')
@section('content')
    <div class="flex flex-col items-center justify-center gap-8 py-10 px-4 min-h-screen">
        <p class="text-center text-2xl font-semibold leading-relaxed text-gray-800 max-w-md">
            토계부에 오신걸 환영합니다. </br>
        </p>
        <a href="{{ route('main.index') }}">
            <img src="{{ asset('images/intro.jpg') }}" alt="Intro" class="w-full max-w-lg h-auto rounded-3xl">
        </a>
    </div>
@endsection