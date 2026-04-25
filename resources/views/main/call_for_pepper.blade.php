@extends('layouts.main')
@section('content')
    <main id="main" class="main-page">
        <section class="wow fadeIn">
            <div class="title-section">
                <div class="container">
                    <div class="section-header">
                        <h3>CALL FOR PAPERS</h3>
                    </div>
                </div>
            </div>
        </section>

        <section class="content blogs margin-top-40">
            <div class="container">
                <div class="row">
                    <div class="col-md-12  col-sm-12">
                        <!-- PDF Viewer -->
                        <div class="pdf-card" style="border:1px solid #ddd; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.1); overflow:hidden; margin-bottom:20px;">

                        <iframe
                                src="{{ asset('documents/CFPApril8.pdf') }}"
                                width="100%"
                                height="800px"
                                style="border:1px solid #ccc;">
                            </iframe>
                        </div>

                        <!-- Download Link -->
                        <div class="pdf-download">
                            <a href="{{ asset('documents/Call_for_Papers_with_References_long_form.pdf') }}" style="margin-top: 20px; margin-bottom: 20px " download class="btn btn-primary">
                                Call for Papers with References (Long Form)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
