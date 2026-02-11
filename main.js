// Food Ordering Website - Main JavaScript File

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (input.value.trim() === '') {
            input.style.borderColor = '#ff6b6b';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });

    return isValid;
}

// Email Validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Phone Validation
function validatePhone(phone) {
    const phoneRegex = /^[0-9]{10,}$/;
    return phoneRegex.test(phone.replace(/[^\d]/g, ''));
}

// Add to Cart with Confirmation
function addToCart(foodId, foodName) {
    if (foodId && foodName) {
        alert(foodName + ' has been added to cart!');
        return true;
    } else {
        alert('Error adding item to cart. Please try again.');
        return false;
    }
}

// Remove from Cart with Confirmation
function removeFromCart(itemId) {
    if (confirm('Are you sure you want to remove this item from cart?')) {
        return true;
    }
    return false;
}

// Search Form Validation
function validateSearch() {
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput && searchInput.value.trim() === '') {
        alert('Please enter a search term');
        return false;
    }
    return true;
}

// Login Form Validation
function validateLogin(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const email = form.querySelector('input[name="email"]');
    const password = form.querySelector('input[name="password"]');

    let isValid = true;

    if (!email || email.value.trim() === '') {
        alert('Please enter your email');
        isValid = false;
    } else if (!validateEmail(email.value)) {
        alert('Please enter a valid email address');
        isValid = false;
    }

    if (!password || password.value.trim() === '') {
        alert('Please enter your password');
        isValid = false;
    } else if (password.value.length < 6) {
        alert('Password must be at least 6 characters long');
        isValid = false;
    }

    return isValid;
}

// Register Form Validation
function validateRegister(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const firstName = form.querySelector('input[name="first_name"]');
    const lastName = form.querySelector('input[name="last_name"]');
    const email = form.querySelector('input[name="email"]');
    const phone = form.querySelector('input[name="phone"]');
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="confirm_password"]');

    let isValid = true;

    if (!firstName || firstName.value.trim() === '') {
        alert('Please enter your first name');
        isValid = false;
    }

    if (!lastName || lastName.value.trim() === '') {
        alert('Please enter your last name');
        isValid = false;
    }

    if (!email || email.value.trim() === '') {
        alert('Please enter your email');
        isValid = false;
    } else if (!validateEmail(email.value)) {
        alert('Please enter a valid email address');
        isValid = false;
    }

    if (phone && phone.value.trim() !== '') {
        if (!validatePhone(phone.value)) {
            alert('Please enter a valid phone number');
            isValid = false;
        }
    }

    if (!password || password.value.trim() === '') {
        alert('Please enter a password');
        isValid = false;
    } else if (password.value.length < 6) {
        alert('Password must be at least 6 characters long');
        isValid = false;
    }

    if (!confirmPassword || confirmPassword.value.trim() === '') {
        alert('Please confirm your password');
        isValid = false;
    } else if (confirmPassword.value !== password.value) {
        alert('Passwords do not match');
        isValid = false;
    }

    return isValid;
}

// Smooth Scroll to Element
function smoothScroll(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

// Toggle Mobile Menu
function toggleMenu() {
    const menu = document.querySelector('.menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Format Currency - Tanzania Shilling
function formatCurrency(amount) {
    return 'Tsh ' + new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount) + '/=';
}

// Calculate Total Cart Price
function calculateTotal() {
    const rows = document.querySelectorAll('table tbody tr');
    let total = 0;

    rows.forEach(row => {
        const priceCell = row.querySelector('td:nth-child(3)');
        const quantityCell = row.querySelector('input[name="qty"]');
        
        if (priceCell && quantityCell) {
            const price = parseFloat(priceCell.textContent.replace(/[^0-9.-]+/g, ''));
            const quantity = parseInt(quantityCell.value);
            total += price * quantity;
        }
    });

    const totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.textContent = formatCurrency(total);
    }

    return total;
}

// Update Cart when Quantity Changes
function updateCartQuantity(input) {
    if (input.value < 1) {
        input.value = 1;
    }
    calculateTotal();
}

// Initialize on Page Load
document.addEventListener('DOMContentLoaded', function() {
    // Focus on search input if page is search page
    const searchInput = document.querySelector('input[name="q"]');
    if (searchInput) {
        searchInput.focus();
    }

    // Calculate total on cart page
    if (document.getElementById('cart-total')) {
        calculateTotal();
    }

    // Add event listeners to quantity inputs
    const qtyInputs = document.querySelectorAll('input[name="qty"]');
    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            updateCartQuantity(this);
        });
    });

    // Add form submission event handlers
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (form.id === 'login-form') {
                if (!validateLogin('login-form')) {
                    e.preventDefault();
                }
            } else if (form.id === 'register-form') {
                if (!validateRegister('register-form')) {
                    e.preventDefault();
                }
            } else if (form.action.includes('food-search')) {
                if (!validateSearch()) {
                    e.preventDefault();
                }
            }
        });
    });
});

// Confirm Action
function confirmAction(message) {
    return confirm(message || 'Are you sure you want to proceed?');
}
