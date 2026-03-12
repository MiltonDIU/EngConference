<footer id="footer">
  <div class="footer-top">
    <div class="container">
      <div class="row">

        <div class="col-lg-3 col-md-6 footer-info">
          <h4>{{ $settings['title'] ?? '' }}</h4>
          <p>{{ $settings['footer_description'] ?? '' }}</p>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><i class="fa fa-angle-right"></i> <a href="#">Home</a></li>
            <li><i class="fa fa-angle-right"></i> <a href="#about">About us</a></li>
            <!--<li><i class="fa fa-angle-right"></i> <a href="#">Services</a></li>-->
            <!--<li><i class="fa fa-angle-right"></i> <a href="#">Terms of service</a></li>-->
            <li><i class="fa fa-angle-right"></i> <a href="{{ route('privacy-policy') }}">Privacy policy</a></li>
            @guest
              <li><i class="fa fa-angle-right"></i> <a href="{{ route('login') }}">Login</a></li>
            @endguest
            @auth
              <li><i class="fa fa-angle-right"></i> <a href="{{ route('admin.home') }}">Admin Panel</a></li>
            @endauth
          </ul>
        </div>

        <div class="col-lg-3 col-md-6 footer-links">
          <h4>QR Code</h4>
          <img width="65%" src="{{ asset('/') }}img/qr-code.png">
          <!--<ul>-->
          <!--  <li><i class="fa fa-angle-right"></i> <a href="#">Home</a></li>-->
          <!--  <li><i class="fa fa-angle-right"></i> <a href="#">About us</a></li>-->
            <!--<li><i class="fa fa-angle-right"></i> <a href="#">Services</a></li>-->
            <!--<li><i class="fa fa-angle-right"></i> <a href="#">Terms of service</a></li>-->
          <!--  <li><i class="fa fa-angle-right"></i> <a href="#">Privacy policy</a></li>-->
          <!--  @guest-->
          <!--    <li><i class="fa fa-angle-right"></i> <a href="{{ route('login') }}">Login</a></li>-->
          <!--  @endguest-->
          <!--  @auth-->
          <!--    <li><i class="fa fa-angle-right"></i> <a href="{{ route('admin.home') }}">Admin Panel</a></li>-->
          <!--  @endauth-->
          <!--</ul>-->
        </div>

        <div class="col-lg-3 col-md-6 footer-contact">
          <h4>Contact Us</h4>
          <p>
            {!! $settings['footer_address'] ?? '' !!}<br>
            <strong>Phone:</strong> @if(isset($settings['contact_phone'])){{ $settings['contact_phone'] }}@endif<br>
            <strong>Email:</strong> @if(isset($settings['contact_email'])){{ $settings['contact_email'] }}@endif<br>
          </p>

          <div class="social-links">
            <a href="{{ $settings['footer_twitter'] ?? '' }}" class="twitter"><i class="fa fa-twitter"></i></a>
            <a href="{{ $settings['footer_facebook'] ?? '' }}" class="facebook"><i class="fa fa-facebook"></i></a>
            <a href="{{ $settings['footer_instagram'] ?? '' }}" class="instagram"><i class="fa fa-instagram"></i></a>
            <a href="{{ $settings['footer_googleplus'] ?? '' }}" class="google-plus"><i class="fa fa-google-plus"></i></a>
            <a href="{{ $settings['footer_linkedin'] ?? '' }}" class="linkedin"><i class="fa fa-linkedin"></i></a>
          </div>

        </div>

      </div>
    </div>
  </div>

  <div class="container">
    <div class="copyright">
      &copy; Copyright <strong>{{ env('APP_NAME', 'Daffodil Family') }}</strong>. All Rights Reserved
    </div>
    <div class="credits">
      Developed by <a href="https://daffodilweb.com/" target="_blank">Daffodil Web & e-Commerce Ltd.</a>
    </div>
  </div>
</footer><!-- #footer -->
