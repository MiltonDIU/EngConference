@extends('layouts.main')

@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="container">
                <div class="section-header">
                    <h2>Payment</h2>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        @if(session()->has('message'))
                            <div class="alert alert-success alert-dismissible">
                                <strong>Success!</strong> {{ session()->get('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @else
                            <h3>No data here</h3>
                        @endif
                    </div>

                </div>
            </div>
        </section>
    </main>
@endsection
