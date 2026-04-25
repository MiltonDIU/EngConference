@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Blogs</h3>
                        <p>{{ $subtitle??"" }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs margin-top-40">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 col-sm-12">
                        @foreach($blogs as $blog)
                            <div class="row blog-box-row no-margin-left no-margin-right no-padding">
                                @if($blog->feature_image)
                                    <div class="col-md-6 col-sm-12 no-padding ">
                                        <div class="blog-image-box">
                                            <img src="{{ $blog->feature_image->getUrl() }}" alt="">
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6 col-sm-12 content-right padding-left-30">
                                    <div class="blog-title blog-title-top-margin-no">
                                        <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">   <h2><strong>
                                                    {{ $blog->title }}


                                                </strong></h2></a>
                                    </div>
                                    <div class="blog-date-div">
                                        <span class="blog-date">  <i class="fa fa-calendar"></i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}</span>
                                        <span>  <i class="fa fa-eye"></i>Views:  {{ $blog->views }}</span>
                                        {{--                                <span>  <i class="fa fa-share"></i>Share: 02</span>--}}
                                    </div>
                                    <div class="blog-content-div">
                                        <p>   {!! $blog->summary !!} </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach



                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="row">
                            <div class="blog-section-title">
                                <h1><strong>Top Articles</strong></h1>
                            </div>

                            @foreach($populers as $blog)
                                <div class="row sidebar-row">
                                    <div class="col-md-5 col-sm-12">
                                        <div class="blog-image-box">
                                            @if($blog->feature_image)
                                                <img src="{{ $blog->feature_image->getUrl()  }}" alt="">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-7 col-sm-12">
                                        <div class="blog-sidebar-title">
                                            <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">   <h2>{{ $blog->title??"" }}</h2></a>
                                        </div>
                                        <div class="blog-date-div">
                                            <span class="blog-date">  <i class="fa fa-calendar"></i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach





                            <div class="blog-section-title margin-top-20">
                                <h1><strong>Browse by Category</strong></h1>
                            </div>
                            @foreach($blogCategories as $blogCategory)
                                <div class=" col-sm-12 col-md-6 col-lg-6  browse-category-row">
                                    <div class="blog-image-category-box">
                                        <img  src="{{ $blogCategory->feature_image->getUrl('thumb') }}" alt="">
                                    </div>
                                    <div class="blog-image-category-content d-flex align-items-center">
                                        <a href="{{ route('blogsCategory',[$blogCategory->id,$blogCategory->slug]) }}">
                                            <span><strong>{{ $blogCategory->title }} ({{ ($blogCategory->post!=null)?count($blogCategory->post):0 }})</strong></span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </main>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ url('css/blog.css') }}">
@endpush
