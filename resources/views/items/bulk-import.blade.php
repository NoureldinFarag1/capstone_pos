@extends('layouts.dashboard')
@section('title', 'Import Items')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                        <h1 class="fw-bold text-primary">
                            <i class="fas fa-upload me-2"></i>Import Items from Excel
                    </h1>
                    <p class="text-muted mb-0">Import multiple items and variants from Excel or CSV files</p>
                </div>
                <a href="{{ route('items.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Items
                </a>
            </div>

            <!-- Steps Guide -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-list-ol me-2"></i>Import Process</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="step-item">
                                        <div class="step-number bg-primary text-white">1</div>
                                        <h6 class="mt-2">Download Template</h6>
                                        <p class="text-muted small">Get the Excel template with proper format</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="step-item">
                                        <div class="step-number bg-info text-white">2</div>
                                        <h6 class="mt-2">Fill Data</h6>
                                        <p class="text-muted small">Add your items with variants and quantities</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="step-item">
                                        <div class="step-number bg-warning text-white">3</div>
                                        <h6 class="mt-2">Upload File</h6>
                                        <p class="text-muted small">Upload your completed Excel or CSV file</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="step-item">
                                        <div class="step-number bg-success text-white">4</div>
                                        <h6 class="mt-2">Review Results</h6>
                                        <p class="text-muted small">Check imported items and any errors</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Download Section -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-download me-2 text-primary"></i>Download Template</h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Download our Excel template with proper format, examples, and instructions.</p>

                            <div class="mb-3">
                                <h6 class="text-muted">Template includes:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Formatted headers</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Detailed instructions</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Example data</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Validation rules</li>
                                </ul>
                            </div>

                            <a href="{{ route('items.downloadTemplate') }}" class="btn btn-primary btn-lg me-2">
                                <i class="fas fa-file-excel me-2"></i>Download Excel Template
                            </a>
                            <a href="{{ route('items.downloadDemoCSV') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-file-csv me-2"></i>Download Demo CSV
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Format Guide -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-info"></i>Format Guide</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb me-2"></i>Key Points:</h6>
                                <ul class="mb-0 small">
                                    <li>Each row represents one variant (size/color combination)</li>
                                    <li>Items with same name, brand, and category are grouped together</li>
                                    <li>Brands and categories are created automatically if they don't exist</li>
                                    <li>Use "N/A" for size or color if not applicable</li>
                                    <li>Color codes should be in hex format (#RRGGBB)</li>
                                </ul>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Required Fields</th>
                                            <th>Optional Fields</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <small>
                                                    • item_name<br>
                                                    • brand<br>
                                                    • category<br>
                                                    • quantity<br>
                                                    • buying_price<br>
                                                    • selling_price
                                                </small>
                                            </td>
                                            <td>
                                                <small>
                                                    • size<br>
                                                    • color<br>
                                                    • color_code<br>
                                                    • tax<br>
                                                    • discount_type<br>
                                                    • discount_value<br>
                                                    • description
                                                </small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-cloud-upload-alt me-2 text-success"></i>Upload Your File</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('items.bulkUpload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                                @csrf

                                <div class="row">
                                    <div class="col-lg-8">
                                        <div class="upload-area" id="uploadArea">
                                            <div class="upload-content text-center py-5">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                                <h5>Drag & Drop your file here</h5>
                                                <p class="text-muted">or click to browse</p>
                                                <input type="file" name="file" id="fileInput" accept=".xlsx,.csv" required class="d-none">
                                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('fileInput').click()">
                                                    <i class="fas fa-folder-open me-2"></i>Choose File
                                                </button>
                                            </div>

                                            <div class="file-info d-none" id="fileInfo">
                                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-excel fa-2x text-success me-3"></i>
                                                        <div>
                                                            <h6 class="mb-0" id="fileName"></h6>
                                                            <small class="text-muted" id="fileSize"></small>
                                                        </div>
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile()">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Supported formats: Excel (.xlsx) and CSV (.csv). Maximum file size: 10MB.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="import-options">
                                            <h6>Import Options</h6>

                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" id="skipErrors" name="skip_errors" checked>
                                                <label class="form-check-label" for="skipErrors">
                                                    Skip rows with errors
                                                </label>
                                            </div>

                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="updateExisting" name="update_existing" checked>
                                                <label class="form-check-label" for="updateExisting">
                                                    Update existing items
                                                </label>
                                            </div>

                                            <button type="submit" class="btn btn-success btn-lg w-100" id="uploadBtn" disabled>
                                                <i class="fas fa-upload me-2"></i>Import Items
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Modal -->
            <div class="modal fade" id="progressModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5>Importing Items...</h5>
                            <p class="text-muted mb-0">Please wait while we process your file.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: 0 auto;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.upload-area.dragover {
    border-color: #0d6efd;
    background-color: #e7f3ff;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const uploadContent = uploadArea.querySelector('.upload-content');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadForm = document.getElementById('uploadForm');

    // Drag and drop functionality
    uploadArea.addEventListener('click', () => fileInput.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFile(e.target.files[0]);
        }
    });

    function handleFile(file) {
        // Validate file type
        const allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'];
        if (!allowedTypes.includes(file.type) && !file.name.toLowerCase().endsWith('.csv')) {
            alert('Please select a valid Excel (.xlsx) or CSV (.csv) file.');
            return;
        }

        // Validate file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB.');
            return;
        }

        // Update UI
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);

        uploadContent.classList.add('d-none');
        fileInfo.classList.remove('d-none');
        uploadBtn.disabled = false;

        // Set the file to the input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;
    }

    function removeFile() {
        fileInput.value = '';
        uploadContent.classList.remove('d-none');
        fileInfo.classList.add('d-none');
        uploadBtn.disabled = true;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission
    uploadForm.addEventListener('submit', function(e) {
        // Show progress modal
        const progressModal = new bootstrap.Modal(document.getElementById('progressModal'));
        progressModal.show();
    });

    // Make removeFile function global
    window.removeFile = removeFile;
});
</script>
@endpush
@endsection
