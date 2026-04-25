@extends('layouts.main')

@section('content')
    @include('main.sections.intro')

    <main id="main">
           @include('main.sections.sponsors')
           
        <!--@include('main.sections.about')-->

        @include('main.sections.CallforPapers')
        @include('main.sections.schedule')

        @include('main.sections.speakers')
        @include('main.sections.advisors')

        @include('main.sections.organizing_committee')



{{--    @include('main.sections.hotels')--}}

    <!--@include('main.sections.gallery')-->

{{--        @include('main.sections.strategic-partner')--}}

     

{{-- @include('main.sections.club-partner')--}}




{{--    @include('main.sections.subscribe')--}}

 <!--@include('main.sections.buy_ticket')-->
        <!--@include('main.sections.register')-->
        @include('main.sections.contact')
                <!--@include('main.popup')-->

        @include('main.sections.venues')
            @include('main.sections.faq')
    </main>
@endsection
