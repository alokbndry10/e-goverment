// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const mobileInput = document.getElementById('mobile');
    const mobileWrapper = document.getElementById('mobileWrapper');
    const mobileError = document.getElementById('mobileError');
    const mobileErrorText = document.getElementById('mobileErrorText');
    
    if (mobileInput) {
        mobileInput.addEventListener('blur', function() {
            const mobile = this.value.trim();
            if (mobile === '') {
                mobileWrapper.classList.add('error');
                mobileError.style.display = 'block';
                mobileErrorText.textContent = 'Required.';
            } else if (!/^[0-9]{10}$/.test(mobile)) {
                mobileWrapper.classList.add('error');
                mobileError.style.display = 'block';
                mobileErrorText.textContent = 'Enter valid 10-digit mobile number.';
            } else {
                mobileWrapper.classList.remove('error');
                mobileError.style.display = 'none';
                mobileErrorText.textContent = '';
            }
        });
    }
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const mobile = mobileInput.value.trim();
            if (!/^[0-9]{10}$/.test(mobile)) {
                e.preventDefault();
                mobileWrapper.classList.add('error');
                mobileError.style.display = 'block';
                mobileErrorText.textContent = 'Enter valid 10-digit mobile number.';
            }
        });
    }
    
    // Check for URL parameters for messages
    const urlParams = new URLSearchParams(window.location.search);
    const alertBox = document.getElementById('alertBox');
    
    if (urlParams.get('error') && alertBox) {
        alertBox.innerHTML = `<div class="alert alert-error">${decodeURIComponent(urlParams.get('error'))}</div>`;
    }
    
    if (urlParams.get('success') && alertBox) {
        alertBox.innerHTML = `<div class="alert alert-success">${decodeURIComponent(urlParams.get('success'))}</div>`;
    }
});

// File upload preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.classList.remove('active');
    }
});
