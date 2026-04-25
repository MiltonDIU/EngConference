<section id="CallforPapers">
    <div class="container wow fadeInUp">
        <div class="section-header text-center mb-5">
            <h2>Call for Papers</h2>
            <p>Download or view the official Call for Papers for academic requirements and submission guidelines.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Advanced PDF Viewer -->
                <div class="pdf-viewer-wrapper shadow-lg rounded overflow-hidden bg-light border">
                    <div id="pdf-loader" class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading PDF...</span>
                        </div>
                        <p class="mt-2 text-muted">Preparing document...</p>
                    </div>

                    <!-- Desktop/Modern Mobile Viewer -->
                    <div id="pdf-container" style="display: none; background: #525659; overflow-y: auto; max-height: 800px;">
                        <div id="pdf-render-area" class="text-center p-3"></div>
                    </div>

                    <!-- Mobile Fallback / Direct Link -->
                    <div id="pdf-fallback" class="p-4 text-center" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle mr-2"></i> Your browser doesn't support inline PDF viewing.
                        </div>
                        <a href="{{ asset('documents/CFPApril8.pdf') }}" target="_blank" class="btn btn-lg btn-primary rounded-pill px-4">
                           <i class="fa fa-file-pdf-o mr-2"></i> Open PDF in New Tab
                        </a>
                    </div>
                </div>

                <!-- Download Options -->
                <div class="mt-4 d-flex flex-wrap justify-content-center gap-3">
{{--                    <a href="{{ asset('documents/CFPApril8.pdf') }}" download class="btn btn-outline-primary m-2">--}}
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
    .pdf-viewer-wrapper { background: #fff; min-height: 400px; }
    .pdf-page-canvas {
        max-width: 100%;
        height: auto !important;
        margin-bottom: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
        background-color: #fff;
    }
    .gap-3 { gap: 1rem; }
</style>

@push('script')
    <!-- PDF.js Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const url = "{{ asset('documents/CFPApril8.pdf') }}";
            const pdfjsLib = window['pdfjs-dist/build/pdf'];
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

            const container = document.getElementById('pdf-render-area');
            const loader = document.getElementById('pdf-loader');
            const pdfContent = document.getElementById('pdf-container');
            const fallback = document.getElementById('pdf-fallback');

            // Check for basic support (older mobile browsers might fail)
            if (!pdfjsLib) {
                showFallback();
                return;
            }

            pdfjsLib.getDocument(url).promise.then(pdf => {
                loader.style.display = 'none';
                pdfContent.style.display = 'block';

                // Render all pages
                for (let i = 1; i <= pdf.numPages; i++) {
                    renderPage(pdf, i);
                }
            }).catch(err => {
                console.error("PDF.js error:", err);
                showFallback();
            });

            function renderPage(pdf, pageNumber) {
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

            function showFallback() {
                loader.style.display = 'none';
                fallback.style.display = 'block';
            }
        });
    </script>

@endpush
