// Cart functionality
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let cartCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);

// Update cart count display
function updateCartCount() {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);
        cartCountElement.textContent = cartCount;
        cartCountElement.style.display = cartCount > 0 ? 'flex' : 'none';
    }
}

// Add to cart with visual feedback
function addToCart(productId, productName, productPrice, productImage) {
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: parseFloat(productPrice),
            image: productImage,
            quantity: 1
        });
    }
    
    cartCount = cart.reduce((total, item) => total + (item.quantity || 1), 0);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    return true;
}

// Update user status in navigation
function updateUserStatus() {
    const userLoggedIn = localStorage.getItem('userLoggedIn');
    const userName = localStorage.getItem('userName');
    const userType = localStorage.getItem('userType');
    
    const userInfo = document.getElementById('user-info');
    const authButtons = document.getElementById('auth-buttons');
    const userGreeting = document.getElementById('user-greeting');
    
    console.log('User status:', { userLoggedIn, userName, userType });
    
    if (userLoggedIn === 'true' && userName) {
        if (userInfo) userInfo.style.display = 'flex';
        if (authButtons) authButtons.style.display = 'none';
        if (userGreeting) {
            userGreeting.textContent = `Welcome, ${userName}`;
            if (userType) {
                userGreeting.textContent += ` (${userType})`;
            }
        }
    } else {
        if (userInfo) userInfo.style.display = 'none';
        if (authButtons) authButtons.style.display = 'flex';
    }
}

// Get the correct API URL
function getApiUrl(endpoint) {
    const basePath = window.location.pathname.includes('/') 
        ? window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/'))
        : '';
    return `${basePath}/api/${endpoint}`;
}

// SIMPLE SIGNUP - FIXED VERSION
function setupSignup() {
    const signupForm = document.querySelector('#signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                fullname: document.getElementById('fullname').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone')?.value || '',
                password: document.getElementById('password').value,
                confirmPassword: document.getElementById('confirmPassword').value,
                userType: document.getElementById('userType').value,
                newsletter: document.getElementById('newsletter') ? document.getElementById('newsletter').checked : false
            };
            
            console.log('Form data:', formData);
            
            // Validation
            if (!formData.fullname || !formData.email || !formData.password || !formData.userType) {
                showNotification('Please fill in all required fields!', 'error');
                return;
            }
            
            if (formData.password !== formData.confirmPassword) {
                showNotification('Passwords do not match!', 'error');
                return;
            }
            
            if (formData.password.length < 6) {
                showNotification('Password must be at least 6 characters long!', 'error');
                return;
            }
            
            showNotification('Processing registration...', 'success');
            
            try {
                const apiUrl = 'api/register.php';
                console.log('Calling API:', apiUrl);
                
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('Response data:', result);
                
                if (result.status === 'success') {
                    showNotification(`Welcome ${formData.fullname}! Registration successful! Please login. 🎉`, 'success');
                    
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showNotification(result.message || 'Registration failed!', 'error');
                }
                
            } catch (error) {
                console.error('Signup error:', error);
                showNotification('Cannot connect to server. Please try again.', 'error');
            }
        });
    }
}

// SIMPLE LOGIN - FIXED VERSION
function setupLogin() {
    const loginForm = document.querySelector('#loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                showNotification('Please fill in both email and password!', 'error');
                return;
            }
            
            showNotification('Logging in...', 'success');
            
            try {
                const apiUrl = 'api/login.php';
                console.log('Calling API:', apiUrl);
                
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });
                
                console.log('Response status:', response.status);
                
                const result = await response.json();
                console.log('Response data:', result);
                
                if (result.status === 'success') {
                    showNotification(`Welcome back, ${result.fullname}! Login successful! ✅`, 'success');
                    
                    localStorage.setItem('userLoggedIn', 'true');
                    localStorage.setItem('userId', result.user_id);
                    localStorage.setItem('userName', result.fullname);
                    localStorage.setItem('userEmail', email);
                    localStorage.setItem('userType', result.user_type);
                    
                    updateUserStatus();
                    
                    setTimeout(() => {
                        window.location.href = 'index.html';
                    }, 1500);
                } else {
                    showNotification(result.message || 'Login failed!', 'error');
                }
                
            } catch (error) {
                console.error('Login error:', error);
                showNotification('Cannot connect to server. Please check your connection.', 'error');
            }
        });
    }
}

// Notification System
function showNotification(message, type = 'success') {
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification ${type === 'error' ? 'error' : ''}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'error' ? '#ff4444' : '#4CAF50'};
        color: white;
        border-radius: 5px;
        z-index: 1000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 4000);
}

// Logout function
function logout() {
    localStorage.removeItem('userLoggedIn');
    localStorage.removeItem('userId');
    localStorage.removeItem('userName');
    localStorage.removeItem('userEmail');
    localStorage.removeItem('userType');
    showNotification('Logged out successfully.', 'success');
    updateUserStatus();
    setTimeout(() => {
        window.location.href = 'index.html';
    }, 1500);
}

// Attach event listeners to add to cart buttons
function attachAddToCartListeners() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart:not([disabled])');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = this.getAttribute('data-price');
            const productImage = this.getAttribute('data-image');
            
            if (addToCart(productId, productName, productPrice, productImage)) {
                const originalText = this.innerHTML;
                this.innerHTML = '✓ Added!';
                this.style.background = '#4caf50';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.style.background = '';
                    this.disabled = false;
                }, 2000);
                
                showNotification(`"${productName}" added to cart! 🛒`);
            }
        });
    });
}

// Main initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Website initialized');
    
    const currentPage = window.location.pathname.split('/').pop();
    
    if (currentPage === 'signup.html') setupSignup();
    if (currentPage === 'login.html') setupLogin();
    
    updateCartCount();
    updateUserStatus();
    
    // Set active navigation link
    document.querySelectorAll('nav a').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
    });
    
    if (document.querySelector('.add-to-cart')) {
        attachAddToCartListeners();
    }
    
    console.log('✅ Website fully initialized!');
});

// Make functions available globally
window.addToCart = addToCart;
window.logout = logout;
window.showNotification = showNotification;