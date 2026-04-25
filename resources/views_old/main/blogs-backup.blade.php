@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Blogs</h3>
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs">
            <div class="container-fluid">
                <div class="row mt-3">
                    <div class="col-md-7 offset-md-1">
                            @foreach($blogs as $blog)
                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-auto">
                                            @if($blog->feature_image)

                                                    <img class="img-fluid" src="{{ $blog->feature_image->getUrl('preview') }}" alt="Photo" style="max-height: 200px;">
                                            @endif

                                        </div>

                                        <div class="col px-4">
                                            <div>
                                                <div class="float-right">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}
                                                    <br>
                                                    <i class="fa fa-eye"></i>    Views: {{ $blog->views }}
                                                </div>
                                                <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">
                                                    <h3>{{ $blog->title }}</h3>
                                                </a>
                                                <p class="mb-0">
                                                    {!! $blog->summary !!}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    <div class="col-md-3">
                        @foreach($populers as $blog)
                            <div class="list-group-item">
                                <div class="row">
                                    <div class="col px-4">
                                        <div class="right-side-bar-top">


                                            <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">
                                                <h3>
                                                    @if($blog->feature_image)
                                                        <img class="sidebar-image-thumb" src="{{ $blog->feature_image->getUrl('thumb') }}" alt="Photo" >
                                                    @endif
                                                    {{ $blog->title }}
                                                </h3>
                                            </a>
                                            <div class="float-left post-date">
                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}
                                                <br>
                                                <i class="fa fa-eye"></i>    Views: {{ $blog->views }}
                                            </div>
                                        </div>
                                        <p class="mb-0">
                                            {!! $blog->summary !!}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
<style>
.blogs{
    margin-bottom: 20px;
}
.sidebar-image-thumb{ width: 50px; padding: 3px;  margin-right: 10px; }
.right-side-bar-top h3{ font-size: 30px; line-height: 45px }
.post-date{ width: 100%; float: left; margin-bottom: 20px;}
</style>
