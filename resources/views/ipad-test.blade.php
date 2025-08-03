<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>iPad Compatibility Test - LocalHub POS</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-touch-fullscreen" content="yes">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- iPad Optimizations -->
    <link rel="stylesheet" href="{{ asset('css/ipad.css') }}">
    
    <style>
        .test-section {
            margin-bottom: 3rem;
            padding: 2rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
        }
        .device-info {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }
        .touch-target-demo {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .status-indicator {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: bold;
            text-align: center;
            margin: 0.5rem 0;
        }
        .status-good { background: #d4edda; color: #155724; }
        .status-warning { background: #fff3cd; color: #856404; }
        .status-info { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="text-center mb-4">
            <h1 class="display-4">🍎 iPad Compatibility Test</h1>
            <p class="lead">LocalHub POS - Touch Optimization Demo</p>
        </div>

        <!-- Device Detection -->
        <div class="device-info">
            <h3><i class="fas fa-tablet-alt"></i> Device Information</h3>
            <div id="deviceInfo">
                <div class="status-indicator status-info">
                    <span id="deviceType">Detecting device...</span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>User Agent:</strong><br>
                        <small><span id="userAgent"></span></small>
                    </div>
                    <div class="col-md-6">
                        <strong>Screen Size:</strong> <span id="screenSize"></span><br>
                        <strong>Touch Points:</strong> <span id="touchPoints"></span><br>
                        <strong>Orientation:</strong> <span id="orientation"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Touch Target Test -->
        <div class="test-section">
            <h3><i class="fas fa-hand-pointer"></i> Touch Target Test</h3>
            <p>All buttons should be at least 44px × 44px for optimal touch interaction:</p>
            
            <div class="touch-target-demo">
                <button class="btn btn-primary">Primary</button>
                <button class="btn btn-success">Success</button>
                <button class="btn btn-warning">Warning</button>
                <button class="btn btn-danger">Danger</button>
                <button class="btn btn-info">Info</button>
                <button class="btn btn-outline-secondary">Outline</button>
            </div>
            
            <h5>Small vs Large Buttons:</h5>
            <div class="mb-3">
                <button class="btn btn-sm btn-primary me-2">Small Button</button>
                <button class="btn btn-primary me-2">Normal Button</button>
                <button class="btn btn-lg btn-primary">Large Button</button>
            </div>
        </div>

        <!-- Form Input Test -->
        <div class="test-section">
            <h3><i class="fas fa-keyboard"></i> Form Input Test</h3>
            <p>Form inputs should be 16px+ font size to prevent zoom on focus:</p>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="testText" class="form-label">Text Input</label>
                        <input type="text" class="form-control" id="testText" placeholder="Type something...">
                    </div>
                    <div class="mb-3">
                        <label for="testEmail" class="form-label">Email Input</label>
                        <input type="email" class="form-control" id="testEmail" placeholder="email@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="testNumber" class="form-label">Number Input</label>
                        <input type="number" class="form-control" id="testNumber" placeholder="123">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="testSelect" class="form-label">Select Dropdown</label>
                        <select class="form-select" id="testSelect">
                            <option>Option 1</option>
                            <option>Option 2</option>
                            <option>Option 3</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="testTextarea" class="form-label">Textarea</label>
                        <textarea class="form-control" id="testTextarea" rows="3" placeholder="Enter text here..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- POS Interface Demo -->
        <div class="test-section pos-interface">
            <h3><i class="fas fa-cash-register"></i> POS Interface Demo</h3>
            <p>Simulated POS interface with iPad optimizations:</p>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5>Barcode Scanner</h5>
                        </div>
                        <div class="card-body">
                            <div class="input-group barcode-scanner-group mb-3">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Scan or type barcode">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            </div>
                            
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-outline-primary product-btn">
                                    <i class="fas fa-star"></i> Popular Items
                                </button>
                                <button class="btn btn-outline-success product-btn">
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                                <button class="btn btn-outline-danger product-btn">
                                    <i class="fas fa-trash"></i> Clear Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Payment</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <select class="form-select">
                                    <option>Cash</option>
                                    <option>Credit Card</option>
                                    <option>Mobile Payment</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary payment-method-btn">
                                    <i class="fas fa-check-circle"></i> Complete Sale
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gesture Test -->
        <div class="test-section">
            <h3><i class="fas fa-hand-rock"></i> Touch Gesture Test</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Tap Test</h5>
                            <button id="tapTest" class="btn btn-success w-100 mb-2">Tap Me!</button>
                            <p>Taps: <span id="tapCount">0</span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Scroll Test</h5>
                            <div style="height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 1rem;">
                                <p>This is a scrollable area. Try scrolling with touch gestures.</p>
                                <p>Line 2</p>
                                <p>Line 3</p>
                                <p>Line 4</p>
                                <p>Line 5</p>
                                <p>Line 6</p>
                                <p>Line 7</p>
                                <p>Line 8</p>
                                <p>Line 9</p>
                                <p>Line 10</p>
                                <p>End of content</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Results -->
        <div class="test-section">
            <h3><i class="fas fa-clipboard-check"></i> Compatibility Status</h3>
            <div id="testResults">
                <div class="status-indicator status-info">
                    Running tests...
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('sales.create') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-cash-register"></i> Go to POS Interface
            </a>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/ipad-optimizations.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Device detection
            const isTablet = /iPad|Tablet|Android/i.test(navigator.userAgent) || 
                            (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            const isIPad = /iPad/.test(navigator.userAgent) || 
                          (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
            
            // Update device info
            document.getElementById('userAgent').textContent = navigator.userAgent;
            document.getElementById('screenSize').textContent = `${screen.width} × ${screen.height}`;
            document.getElementById('touchPoints').textContent = navigator.maxTouchPoints || 'Not supported';
            document.getElementById('orientation').textContent = screen.orientation ? screen.orientation.type : 'Unknown';
            
            const deviceType = document.getElementById('deviceType');
            if (isIPad) {
                deviceType.textContent = '✅ iPad Detected';
                deviceType.className = 'status-indicator status-good';
            } else if (isTablet) {
                deviceType.textContent = '📱 Tablet Detected';
                deviceType.className = 'status-indicator status-warning';
            } else {
                deviceType.textContent = '💻 Desktop/Mobile Device';
                deviceType.className = 'status-indicator status-info';
            }
            
            // Tap test
            let tapCount = 0;
            document.getElementById('tapTest').addEventListener('click', function() {
                tapCount++;
                document.getElementById('tapCount').textContent = tapCount;
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 100);
            });
            
            // Run compatibility tests
            setTimeout(function() {
                const results = [];
                
                // Test touch targets
                const buttons = document.querySelectorAll('.btn');
                let smallButtons = 0;
                buttons.forEach(btn => {
                    const rect = btn.getBoundingClientRect();
                    if (rect.height < 44 || rect.width < 44) {
                        smallButtons++;
                    }
                });
                
                if (smallButtons === 0) {
                    results.push('<div class="status-indicator status-good">✅ All touch targets are 44px+ (Apple guideline)</div>');
                } else {
                    results.push(`<div class="status-indicator status-warning">⚠️ ${smallButtons} touch targets below 44px</div>`);
                }
                
                // Test input font sizes
                const inputs = document.querySelectorAll('input, select, textarea');
                let smallInputs = 0;
                inputs.forEach(input => {
                    const fontSize = parseFloat(window.getComputedStyle(input).fontSize);
                    if (fontSize < 16) {
                        smallInputs++;
                    }
                });
                
                if (smallInputs === 0) {
                    results.push('<div class="status-indicator status-good">✅ All inputs are 16px+ font (prevents zoom)</div>');
                } else {
                    results.push(`<div class="status-indicator status-warning">⚠️ ${smallInputs} inputs below 16px font</div>`);
                }
                
                // Test viewport
                const viewport = document.querySelector('meta[name="viewport"]');
                if (viewport && viewport.content.includes('user-scalable=no')) {
                    results.push('<div class="status-indicator status-good">✅ Viewport prevents unwanted zooming</div>');
                } else {
                    results.push('<div class="status-indicator status-warning">⚠️ Viewport may allow unwanted zooming</div>');
                }
                
                // Test iPad optimizations loaded
                if (window.iPadOptimizations) {
                    results.push('<div class="status-indicator status-good">✅ iPad optimizations loaded</div>');
                } else {
                    results.push('<div class="status-indicator status-warning">⚠️ iPad optimizations not loaded</div>');
                }
                
                document.getElementById('testResults').innerHTML = results.join('');
            }, 1000);
            
            // Orientation change test
            window.addEventListener('orientationchange', function() {
                setTimeout(() => {
                    document.getElementById('orientation').textContent = screen.orientation ? screen.orientation.type : 'Unknown';
                }, 500);
            });
        });
    </script>
</body>
</html>