<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>{{ env('APP_NAME', 'Conference | Daffodil International University') }}</title>

  <meta property="og:url"               content="https://engconference.diu.edu.bd/" />
  <meta property="og:type"              content="website" />
  <meta property="og:title"             content="Conference, Daffodil International University" />
  <!--<meta property="og:description"       content="Conference, Daffodil International University" />-->
  <!--<meta property="og:image"             content="https://aiconnect.dipti.com.bd/img/aiconnect-banner1.jpg" />-->
  <!--<meta property="og:image:secure_url"  content="https://aiconnect.dipti.com.bd/img/aiconnect-banner1.jpg" />-->

  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!--<link rel="icon" type="image/png" href="{{ asset('/') }}img/AIConnectFavicon.png"/>-->
  <link rel="icon" type="image/png" href="https://daffodilvarsity.edu.bd/favicon.ico"/>
  

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,700,700i|Raleway:300,400,500,700,800" rel="stylesheet">

  <!-- Bootstrap CSS File -->
  <link href="{{ asset('lib/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

  <!-- Libraries CSS Files -->
  <link href="{{ asset('lib/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/venobox/venobox.css') }}" rel="stylesheet">
  <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">

  <!-- Main Stylesheet File -->
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    @stack('style')
    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '989405928987211');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=989405928987211&ev=PageView&noscript=1"
        />
    </noscript>
    <!-- End Meta Pixel Code -->
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-GEPJR65WYL"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-GEPJR65WYL');
</script>
</head>

<body>
  @include('main.partials.header')

  @yield('content')

  @include('main.partials.footer')

  <a href="#" class="back-to-top"><i class="fa fa-angle-up"></i></a>

  <!-- JavaScript Libraries -->
  <script src="{{ asset('lib/jquery/jquery.min.js') }}"></script>
  <script src="{{ asset('lib/jquery/jquery-migrate.min.js') }}"></script>
  <script src="{{ asset('lib/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('lib/easing/easing.min.js') }}"></script>
  <script src="{{ asset('lib/superfish/hoverIntent.js') }}"></script>
  <script src="{{ asset('lib/superfish/superfish.min.js') }}"></script>
  <script src="{{ asset('lib/wow/wow.min.js') }}"></script>
  <script src="{{ asset('lib/venobox/venobox.min.js') }}"></script>
  <script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>

  <!-- Contact Form JavaScript File -->
  <script src="{{ asset('js/contactform.js') }}"></script>

  <!-- Template Main Javascript File -->
  <script src="{{ asset('js/app.js') }}"></script>
  @yield('scripts')
@stack('script')
</body>

</html>
