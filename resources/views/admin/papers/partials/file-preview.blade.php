{{-- In-browser preview of .docx / .pptx paper files (admin only). Any element with
     .paper-file-preview-btn and data-kind / data-url / data-download-url / data-name opens it. --}}
<div class="modal fade" id="filePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header py-2">
                <h6 class="modal-title font-weight-bold text-dark text-truncate" id="filePreviewTitle"></h6>
                <div class="d-flex align-items-center">
                    <a href="#" id="filePreviewDownload" class="btn btn-sm btn-outline-primary mr-2">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body p-0" style="max-height: 80vh; overflow: auto; background-color: #f9fafb;">
                <div id="filePreviewLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
                </div>
                <div id="filePreviewError" class="alert alert-warning m-3" style="display: none;"></div>
                <div id="filePreviewContainer"></div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
$(function () {
    var $modal = $('#filePreviewModal');
    var container = document.getElementById('filePreviewContainer');
    var currentRequest = 0;
    var libPromises = {};

    function loadScript(src) {
        return new Promise(function (resolve, reject) {
            var s = document.createElement('script');
            s.src = src;
            s.onload = resolve;
            s.onerror = function () { reject(new Error('Preview library failed to load.')); };
            document.head.appendChild(s);
        });
    }

    // Libraries are loaded on first use only. docx-preview needs JSZip 3.x and captures it
    // when it loads, while the layout's JSZip 2.5 is used by the DataTables Excel export,
    // so the original global is restored afterwards.
    var loaders = {
        docx: function () {
            var previousJSZip = window.JSZip;
            return loadScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js')
                .then(function () { return loadScript('https://cdn.jsdelivr.net/npm/docx-preview@0.4.1/dist/docx-preview.min.js'); })
                .finally(function () { window.JSZip = previousJSZip; })
                .then(function () { if (!window.docx) { throw new Error('Preview library failed to load.'); } });
        },
        pptx: function () {
            return loadScript('https://cdn.jsdelivr.net/npm/pptx-preview@1.0.7/dist/pptx-preview.umd.js')
                .then(function () { if (!window.pptxPreview) { throw new Error('Preview library failed to load.'); } });
        }
    };

    function loadLib(kind) {
        if (!libPromises[kind]) {
            libPromises[kind] = loaders[kind]().catch(function (err) {
                delete libPromises[kind];
                throw err;
            });
        }
        return libPromises[kind];
    }

    var renderers = {
        docx: function (blob) {
            return window.docx.renderAsync(blob, container, null, {
                className: 'docx',
                inWrapper: true,
                breakPages: true,
                ignoreLastRenderedPageBreak: true
            });
        },
        pptx: function (blob) {
            return blob.arrayBuffer().then(function (buffer) {
                var width = Math.min(960, window.innerWidth - 60);
                container.style.padding = '15px 0';
                return window.pptxPreview.init(container, { width: width, height: Math.round(width * 9 / 16) }).preview(buffer);
            });
        }
    };

    // Delegated so buttons rendered later by DataTables work too
    $(document).on('click', '.paper-file-preview-btn', function (e) {
        e.preventDefault();
        var btn = this;
        var kind = btn.dataset.kind;
        if (!renderers[kind]) { return; }
        var requestId = ++currentRequest;

        $('#filePreviewTitle').text(btn.dataset.name);
        $('#filePreviewDownload').attr('href', btn.dataset.downloadUrl);
        $('#filePreviewError').hide().text('');
        $('#filePreviewLoading').show();
        container.innerHTML = '';
        container.style.padding = '';
        $modal.modal('show');

        Promise.all([
            fetch(btn.dataset.url, { credentials: 'same-origin' }).then(function (res) {
                if (!res.ok) { throw new Error('Server returned ' + res.status); }
                return res.blob();
            }),
            loadLib(kind)
        ])
            .then(function (results) {
                if (requestId !== currentRequest) { return; }
                return renderers[kind](results[0]);
            })
            .catch(function (err) {
                if (requestId !== currentRequest) { return; }
                container.innerHTML = '';
                $('#filePreviewError').show().text('Could not preview this file (' + err.message + '). Please use Download instead.');
            })
            .finally(function () {
                if (requestId === currentRequest) { $('#filePreviewLoading').hide(); }
            });
    });

    $modal.on('hidden.bs.modal', function () {
        currentRequest++;
        container.innerHTML = '';
    });
});
</script>
@endpush
