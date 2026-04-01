<section id="intro">
    <div class="intro-container container wow fadeIn" style="background: #4E7FAF; border-radius: 15px; opacity: 0.9; margin-bottom: 30px">

{{--        <div class="main-title pb-0">{!! $settings['title'] ?? '' !!}</div>--}}

        <span class="main-title">International Conference on</span>
        <img src="{{ asset('/') }}img/eng-con_logo.png" class="intro-img">
        <span class="second-title"> Beyond Nature and Culture </span>
        <span class="sub-title">Planetarity Precarity in Literary-Cultural-Linguistic Representations</span>


{{--        <p style="margin:10px" class="mb-4 pb-0">{!! $settings['subtitle'] ?? '' !!}</p>--}}

        <p class="mb-4 pb-0">{!! $settings['about_when'] ?? '' !!}</p>
        @if(isset($settings['youtube_link']))
            <!--<a href="{{ $settings['youtube_link'] }}" class="venobox play-btn mb-4" data-vbtype="video"-->
            <!--   data-autoplay="true"></a>-->
        @endif
        @php
            $eventStartDate = \Carbon\Carbon::parse($settings['registration_start_date']);
            $eventCloseDate = \Carbon\Carbon::parse($settings['registration_close_date']);
            $eventEarlyRegDate = \Carbon\Carbon::parse($settings['early_registration_last_date']);
            $currentDate = \Carbon\Carbon::now();
        @endphp

        @if(!Auth::check())
            @if($currentDate >= $eventStartDate && $currentDate <= $eventCloseDate)
                <a href="{{ route('book-ticket') }}" class="about-btn scrollto">Register Now</a>
            @else
                {{--                <a href="#" class="about-btn scrollto">Registration Closed</a>--}}
            @endif

        @endif
        {{--                <div class="clock pt-3">--}}
        {{--                    <div id="countdown" class="countdown">--}}
        {{--                        <h4 style="color:white">Registration Start Date</h4>--}}
        {{--                       <ul style="padding-left:0!important;">--}}
        {{--                          <li><span class="frame" id="days"></span><span class="font">days</span></li>--}}
        {{--                          <li><span class="frame" id="hours"></span><span class="font">Hours</span></li>--}}
        {{--                         <li><span class="frame" id="minutes"></span><span class="font">Minutes</span></li>--}}
        {{--                          <li><span class="frame" id="seconds"></span><span class="font">Seconds</span></li>--}}
        {{--                        </ul>--}}
        {{--                    </div>--}}
        {{--                </div>--}}
        <p class="organize" style="margin-top: 20px"> Organized by</p>
        <img src="{{ asset('/') }}img/eng-dept_logo.png" class="intro-img">
        <br>
        <p>Department of English | Daffodil International University</p>
        <div class="organize">
            {{--            <img src="{{ asset('/') }}img/AWS-DIU-DIPTI.png">
                        <!--<img src="{{ asset('/') }}img/banner-logo.png">-->
                        <p>Department of English, Daffodil International University</p>

            {{--            @foreach($strategics as $strategic)--}}
            {{--                <img src="{{ $strategic->logo!=null?$strategic->logo->getUrl():'' }}" alt="{{ $strategic->name }}">--}}
            {{--            @endforeach--}}

        </div>


        <img src="{{ asset('/') }}img/hero-partner_engcon2.png" class="intro-img">



    </div>

</section>
<style>
    #intro .intro-container {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        top: 90px;
        width: 100%;
        max-width: 1320px; /* Constrains the blue card on wide screens */
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        text-align: center;
        padding: 0 15px;
    }

    .intro-img {
        max-width: 100%;
        height: auto;
    }

    .main-title {
        display: block;
        font-size: 28px;
        color: #000000;
        font-weight: 700;
        line-height: normal;
        font-family: 'edo', sans-serif;
    }
    .second-title {
        display: block;
        font-family: 'edo', sans-serif;
        font-size: 40px;
        color: #ffffff;
        line-height: normal;
        margin: 10px 0;


    }
    .sub-title {
        display: block;
        font-family: 'GlacialIndifference-Regular', sans-serif;
        font-size: 21px;
        color: #ffffff;
        line-height: normal;
        font-weight: bold;
        padding-bottom: 35px;
    }

    @font-face {
        font-family: 'edo';
        src: url('{{"fonts/edo.ttf"}}') format('truetype');
    }
    @font-face {
        font-family: 'GlacialIndifference-Regular';
        src: url('{{"fonts/GlacialIndifference-Regular.otf"}}') format('truetype');
    }
    .countdown li {
        display: inline-block;
        font-size: 1.5em;
        list-style-type: none;
        padding: .75em;
        color: #fff;
        text-transform: uppercase;
    }

    /* ... Keep other original li span styles ... */

    .frame {
        border: 1px solid;
        padding: 0px 5px;
        width:80px;
        display: block;
        font-size: 3rem;
    }
    .font{
        font-size:15px;
    }
    .organize img {
        width: 600px;
        max-width: 100%;
    }
    .microsoft img{ width: 500px; max-width: 100%; }

    @media all and (max-width: 768px) {
        .microsoft img{ width: 300px}
        .frame {
            width:50px;
            font-size: calc(3.375rem * var(--smaller));
        }
        .font{
            font-size:10px;
        }

        .organize img {
            width: 250px;
        }
        .main-title {
            font-size: 20px;
            color: #000000;
        }
        .second-title {
            font-size: 28px;
        }
        .sub-title {
            font-size: 16px;
        }
    }
    @media all and (max-width: 992px) {
        .frame {
            width:60px;
        }
        .font{
            font-size:12px;
        }
        .organize img {
            width: 350px;
        }
        .main-title {
            font-size: 22px;
            color: #000000;
        }
        .second-title {
            font-size: 30px;
        }
        .sub-title {
            font-size: 17px;
        }
    }
    @media all and (max-width: 1366px) {
        .main-title {
            font-size: 24px;
        }
        .second-title {
            font-size: 32px;
        }
        .frame {
            width:50px;
            display: block;
            font-size: 1.5rem;
        }
        .font{
            font-size:10px;
        }
        .organize img {
            width:350px;
        }
        .frame {
            padding: 10px 5px;
            width:50px;
            display: block;
            font-size: 1rem;
        }
    }
</style>
<script>
    (function () {
        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        let today = new Date(),
            dd = String(today.getDate()).padStart(2, "0"),
            mm = String(today.getMonth() + 1).padStart(2, "0"),
            yyyy = today.getFullYear(),
            nextYear = yyyy + 1,
            dayMonth = "09/30/",
            birthday = dayMonth + yyyy;

        today = mm + "/" + dd + "/" + yyyy;
        if (today > birthday) {
            birthday = dayMonth + nextYear;
        }

        const countDown = new Date(birthday).getTime(),
            x = setInterval(function() {

                const now = new Date().getTime(),
                    distance = countDown - now;

                if (document.getElementById("days")) {
                    document.getElementById("days").innerText = Math.floor(distance / (day));
                    document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour));
                    document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute));
                    document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);
                }

                if (distance < 0) {
                    clearInterval(x);
                }
            }, 1000)
    }());
</script>
