@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Blogs</h3>
                        <p>
                            {{ $blog->title }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs">
            <div class="container-fluid">
                <div class="row mt-3">
                    <div class="col-md-7 offset-md-1">
                        <div class="list-group">

                                <div class="list-group-item">
                                    <div class="row">
                                        <div class="col-auto feature-image">
                                            @if($blog->feature_image)
{{--                                                <a href="{{ $blog->feature_image->getUrl() }}" target="_blank" style="display: inline-block">--}}
                                                    <img class="img-fluid" src="{{ $blog->feature_image->getUrl() }}" alt="Photo" style="width: 100%;">
{{--                                                </a>--}}
                                            @endif

                                        </div>

                                        <div class="col px-4 blog-details">
                                            <div>

                                                <h3>{{ $blog->title }}

                                                </h3>
                                                <span class="post-date">{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }} &nbsp;&nbsp;&nbsp;  <i class="fa fa-eye"></i>    Views: {{ $blog->views }}
                                                </span>

                                                <p class="mb-0">
                                                    {!! App\Providers\AppServiceProvider::renderOembed($blog->details) !!}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>



                        </div>
                    </div>

                    <div class="col-md-3">
                        @foreach($blogs as $blog)
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
                                                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}  <br><i class="fa fa-eye"></i>    Views: {{ $blog->views }}
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
.px-4 .image{
    float: left;
    margin-right: 20px;
    margin-bottom: 20px;
}
.px-4 .image.image-style-side{
    float: right;
    margin-right: 20px;
    margin-bottom: 20px;
}
.feature-image{ width: 100%!important; float: left; padding-bottom: 30px }
.list-group-item{ border: none!important; }
.blog-details{ max-width: 80%!important; margin: 0 auto}
.blog-details img{ width: 100%!important;}
.post-date{ font-size: 13px; color: #7D7D7D; }
.sidebar-image-thumb{ width: 50px; padding: 3px;  margin-right: 10px; }
.right-side-bar-top h3{ font-size: 30px; line-height: 45px }
.post-date{ width: 100%; float: left; margin-bottom: 20px;}
</style>
@push('script')

@endpush
