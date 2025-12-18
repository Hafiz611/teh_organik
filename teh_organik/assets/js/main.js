// JavaScript untuk Teh Organik Website

// Utility Functions
const Utils = {
    // Format currency
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },

    // Format date
    formatDate: (dateString) => {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    },

    // Debounce function
    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Show notification
    showNotification: (message, type = 'success') => {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    },

    // Loading state
    setLoading: (element, loading = true) => {
        if (loading) {
            element.disabled = true;
            element.innerHTML = '<span class="loading"></span> Loading...';
        } else {
            element.disabled = false;
            element.innerHTML = element.getAttribute('data-original-text') || element.innerHTML;
        }
    }
};

// Cart Management
const Cart = {
    items: [],
    
    init: function() {
        this.loadFromStorage();
        this.updateUI();
    },

    loadFromStorage: function() {
        const stored = localStorage.getItem('teh_organik_cart');
        if (stored) {
            this.items = JSON.parse(stored);
        }
    },

    saveToStorage: function() {
        localStorage.setItem('teh_organik_cart', JSON.stringify(this.items));
    },

    add: function(productId, name, price, quantity = 1) {
        const existingItem = this.items.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.items.push({
                id: productId,
                name: name,
                price: price,
                quantity: quantity
            });
        }
        
        this.saveToStorage();
        this.updateUI();
        Utils.showNotification('Produk ditambahkan ke keranjang!');
    },

    remove: function(productId) {
        this.items = this.items.filter(item => item.id !== productId);
        this.saveToStorage();
        this.updateUI();
    },

    updateQuantity: function(productId, quantity) {
        const item = this.items.find(item => item.id === productId);
        if (item) {
            item.quantity = Math.max(1, quantity);
            this.saveToStorage();
            this.updateUI();
        }
    },

    getTotal: function() {
        return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    },

    getCount: function() {
        return this.items.reduce((total, item) => total + item.quantity, 0);
    },

    clear: function() {
        this.items = [];
        this.saveToStorage();
        this.updateUI();
    },

    updateUI: function() {
        // Update cart count in navigation
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = this.getCount();
        });

        // Update cart total
        const cartTotalElements = document.querySelectorAll('.cart-total');
        cartTotalElements.forEach(element => {
            element.textContent = Utils.formatCurrency(this.getTotal());
        });

        // Update cart items display
        this.updateCartDisplay();
    },

    updateCartDisplay: function() {
        const cartItemsContainer = document.getElementById('cartItems');
        if (!cartItemsContainer) return;

        if (this.items.length === 0) {
            cartItemsContainer.innerHTML = '<p class="text-muted">Keranjang belanja kosong</p>';
        } else {
            let html = '';
            this.items.forEach(item => {
                html += `
                    <div class="cart-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${item.name}</strong><br>
                                <small>${item.quantity} x ${Utils.formatCurrency(item.price)}</small>
                            </div>
                            <div>
                                <strong>${Utils.formatCurrency(item.price * item.quantity)}</strong>
                                <button class="btn btn-sm btn-danger ms-2" onclick="Cart.remove('${item.id}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            cartItemsContainer.innerHTML = html;
        }
    }
};

// Product Management
const Product = {
    filter: function(category) {
        const products = document.querySelectorAll('.product-item');
        
        products.forEach(product => {
            const productCategory = product.dataset.category;
            if (category === '' || productCategory === category) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    },

    search: function(query) {
        const products = document.querySelectorAll('.product-item');
        const searchTerm = query.toLowerCase();
        
        products.forEach(product => {
            const title = product.querySelector('.product-title').textContent.toLowerCase();
            const description = product.querySelector('.product-description').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        });
    }
};

// Form Validation
const Validation = {
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    validatePhone: function(phone) {
        const re = /^[0-9]{10,13}$/;
        return re.test(phone);
    },

    validateRequired: function(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        return isValid;
    },

    showError: function(field, message) {
        field.classList.add('is-invalid');
        let feedback = field.parentNode.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            field.parentNode.appendChild(feedback);
        }
        feedback.textContent = message;
    },

    clearErrors: function(form) {
        form.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
        });
        form.querySelectorAll('.invalid-feedback').forEach(feedback => {
            feedback.remove();
        });
    }
};

// Initialize on DOM load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart
    Cart.init();

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar background on scroll
    window.addEventListener('scroll', Utils.debounce(function() {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }, 100));

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!Validation.validateRequired(form)) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });

    // Product quantity controls
    document.querySelectorAll('.quantity-control').forEach(control => {
        const input = control.querySelector('input');
        const decreaseBtn = control.querySelector('.decrease');
        const increaseBtn = control.querySelector('.increase');
        const max = parseInt(input.getAttribute('max')) || 999;

        decreaseBtn.addEventListener('click', () => {
            const current = parseInt(input.value);
            if (current > 1) {
                input.value = current - 1;
                input.dispatchEvent(new Event('change'));
            }
        });

        increaseBtn.addEventListener('click', () => {
            const current = parseInt(input.value);
            if (current < max) {
                input.value = current + 1;
                input.dispatchEvent(new Event('change'));
            }
        });
    });

    // Image preview for file uploads
    document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(input.getAttribute('data-preview'));
                    if (preview) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

// Export for use in other files
window.TehOrganik = {
    Utils,
    Cart,
    Product,
    Validation
};