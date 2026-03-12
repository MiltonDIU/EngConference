<header id="header"@if(Route::current()->getName() != 'home') class="header-fixed"@endif>
  <div class="container">

    <div id="logo" class="pull-left">
      <h1>
        <a href="{{ route('home') }}#intro">
          <span><i class="fa fa-map-marker" aria-hidden="true"></i></span>
          {{ env('APP_NAME', 'The Event') }}
        </a>
      </h1>
    </div>

    <nav id="nav-menu-container">
      <ul class="nav-menu">
        <li class="menu-active"><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#intro">Home</a></li>

        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#about">About</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#speakers">Speakers</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#schedule">Schedule</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#venue">Venue</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#hotels">Hotels</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#gallery">Gallery</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#supporters">Sponsors</a></li>
        <li><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#contact">Contact</a></li>
        <li class="buy-tickets"><a href="{{ Route::current()->getName() != 'home' ? url()->current() : '' }}#buy-tickets">Tickets</a></li>
          @auth
              <li class="buy-tickets"><a href="{{ route("login") }}">Admin</a></li>
          @else
        <li class="buy-tickets"><a href="{{ route("admin.home") }}">Admin</a></li>
          @endauth

      </ul>
    </nav>
  </div>
</header>
