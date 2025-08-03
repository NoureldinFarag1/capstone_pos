// iPad Touch Optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Detect if we're on an iPad/tablet
    const isTablet = /iPad|Tablet|Android/i.test(navigator.userAgent) || 
                    (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    
    if (isTablet) {
        document.body.classList.add('is-tablet');
        
        // Prevent zoom on double tap for buttons and form elements
        let lastTouchEnd = 0;
        document.addEventListener('touchend', function (event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);
        
        // Add touch feedback to buttons
        document.addEventListener('touchstart', function(e) {
            if (e.target.matches('button, .btn, a')) {
                e.target.style.transform = 'scale(0.98)';
                e.target.style.transition = 'transform 0.1s ease';
            }
        });
        
        document.addEventListener('touchend', function(e) {
            if (e.target.matches('button, .btn, a')) {
                setTimeout(() => {
                    e.target.style.transform = '';
                }, 100);
            }
        });
        
        // Improve dropdown behavior on touch
        document.querySelectorAll('[x-data*="isOpen"]').forEach(dropdown => {
            dropdown.addEventListener('touchstart', function(e) {
                e.stopPropagation();
            });
        });
        
        // Improve form input experience
        document.addEventListener('focusin', function(e) {
            if (e.target.matches('input, textarea, select')) {
                // Prevent zoom by setting font-size to 16px minimum
                if (window.getComputedStyle(e.target).fontSize < '16px') {
                    e.target.style.fontSize = '16px';
                }
                
                // Scroll into view with padding
                setTimeout(() => {
                    e.target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }, 300);
            }
        });
        
        // Add better touch scrolling
        document.querySelectorAll('.overflow-auto, .overflow-y-auto, .overflow-x-auto').forEach(element => {
            element.style.webkitOverflowScrolling = 'touch';
        });
        
        // Improve modal interactions
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal')) {
                // Close modal when clicking backdrop
                const modal = bootstrap.Modal.getInstance(e.target);
                if (modal) {
                    modal.hide();
                }
            }
        });
    }
    
    // POS-specific touch improvements
    if (document.querySelector('.pos-interface')) {
        // Make quantity controls easier to use
        document.querySelectorAll('.quantity-control').forEach(input => {
            // Add + and - buttons for easier quantity adjustment
            const wrapper = document.createElement('div');
            wrapper.className = 'quantity-wrapper d-flex align-items-center';
            
            const minusBtn = document.createElement('button');
            minusBtn.type = 'button';
            minusBtn.className = 'btn btn-outline-secondary btn-sm quantity-btn';
            minusBtn.innerHTML = '<i class="fas fa-minus"></i>';
            minusBtn.style.minHeight = '44px';
            minusBtn.style.minWidth = '44px';
            
            const plusBtn = document.createElement('button');
            plusBtn.type = 'button';
            plusBtn.className = 'btn btn-outline-secondary btn-sm quantity-btn';
            plusBtn.innerHTML = '<i class="fas fa-plus"></i>';
            plusBtn.style.minHeight = '44px';
            plusBtn.style.minWidth = '44px';
            
            minusBtn.addEventListener('click', function() {
                const current = parseInt(input.value) || 1;
                if (current > 1) {
                    input.value = current - 1;
                    input.dispatchEvent(new Event('change'));
                }
            });
            
            plusBtn.addEventListener('click', function() {
                const current = parseInt(input.value) || 1;
                input.value = current + 1;
                input.dispatchEvent(new Event('change'));
            });
            
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(minusBtn);
            wrapper.appendChild(input);
            wrapper.appendChild(plusBtn);
        });
        
        // Improve barcode scanner input
        const barcodeInput = document.getElementById('barcode');
        if (barcodeInput) {
            barcodeInput.addEventListener('focus', function() {
                // Ensure virtual keyboard doesn't hide the input
                setTimeout(() => {
                    this.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 300);
            });
        }
    }
    
    // Improve payment method selection
    document.querySelectorAll('.payment-method-btn').forEach(btn => {
        btn.addEventListener('touchstart', function() {
            this.style.backgroundColor = '#0d6efd';
            this.style.color = 'white';
        });
        
        btn.addEventListener('touchend', function() {
            setTimeout(() => {
                this.style.backgroundColor = '';
                this.style.color = '';
            }, 150);
        });
    });
    
    // Improve cart item interactions
    document.addEventListener('click', function(e) {
        if (e.target.closest('.cart-item')) {
            const cartItem = e.target.closest('.cart-item');
            cartItem.style.backgroundColor = '#f8f9fa';
            setTimeout(() => {
                cartItem.style.backgroundColor = '';
            }, 200);
        }
    });
    
    // Add haptic feedback simulation for supported devices
    function hapticFeedback() {
        if (navigator.vibrate) {
            navigator.vibrate(10);
        }
    }
    
    // Add haptic feedback to important buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('.btn-primary, .btn-success, .btn-danger, .payment-method-btn')) {
            hapticFeedback();
        }
    });
});

// Orientation change handler
window.addEventListener('orientationchange', function() {
    // Refresh layout after orientation change
    setTimeout(() => {
        window.scrollTo(0, 0);
        
        // Recalculate any fixed positioning
        document.querySelectorAll('.floating-summary').forEach(element => {
            element.style.display = 'none';
            setTimeout(() => {
                element.style.display = '';
            }, 100);
        });
    }, 500);
});

// Prevent context menu on long press for better UX
document.addEventListener('contextmenu', function(e) {
    if (e.target.matches('button, .btn, a, input[type="button"], input[type="submit"]')) {
        e.preventDefault();
    }
});

// Export for use in other scripts
window.iPadOptimizations = {
    isTablet: /iPad|Tablet|Android/i.test(navigator.userAgent) || 
              (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1),
    hapticFeedback: function() {
        if (navigator.vibrate) {
            navigator.vibrate(10);
        }
    }
};