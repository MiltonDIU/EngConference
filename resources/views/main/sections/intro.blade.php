<section id="intro">
    <div class="intro-container container wow fadeIn" style="background: #4E7FAF; border-radius: 15px; opacity: 0.9;">

        {{--        <div class="main-title pb-0">{!! $settings['title'] ?? '' !!}</div>--}}

        <span class="main-title">International Conference on</span>
        <img src="{{ asset('/') }}img/eng-con_logo.png">
        <span class="second-title"> Beyond Nature and Culture </span>
        <span class="sub-title">Planetary Precarity in Literary-Cultural-Linguistic Representations</span>


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
        <img src="{{ asset('/') }}img/eng-dept_logo.png">
        <br><br>
        <p style="margin-top:20px">Department of English | Daffodil International University</p>
        <div class="organize">
            {{--            <img src="{{ asset('/') }}img/AWS-DIU-DIPTI.png">
                        <!--<img src="{{ asset('/') }}img/banner-logo.png">-->
                        <p>Department of English, Daffodil International University</p>

            {{--            @foreach($strategics as $strategic)--}}
            {{--                <img src="{{ $strategic->logo!=null?$strategic->logo->getUrl():'' }}" alt="{{ $strategic->name }}">--}}
            {{--            @endforeach--}}


            <!--<img  src="{{ asset('/') }}img/hero-partner_engcon2.png">-->



        </div>





    </div>

</section>
<style>
    #intro {
        min-height: 100vh!important;
        height: auto!important;
    }
    .intro-container {
        position: relative!important;
        padding: 40px 15px!important;
        margin-bottom:130px;
    }
    #intro .intro-container{

        top:120px!important;
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

    /*.countdown li span {*/
    /*    display: block;*/
    /*    font-size: 3rem;*/
    /*}*/

    .emoji {
        display: none;
        padding: 1rem;
    }

    .emoji span {
        font-size: 4rem;
        padding: 0 .5rem;
    }
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
    }
    .microsoft img{ width: 500px}
    @media all and (max-width: 768px) {
        .microsoft img{ width: 300px}
        h1 {
            font-size: calc(1.5rem * var(--smaller));
        }

        .countdown li {
            font-size: calc(1.125rem * var(--smaller));
        }

        /*li span {*/
        /*    font-size: calc(3.375rem * var(--smaller));*/
        /*}*/
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

    @media (max-width: 991px) {
        #intro .intro-container {
            top: 90px;
            margin-bottom: 140px;
        }

        .organize{ width:95%; margin: auto 0px;}
        .organize img{width:100%;}
        .intro-container{ width:95%; margin: 0 auto}
    }







</style>
<script>
    (function () {
        const second = 1000,
            minute = second * 60,
            hour = minute * 60,
            day = hour * 24;

        //I'm adding this section so I don't have to keep updating this pen every year :-)
        //remove this if you don't need it
        let today = new Date(),
            dd = String(today.getDate()).padStart(2, "0"),
            mm = String(today.getMonth() + 1).padStart(2, "0"),
            yyyy = today.getFullYear(),
            nextYear = yyyy + 1,
            dayMonth = "09/30/",
            birthday = dayMonth + yyyy;

        today = mm + "/" + dd + "/" + yyyy;
        console.log(today);
        if (today > birthday) {
            birthday = dayMonth + nextYear;
        }
        //end

        const countDown = new Date(birthday).getTime(),
            x = setInterval(function() {

                const now = new Date().getTime(),
                    distance = countDown - now;

                document.getElementById("days").innerText = Math.floor(distance / (day)),
                    document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour)),
                    document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
                    document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);

                //do something later when date is reached
                if (distance < 0) {
                    document.getElementById("headline").innerText = "It's my birthday!";
                    document.getElementById("countdown").style.display = "none";
                    document.getElementById("content").style.display = "block";
                    clearInterval(x);
                }
                //seconds
            }, 0)
    }());
</script>
