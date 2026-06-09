<section id="CallforPapers">
    <div class="container wow fadeInUp">
        <div class="section-header text-center mb-5">
            <h2>Call for Papers</h2>
            <p>Download or view the official Call for Papers for academic requirements and submission guidelines.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Premium Tab Navigation -->
                <div class="text-center mb-4">
                    <ul class="nav nav-tabs pdf-tabs d-inline-flex" id="pdfTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="english-tab" data-toggle="tab" href="#english-pdf" role="tab" aria-controls="english-pdf" aria-selected="true">
                                <i class="fa fa-globe mr-1"></i> English Version
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="bangla-tab" data-toggle="tab" href="#bangla-pdf" role="tab" aria-controls="bangla-pdf" aria-selected="false">
                                <i class="fa fa-language mr-1"></i> Bangla Version (বাংলা)
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="pdfTabContent">
                    <!-- English PDF Tab -->
                    <div class="tab-pane fade show active" id="english-pdf" role="tabpanel" aria-labelledby="english-tab">
                        <!-- Advanced PDF Viewer -->
                        <div class="pdf-viewer-wrapper shadow-lg rounded overflow-hidden bg-light border">
                            <div id="pdf-loader-en" class="text-center p-5 pdf-loader">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading PDF...</span>
                                </div>
                                <p class="mt-2 text-muted">Preparing English document...</p>
                            </div>

                            <!-- Desktop/Modern Mobile Viewer -->
                            <div id="pdf-container-en" class="pdf-container" style="display: none; background: #525659; overflow-y: auto; max-height: 800px;">
                                <div id="pdf-render-area-en" class="text-center p-3"></div>
                            </div>

                            <!-- Mobile Fallback / Direct Link -->
                            <div id="pdf-fallback-en" class="p-4 text-center pdf-fallback" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle mr-2"></i> Your browser doesn't support inline PDF viewing.
                                </div>
                                <a href="{{ asset('documents/CFPApril23.pdf') }}" target="_blank" class="btn btn-lg btn-primary rounded-pill px-4">
                                   <i class="fa fa-file-pdf-o mr-2"></i> Open English PDF in New Tab
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Bangla PDF Tab -->
                    <div class="tab-pane fade" id="bangla-pdf" role="tabpanel" aria-labelledby="bangla-tab">
                        <!-- Advanced PDF Viewer -->
                        <div class="pdf-viewer-wrapper shadow-lg rounded overflow-hidden bg-light border">
                            <div id="pdf-loader-bn" class="text-center p-5 pdf-loader">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading PDF...</span>
                                </div>
                                <p class="mt-2 text-muted">Preparing Bangla document...</p>
                            </div>

                            <!-- Desktop/Modern Mobile Viewer -->
                            <div id="pdf-container-bn" class="pdf-container" style="display: none; background: #525659; overflow-y: auto; max-height: 800px;">
                                <div id="pdf-render-area-bn" class="text-center p-3"></div>
                            </div>

                            <!-- Mobile Fallback / Direct Link -->
                            <div id="pdf-fallback-bn" class="p-4 text-center pdf-fallback" style="display: none;">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle mr-2"></i> Your browser doesn't support inline PDF viewing.
                                </div>
                                <a href="{{ asset('documents/BanglaCFPApril 23-2.pdf') }}" target="_blank" class="btn btn-lg btn-primary rounded-pill px-4">
                                   <i class="fa fa-file-pdf-o mr-2"></i> Open Bangla PDF in New Tab
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Download Options -->
                <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
{{--                    <a href="{{ asset('documents/CFPApril23.pdf') }}" download class="btn btn-outline-primary m-2">--}}
{{--                        <i class="fa fa-download mr-1"></i> Download PDF--}}
{{--                    </a>--}}
                    <a href="{{ asset('documents/Call_for_Papers_with_References_long_form.pdf') }}" download class="btn btn-outline-secondary m-2">
                        <i class="fa fa-file-word-o mr-1"></i> Call for Papers with References (Long Form)
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>


<style>
    #CallforPapers { padding: 80px 0; background: #f9f9f9; }
    .pdf-viewer-wrapper { background: #fff; min-height: 400px; border-radius: 12px; }
    .pdf-page-canvas {
        max-width: 100%;
        height: auto !important;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        background-color: #fff;
        border-radius: 4px;
        transition: transform 0.3s ease;
    }
    .pdf-page-canvas:hover {
        transform: scale(1.01);
    }
    .gap-3 { gap: 1rem; }

    /* Premium dynamic tabs */
    .pdf-tabs {
        border-bottom: none;
        background: #e9ecef;
        padding: 5px;
        border-radius: 30px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    }
    .pdf-tabs .nav-item {
        margin: 0;
    }
    .pdf-tabs .nav-link {
        border: none;
        border-radius: 25px;
        padding: 8px 25px;
        font-weight: 600;
        color: #6c757d;
        background: transparent;
        transition: all 0.3s ease-in-out;
    }
    .pdf-tabs .nav-link.active {
        background: linear-gradient(135deg, #17a2b8, #007bff);
        color: #fff !important;
        box-shadow: 0 4px 10px rgba(0,123,255,0.25);
    }
    .pdf-tabs .nav-link:hover:not(.active) {
        color: #007bff;
        background: rgba(0,123,255,0.05);
    }
</style>

@push('script')
    <!-- PDF.js Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pdfjsLib = window['pdfjs-dist/build/pdf'];
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

            // Check support
            if (!pdfjsLib) {
                showFallback('en');
                showFallback('bn');
                return;
            }

            // Load English PDF immediately
            loadPDF("{{ asset('documents/CFPApril23.pdf') }}", 'en');

            // Lazy-load Bangla PDF when Bangla tab is shown
            let banglaLoaded = false;
            $('#bangla-tab').on('shown.bs.tab', function () {
                if (!banglaLoaded) {
                    loadPDF("{{ asset('documents/BanglaCFPApril 23-2.pdf') }}", 'bn');
                    banglaLoaded = true;
                }
            });

            function loadPDF(url, lang) {
                const container = document.getElementById(`pdf-render-area-${lang}`);
                const loader = document.getElementById(`pdf-loader-${lang}`);
                const pdfContent = document.getElementById(`pdf-container-${lang}`);

                pdfjsLib.getDocument(url).promise.then(pdf => {
                    loader.style.display = 'none';
                    pdfContent.style.display = 'block';

                    // Render all pages
                    for (let i = 1; i <= pdf.numPages; i++) {
                        renderPage(pdf, i, container);
                    }
                }).catch(err => {
                    console.error(`PDF.js error for ${lang}:`, err);
                    showFallback(lang);
                });
            }

            function renderPage(pdf, pageNumber, container) {
                pdf.getPage(pageNumber).then(page => {
                    const scale = 1.5;
                    const viewport = page.getViewport({ scale: scale });

                    const canvas = document.createElement('canvas');
                    canvas.className = 'pdf-page-canvas';
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };

                    container.appendChild(canvas);
                    page.render(renderContext);
                });
            }

            function showFallback(lang) {
                const loader = document.getElementById(`pdf-loader-${lang}`);
                const fallback = document.getElementById(`pdf-fallback-${lang}`);
                if (loader) loader.style.display = 'none';
                if (fallback) fallback.style.display = 'block';
            }
        });
    </script>

@endpush
