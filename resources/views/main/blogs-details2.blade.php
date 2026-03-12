@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Blogs</h3>
                        <p>{{ $blog->title }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8 col-sm-12">
                        <div class="row blog-box-row no-margin-left no-margin-right no-padding">
                            @if($blog->feature_image)
                                <div class="blog-image-box">
                                    <img src="{{ $blog->feature_image->getUrl() }}" alt="">
                                </div>
                            @endif
                            <div class="blog-title">
                                <a href="{{ route('blogDetails',[$blog->id,$blog->slug]) }}">   <h2><strong>
                                            {{ $blog->title }}
                                        </strong></h2></a>
                            </div>
                            <div class="blog-date-div">
                                <span class="blog-date">  <i class="fa fa-calendar"></i>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $blog->created_at)->format('d F, Y h:i A') }}</span>
                                <span>  <i class="fa fa-eye"></i>Views:  {{ $blog->views }}</span>
                                <span>  <i class="fa fa-share"></i>Share: 02</span>
                            </div>
                            <div class="blog-content-div">
                                <p>   {!! $blog->details !!} </p>
                            </div>
                        </div>
                        <div class="row blog-box-row no-margin-left no-margin-right no-padding">
                            <h5 style="line-height: 20px; margin-bottom: 0px; font-weight: bold;"> Tags:</h5>
                            @foreach($blog->tags as $tag)
                          <span class="tag-list">   {{ $tag->name }}</span>
                                <span class="tag-list">  AI in Business</span>
                                @endforeach
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 padding-left-50">
                        <div class="row">
                            <div class="blog-section-title">
                                <h1><strong>Top Articles</strong></h1>
                            </div>

                            @foreach($populers as $blog)
                                <div class="row sidebar-row">
                                    <div class="col-md-5 col-sm-12">
                                        <div class="blog-image-box">
                                            @if($blog->feature_image)
                                                <img src="{{ url('img/blog11111.jpg') }}" alt="">
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

                            <div class="row">
                                @foreach($blogCategories as $blogCategory)
                                    <div class="col-md-6 col-sm-12 browse-category-row">
                                        <div class="blog-image-category-box">
                                            <img  src="{{ url('img/category_Business.webp') }}" alt="">
                                        </div>
                                        <div class="blog-image-category-content d-flex align-items-center justify-content-center">
                                            <a href="{{ route('blogsCategory',[$blogCategory->id,$blogCategory->slug]) }}">
                                                <span>{{ $blogCategory->title }}</span>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach


                            </div>
                        </div>
                    </div>
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
    .blog-content-div .image{
        float: left;
        margin-right: 20px;
        margin-bottom: 20px;
    }
    .blog-content-div .image.image-style-side{
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





    .sidebar-image-thumb{ width: 50px; padding: 3px;  margin-right: 10px; }
    .right-side-bar-top h3{ font-size: 30px; line-height: 45px }
    .post-date{ width: 100%; float: left; margin-bottom: 20px;}
    /*blog page css*/
    .no-padding{ padding: 0px !important; }
    .margin-top-20{ margin-top: 20px !important; }
    .no-margin-left{ margin-left: 0px !important; }
    .no-margin-right{ margin-right: 0px !important; }
    .blog-box-row{ width: 100%; float: left; margin-bottom: 20px;}
    .blog-image-box{width: 100%; float: left; }
    .blog-image-box img{ width: 100%; float: left;  }
    .blog-title{ width: 100%; float: left; text-align: left; padding:  0px; font-size: 14px;  color: #0A0A09}
    .blog-sidebar-title{ width: 100%; float: left; text-align: left; padding:  0px;}
    .blog-sidebar-title h2{ font-size: 14px;  color: #0A0A09; font-size: 22px; font-weight: bold}
    .blog-date-div{ width: 100%; float: left; padding:  0px; }
    .blog-date-div span{ margin-right:  30px; color: #A432CF }
    .blog-date-div span > i{ margin-right:  5px; }
    .blog-content-div{ width: 100%; float: left; padding:  0px; padding-top: 15px;}
    .blog-content-div p{  margin-bottom: 0px}
    /*.content-right{ padding-left: 15px}*/
    /*sidebar box*/
    .blog-box-sidebar{ width: 100%; float: left; margin-bottom: 20px; padding: 20px!important;}
    .blog-section-title{font-weight: bolder; float: left; width: 100%; margin-bottom: 20px}
    .padding-left-30{ padding-left: 30px!important;}
    .padding-left-50{ padding-left: 50px!important;}
    .sidebar-row{ width: 100%; float: left; margin-bottom: 20px; }

    .blog-image-category-box{width: 80px; height: 80px; float: left; margin-right: 10px;}
    .blog-image-category-box img{width: 100%;}
    .blog-image-category-content{ width: 150px; height: 80px; float: left; text-align: center; padding:  0px; font-size: 14px;  color: #0A0A09}
    .browse-category-row{ margin-bottom: 20px; }
    .blog-image-category-content a{color: #0A0A09}
    .tag-list{background:#8D12D1;color: white; padding: 2px 6px; margin: 0px 5px; line-height: 20px; border-radius: 5px; font-size: 12px; }

    @media  (min-width: 1200px) and (max-width: 1500px) {
        .col-md-6-1200-1500{ width: 100%!important; flex: 0 0 100%;}
    }
</style>


