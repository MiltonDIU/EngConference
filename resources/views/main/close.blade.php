@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Register Now</h3>
                        <p>Registration Form</p>
                    </div>
                </div>
            </div>
            <div class="container">
                @if(session()->has('message'))
                    <div class="alert alert-success alert-dismissible">
                        <strong>Success!</strong> {{ session()->get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
{{--                <div class="row">--}}
{{--                    <h1 class="text-center">Registration is closed! Thank You for your Interest</h1>--}}
{{--                </div>--}}
            </div>
        </section>
    </main>
@endsection

