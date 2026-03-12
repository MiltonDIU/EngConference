@extends('layouts.theme2')

@section('content')
@include('theme2.sections.intro',['event',$event])

<main id="main">
  @include('theme2.sections.about')

  @include('theme2.sections.speakers')

  @include('theme2.sections.schedule')

  @include('theme2.sections.venues')

  @include('theme2.sections.hotels')

  @include('theme2.sections.gallery')

  @include('theme2.sections.sponsors')

  @include('theme2.sections.faq')

  @include('theme2.sections.subscribe')

  @include('theme2.sections.buy_ticket')

  @include('theme2.sections.contact')
</main>
@endsection
