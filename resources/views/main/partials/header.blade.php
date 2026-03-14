<header id="header"@if(Route::current()->getName() != 'home') class="header-fixed"@endif>
    <div class="container">

        <div id="logo" class="pull-left">
            <h1>
                <a href="{{ route('home') }}#intro">
                    <img width="200px" src="{{ asset('/') }}img/logo.svg">
                    {{--          <span><i class="fa fa-map-marker" aria-hidden="true"></i></span>--}}
                    {{--          {{ env('APP_NAME', 'The Event') }}--}}
                </a>
            </h1>
        </div>

        <nav id="nav-menu-container">
            <ul class="nav-menu">
                <li class="menu-active"><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#intro">Home</a></li>
                <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#about">About</a></li>
                <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#speakers">Speakers</a></li>
                <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#schedule">Schedule</a></li>
                <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#venue">Venue</a></li>
                {{--       <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#hotels">Hotels</a></li>--}}
                {{--        <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#gallery">Gallery</a></li>--}}
                <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#supporters">Partners</a></li>
                <li><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#contact">Contact</a></li>
{{--                <li><a href="{{  route('blogs') }}">Blogs</a></li>--}}
                {{--        <li class="buy-tickets"><a href="{{ Route::current()->getName() != 'home' ? route('home') : '' }}#registration">Registration</a></li>--}}
                @if(!Auth::check())
                    <li class="buy-tickets"><a href="{{ route('book-ticket') }}">Registration</a></li>
                    <li class="buy-tickets"><a href="{{ route("login") }}">Sign In</a></li>
                @else
                    <li class="buy-tickets"><a href="{{ route("admin.home") }}">Dashboard</a></li>
                    <li class="buy-tickets"><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                         document.getElementById('logout-form').submit();">Logout</a> </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                @endif

            </ul>
        </nav>
    </div>
</header>
