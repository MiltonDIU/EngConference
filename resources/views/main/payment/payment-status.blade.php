@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn p-5">
            <div class="container">
                <div class="section-header">
                    <h2>Payment Status</h2>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="text-center"> Please check your Payment status after login.</h3>
                        <div class="text-center"><a href="{{ route("login") }}" class="btn btn-primary">Login </a></div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
