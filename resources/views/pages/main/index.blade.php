@extends('layouts.main')

@section('content')
    <div role="main" class="main">
        @include('pages.main.sections.aboutMe')
        @include('pages.main.sections.experience')
        @include('pages.main.sections.blog')
    </div>
@endsection

