@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>{{ $schedule->title??"" }}</h3>
                      
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs margin-top-40">
            <div class="container">
                <div class="row padding-top-bottom-50 about-schedule">
                    <div class="col-md-3 offset-2 col-sm-12">
                        <h2 class="schedule-heading">ABOUT THE</h2>
                        <h2 class="schedule-heading schedule-heading-color schedule-heading-bold">WORKSHOP</h2>
                        @guest
                        <a class="reg-schedule-page" href="{{ route('book-ticket') }}">REGISTER NOW</a>
                            @endguest
                    </div>

                    <div class="col-md-6 col-sm-12">

                        {!! $schedule->about??"" !!}
                        <div class="blog-date-div">
                            <span class="schedule-date-time">  <i class="fa fa-calendar"></i>
      &nbsp;

                                {{ \Carbon\Carbon::createFromFormat('Y-m-d',$settings['event_date'])->format(' M d, Y') }}
                              &nbsp;&nbsp;      &nbsp;&nbsp;&nbsp;  <i class="fa fa-clock-o"></i>
                                {{ \Carbon\Carbon::createFromFormat('H:i:s',$schedule->start_time)->format('h:i A') }}
                            </span>

                        </div>
                    </div>

                </div>
            </div>
            </div>
        </section>
        @php
            $tools = json_decode($schedule->tools) ;
            $benefits = json_decode($schedule->benefits);

        @endphp



        <section class="content blogs margin-top-40" style="background: black;padding: 0 0 25px 0;color: #eee;font-size: 14px;">
            <div class="container">
                <div class="row padding-top-bottom-50">
                    @if($benefits>0)
                        <div class="col-md-10 offset-2 col-sm-12">
                            <h2 class="schedule-heading schedule-heading-bold schedule-heading-color-white padding-m-bottom">BENEFITS</h2>
                        </div>
                        <div class="col-md-10 offset-1 col-sm-12" style="margin-top: 30px">
                            <!-- Set up your HTML -->
                            <div class="owl-carousel carousel-1">
                                @foreach($benefits as $key=> $benefit)
                                    <div style=" display: flex;
            align-items: center;
            justify-content: center; height: 150px"> {{$benefit->title}}
                                        <br>
                                        @if($benefit->link!=null)
                                            <a class="btn claim-button" href="{{ $benefit->link??"#"  }}" target="_blank">Claim</a>
                                        @endif

                                    </div>
                                @endforeach
                            </div>

                        </div>
                    @endif
                    @if($tools>0)

                        <div class="col-md-3 offset-2 col-sm-12">
                            <h2 class="schedule-heading schedule-heading-color-white">TOOLS &</h2>
                            <h2 class="schedule-heading schedule-heading-bold schedule-heading-color-white padding-m-bottom">TECHNOLOGIES</h2>
                        </div>
                        <div class="col-md-7 col-sm-12">
                            <ul>
                                @foreach($tools as $key=>$tool)
                                    <li>{{ $tool }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
            </div>
        </section>



        <section class="content blogs margin-top-40">
            <div class="container">
                <div class="row padding-top-bottom-50">
                    <div class="col-md-3 offset-2 col-sm-12" style="margin-top: 50px; margin-bottom: 50px">
                        <h2 class="schedule-heading ">ABOUT THE</h2>
                        <h4 class="schedule-heading schedule-heading-bold schedule-heading-color">
                            SPEAKER</h4>
                    </div>
                    <div class="col-md-6 col-sm-12">

                        {{--                        <div class="owl-carousel speakers">--}}
                        {{--                            @foreach($benefits as $key=> $benefit)--}}
                        {{--                                <div> {{$benefit->title}}--}}
                        {{--                                    <br>--}}
                        {{--                                    <a class="btn claim-button" href="{{ $benefit->link  }}" target="_blank">Claim</a>--}}
                        {{--                                </div>--}}
                        {{--                            @endforeach--}}
                        {{--                        </div>--}}

                        <div class="owl-carousel carousel-2">
                            <!-- Your carousel items go here -->
                            @if($schedule->speakers->count() > 0)
                                @foreach($schedule->speakers as $key => $speaker)
                                    <div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                @if($speaker->photo)
                                                    <img src="{{ $speaker->photo->getUrl() }}" alt="Card image" class="card-img-top round-image">
                                                @endif

                                                <div class="social-links social-center">
                                                    <a href="{{ $speaker->twitter }}"><i class="fa fa-twitter"></i></a>
                                                    <a href="{{ $speaker->facebook }}"><i class="fa fa-facebook"></i></a>
                                                    <a href="{{ $speaker->linkedin }}"><i class="fa fa-linkedin"></i></a>
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="card-body">
                                                    <p class="card-text">
                                                        {!! $speaker->description !!}
                                                    </p>

                                                    @if($speaker->slug)
                                                      <a class="btn btn-primary" style="background: white; color:#8D12D1" href="{{ route('speaker',['slug' => $speaker->slug]) }}"> View Profile</a>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach

                            @else

                                <div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            @if($schedule->speaker->photo)
                                                <img src="{{ $schedule->speaker->photo->getUrl() }}" alt="Card image" class="card-img-top round-image">
                                            @endif

                                            <div class="social-links social-center">
                                                <a href="{{ $schedule->speaker->twitter }}"><i class="fa fa-twitter"></i></a>
                                                <a href="{{ $schedule->speaker->facebook }}"><i class="fa fa-facebook"></i></a>
                                                <a href="{{ $schedule->speaker->linkedin }}"><i class="fa fa-linkedin"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body">
                                                <p class="card-text">
                                                    {!! $schedule->speaker->description !!}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            @endif




                            <!-- Add more items as needed -->
                        </div>
                        @guest
                            <a class="reg-schedule-page" href="{{ route('book-ticket') }}">REGISTER NOW</a>
                        @endguest
                    </div>

                </div>
            </div>
            </div>
        </section>



    </main>
@endsection

@push('style')
    <link rel="stylesheet" href="{{ url('owl-carousel2/dist/assets/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ url('owl-carousel2/dist/assets/owl.theme.default.min.css')}}">
    <style>
        .reg-schedule-page {
            color: #fff;
            background: #8D12D1;
            background: #8D12D1;
            background: #b332e2;
            background: #8D12D1;
            padding: 7px 22px;
            border-radius: 50px;
            border: 2px solid #8D12D1;
            border: 2px solid #8D12D1;
            border: 2px solid #b332e2;
            border: 2px solid #8D12D1;
            transition: all ease-in-out 0.3s;
            font-weight: 500;
            margin-top: 2px;
            line-height: 5;
            font-size: 15px;
            font-width: bold;
        }
        .padding-top-bottom-50{ padding: 50px 0px;}
        .schedule-date-time { font-size: 20px; color:  #8D12D1; }
        .schedule-heading{ font-size: 33px; margin: 0px;  }
        .schedule-heading-color{ color: #8D12D1;  }
        .schedule-heading-color-white{ color: white;  }
        .schedule-heading-bold{font-weight: bold;  }
        .about-schedule {
            display: flex;
            align-items: center;
        }

        /*owl carousel*/
        .carousel-1 > div > div > div {
            height: 190px;
            background: #8D12D1;
            border-radius: 10px;
            padding: 20px;
            font-size: 15px;
            font-weight: bold;
        }
        .carousel-2 > div > div > div {
            min-height: 190px;
            background: #8D12D1;
            border-radius: 10px;
            padding: 20px;
            font-size: 15px;
            font-weight: bold;
        }
        .owl-nav { float: right; }
        .owl-nav > button > span { font-size: 60px; margin-right: 15px; color: #8D12D1; }
        .claim-button {
            background: white;
            color: #8D12D1;
            border-radius: 10px;
            padding: 5px 20px;
            font-weight: bold;
            font-size: 15px;
            bottom: 10px;
            position: absolute;
            margin-bottom: 10px;
            margin: 0 auto;
            left: 10px;
            right: 10px;
        }
        .owl-carousel {
            overflow: hidden;
        }
        .owl-stage {
            display: flex;
        }

        @media only screen and (min-width: 767px) {
            .owl-item {
                width: 100%;
                margin-right: -50%; /* Adjust this value based on your needs */
            }
            .owl-item.active {
                transform: translateX(45%); /* Move the active item to the left by 50% */
            }
            .carousel-2 div div .owl-item {
                width: 100%;
                margin-right: -0%; /* Adjust this value based on your needs */
            }
            .carousel-2 div div .owl-item.active {
                transform: translateX(0%); /* Move the active item to the left by 50% */
            }

        }

        /*Remove offset for mobile screens*/
        @media (max-width: 767px) {
            .offset-2 {
                margin-top: 5px!important; /* or whatever default margin you want for mobile */
                margin-left: 0!important; /* remove left margin */
            }
            .offset-1 {
                margin-top: 5px!important; /* or whatever default margin you want for mobile */
                margin-left: 0!important; /* remove left margin */
            }
            .about-schedule p{ padding-top: 50px!important;}
            .margin-top-40{ margin-top: 5px!important;  }

           .padding-m-bottom{ padding-bottom: 50px}

           .carousel-2{ margin-bottom: 35px}

        }

        /*speaker css*/
        /* Styles for the round image */
        /* Styles for the round image */
        .round-image {
            width: 100px!important;
            height: 100px!important;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            margin-bottom: 10px; /* Adjust margin-bottom as needed */
        }

        /* Additional styles for the card and social links */
        .card {
            text-align: center;
        }

        .card-body {
            padding: 20px;
            color: white;
        }

        social-links {
            margin-top: 15px; /* Adjust margin-top as needed */
            color: white!important;
        }

        .social-links a {
            margin-right: 10px;
            color: white;
            font-size: 20px;

        }

        .social-center {
            display: flex;
            justify-content: center;
        }
    </style>
@endpush

@push('script')
    <script src="{{ url('owl-carousel2/dist/owl.carousel.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            var owl = $('.carousel-1');
            owl.owlCarousel({
                loop: true,
                margin: 10,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                        nav: true
                    },
                    600: {
                        items: 3,
                        nav: false
                    },
                    1000: {
                        items: 4,
                        nav: true,
                        loop: false
                    }
                }
            });
            // Customize the last item visibility
            var lastItem = $('.owl-item:last-child');
            lastItem.find('.claim-button').on('click', function (e) {
                e.preventDefault();
                lastItem.toggleClass('visible-50');
            });

            var carousel2 = $('.carousel-2');
            carousel2.owlCarousel({
                loop: true,
                margin: 10,
                responsiveClass: true,
                responsive: {
                    0: {
                        items: 1,
                        nav: true
                    },
                    600: {
                        items: 1,
                        nav: false
                    },
                    1000: {
                        items: 1,
                        nav: true,
                        loop: false
                    }
                }
            });

        });



    </script>
@endpush
