@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>Blogs</h3>
                        <p>{{ $msg??"" }}</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="content blogs margin-top-40">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12  col-sm-12">
                        <h3 class="message">{{ $message??"" }}
                            @if($link == true)
                                If this is a choice by mistake,   <a href='{{ route('data-banks.subscribe',[$dataBank->unsubscribe_link]) }}'>please click here to subscribe again.</a>
                            @endif

                        </h3>
                    </div>

                </div>
            </div>
            </div>
        </section>
    </main>
@endsection

@push('style')
    <style>
        /* Media query for desktop screens */
        @media only screen and (min-width: 768px) {
            .message {
                width: 50%;
                margin: 0 auto;
                padding: 50px 0px;
            }
        }
    </style>
@endpush
