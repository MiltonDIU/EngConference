@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Payment has been failed</h3>
                    </div>
                </div>
            </div>
            <div class="container">
                @if ($message = Session::get('message'))
                    <div class="alert alert-success alert-dismissible">
                        <strong>Failed!</strong> {{ session()->get('message') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection
