# UI Components Implementation Guide
## Code Examples & Implementation Details

**Project:** UbugeniPalace - Artisan Marketplace  
**Purpose:** Reusable component library for modern web applications  

---

## 1. Header & Navigation System

### Main Header Structure
```html
<header class="header">
    <div class="container">
        <div class="header-content">
            <!-- Logo Section -->
            <div class="header-left">
                <div class="logo-container">
                    <div class="logo-circle">
                        <img src="assets/images/logo/logo.png" alt="Logo" class="logo">
                    </div>
                </div>
                <div class="branding">
                    <h1 class="site-name">UbugeniPalace</h1>
                    <p class="tagline">Discover Authentic Rwandan Craftsmanship</p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="nav">
                <a href="/" class="nav-link">Home</a>
                <a href="/pages/products.php" class="nav-link">Products</a>
                <a href="/pages/artisans.php" class="nav-link">Artisans</a>
                <a href="/pages/about.php" class="nav-link">About</a>
                <a href="/pages/contact.php" class="nav-link">Contact</a>
            </nav>

            <!-- Header Actions -->
            <div class="header-right">
                <div class="search-box">
                    <input type="text" placeholder="Search products...">
                    <button type="button">
                        <img src="assets/images/icons/search.svg" alt="Search">
                    </button>
                </div>
                
                <div class="user-actions">
                    <a href="/pages/login.php" class="login-btn">Login</a>
                    <a href="/pages/register.php" class="signup-btn">Sign Up</a>
                </div>
            </div>
        </div>
    </div>
</header>
```

### Header CSS Implementation
```css
.header {
    background-color: var(--bg-primary);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 100;
    transition: all var(--transition-normal);
}

.header.scrolled {
    box-shadow: var(--shadow-md);
}

.header-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--spacing-md) 0;
}

/* Logo Section */
.header-left {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
}

.logo-container {
    position: relative;
}

.logo-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    transition: all var(--transition-normal);
}

.logo-circle:hover {
    transform: scale(1.1);
}

.logo {
    width: 30px;
    height: 30px;
    transition: transform var(--transition-normal);
}

/* Navigation */
.nav {
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
}

.nav-link {
    color: var(--text-primary);
    text-decoration: none;
    font-weight: 500;
    transition: color var(--transition-fast);
    position: relative;
}

.nav-link::before {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background-color: var(--primary);
    transition: width var(--transition-fast);
}

.nav-link:hover::before,
.nav-link.active::before {
    width: 100%;
}

/* Header Actions */
.header-right {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box input {
    padding: var(--spacing-sm) var(--spacing-lg);
    padding-right: 40px;
    border: 1px solid var(--border);
    border-radius: var(--radius-full);
    font-size: var(--font-size-sm);
    transition: all var(--transition-fast);
}

.search-box input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.search-box button {
    position: absolute;
    right: var(--spacing-sm);
    background: none;
    border: none;
    cursor: pointer;
}

/* User Actions */
.login-btn, .signup-btn {
    padding: var(--spacing-sm) var(--spacing-lg);
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 500;
    transition: all var(--transition-fast);
}

.login-btn {
    color: var(--text-primary);
    background-color: transparent;
}

.signup-btn {
    color: var(--text-white);
    background-color: var(--primary);
}

.login-btn:hover {
    background-color: var(--bg-secondary);
}

.signup-btn:hover {
    background-color: var(--primary-dark);
    transform: translateY(-1px);
}
```

---

## 2. Product Card System

### Product Card HTML Structure
```html
<article class="product-card" data-product="123">
    <div class="product-image">
        <img src="assets/images/products/product1.jpg" alt="Product Name" loading="lazy">
        
        <!-- Product Badges -->
        <div class="product-badges">
            <span class="badge featured">Featured</span>
            <span class="badge sale">Sale</span>
        </div>
        
        <!-- Product Actions -->
        <div class="product-actions">
            <a href="product-details.php?id=123" class="btn-icon view">
                <span>View Details</span>
            </a>
        </div>
    </div>
    
    <div class="product-info">
        <div class="product-category">Pottery</div>
        <h3 class="product-name">
            <a href="product-details.php?id=123">Traditional Rwandan Vase</a>
        </h3>
        <div class="product-artisan">
            By <a href="artisan-profile.php?id=456">Jean Pierre</a>
            <div class="artisan-rating">
                <div class="stars">
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star filled">★</span>
                    <span class="star">★</span>
                </div>
            </div>
        </div>
        <div class="product-price">
            <span class="price-original">RWF 15,000</span>
            <span class="price-current">RWF 12,000</span>
        </div>
        <button class="add-to-cart" data-product="123">
            Add to Cart
        </button>
    </div>
</article>
```

### Product Card CSS Implementation
```css
.product-card {
    background-color: var(--bg-primary);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-normal);
    position: relative;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

/* Product Image */
.product-image {
    position: relative;
    overflow: hidden;
    aspect-ratio: 1;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform var(--transition-normal);
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

/* Product Badges */
.product-badges {
    position: absolute;
    top: var(--spacing-sm);
    left: var(--spacing-sm);
    display: flex;
    gap: var(--spacing-xs);
}

.badge {
    padding: var(--spacing-xs) var(--spacing-sm);
    border-radius: var(--radius-sm);
    font-size: var(--font-size-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge.featured {
    background-color: var(--primary);
    color: var(--text-white);
}

.badge.sale {
    background-color: var(--accent);
    color: var(--text-white);
}

/* Product Actions */
.product-actions {
    position: absolute;
    top: var(--spacing-md);
    right: var(--spacing-md);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
    opacity: 0;
    transform: translateX(10px);
    transition: all var(--transition-normal);
}

.product-card:hover .product-actions {
    opacity: 1;
    transform: translateX(0);
}

.product-actions .btn-icon.view {
    background-color: transparent;
    border: none;
    color: var(--text-white);
    text-decoration: none;
    font-weight: 500;
    font-size: var(--font-size-sm);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
}

.product-actions .btn-icon.view:hover {
    background-color: transparent;
    border: none;
    color: var(--text-white);
    transform: translateY(-1px);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.7);
}

/* Product Info */
.product-info {
    padding: var(--spacing-lg);
}

.product-category {
    font-size: var(--font-size-sm);
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: var(--spacing-xs);
}

.product-name {
    font-size: var(--font-size-lg);
    font-weight: 600;
    margin-bottom: var(--spacing-sm);
}

.product-name a {
    color: var(--text-primary);
    text-decoration: none;
}

.product-name a:hover {
    color: var(--primary);
}

.product-artisan {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin-bottom: var(--spacing-md);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
}

.product-artisan a {
    color: var(--primary);
    font-weight: 500;
}

/* Rating Stars */
.artisan-rating {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
}

.stars {
    display: flex;
    gap: 2px;
}

.star {
    color: #d1d5db;
    font-size: var(--font-size-sm);
}

.star.filled {
    color: var(--rwanda-yellow);
}

/* Product Price */
.product-price {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
}

.price-original {
    font-size: var(--font-size-sm);
    color: var(--text-light);
    text-decoration: line-through;
}

.price-current {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--primary);
}

/* Add to Cart Button */
.add-to-cart {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-lg);
    background-color: var(--primary);
    color: var(--text-white);
    border: none;
    border-radius: var(--radius-md);
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.add-to-cart:hover {
    background-color: var(--primary-dark);
    transform: translateY(-1px);
}
```

---

## 3. Form System

### Form HTML Structure
```html
<form class="form" id="registrationForm">
    <div class="form-group">
        <label for="fullName" class="form-label">Full Name *</label>
        <input type="text" id="fullName" name="full_name" class="form-input" required>
        <div class="field-error" id="fullNameError"></div>
    </div>

    <div class="form-group">
        <label for="email" class="form-label">Email Address *</label>
        <input type="email" id="email" name="email" class="form-input" required>
        <div class="field-error" id="emailError"></div>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Password *</label>
        <div class="password-input-group">
            <input type="password" id="password" name="password" class="form-input" required>
            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                <span class="eye-icon">👁️</span>
            </button>
        </div>
        <div class="password-strength">
            <div class="strength-bar">
                <div class="strength-fill" id="strengthFill"></div>
            </div>
            <span class="strength-text" id="strengthText">Weak</span>
        </div>
        <div class="field-error" id="passwordError"></div>
    </div>

    <div class="form-group">
        <label class="checkbox-label">
            <input type="checkbox" class="checkbox-input" required>
            <span class="checkbox-text">
                I agree to the <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a>
            </span>
        </label>
    </div>

    <button type="submit" class="btn btn-primary btn-full">Create Account</button>
</form>
```

### Form CSS Implementation
```css
.form {
    max-width: 500px;
    margin: 0 auto;
}

.form-group {
    margin-bottom: var(--spacing-lg);
}

.form-label {
    display: block;
    font-weight: 500;
    margin-bottom: var(--spacing-xs);
    color: var(--text-primary);
}

.form-input {
    width: 100%;
    padding: var(--spacing-sm) var(--spacing-md);
    font-size: var(--font-size-base);
    color: var(--text-primary);
    background-color: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    transition: all var(--transition-fast);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.form-input.error {
    border-color: var(--error);
}

/* Password Input Group */
.password-input-group {
    position: relative;
    display: flex;
    align-items: center;
}

.password-toggle {
    position: absolute;
    right: var(--spacing-sm);
    background: none;
    border: none;
    cursor: pointer;
    padding: var(--spacing-xs);
    border-radius: var(--radius-sm);
    transition: background-color var(--transition-fast);
}

.password-toggle:hover {
    background-color: var(--bg-secondary);
}

.eye-icon {
    font-size: var(--font-size-lg);
}

/* Password Strength */
.password-strength {
    margin-top: var(--spacing-sm);
}

.strength-bar {
    width: 100%;
    height: 4px;
    background-color: var(--border);
    border-radius: var(--radius-sm);
    overflow: hidden;
    margin-bottom: var(--spacing-xs);
}

.strength-fill {
    height: 100%;
    transition: all var(--transition-normal);
}

.strength-fill.weak { width: 25%; background-color: var(--error); }
.strength-fill.fair { width: 50%; background-color: var(--warning); }
.strength-fill.good { width: 75%; background-color: var(--accent); }
.strength-fill.strong { width: 100%; background-color: var(--success); }

.strength-text {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
}

/* Field Errors */
.field-error {
    color: var(--error);
    font-size: var(--font-size-sm);
    margin-top: var(--spacing-xs);
    min-height: 1.2em;
}

/* Checkbox */
.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: var(--spacing-sm);
    cursor: pointer;
}

.checkbox-input {
    margin-top: 2px;
}

.checkbox-text {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    line-height: 1.4;
}

.checkbox-text a {
    color: var(--primary);
    text-decoration: none;
}

.checkbox-text a:hover {
    text-decoration: underline;
}

/* Form Buttons */
.btn-full {
    width: 100%;
    padding: var(--spacing-md) var(--spacing-lg);
    font-size: var(--font-size-base);
    font-weight: 500;
}
```

---

## 4. Modal System

### Modal HTML Structure
```html
<div class="modal-overlay" id="loginModal">
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-logo">
                    <div class="logo-circle">
                        <img src="assets/images/logo/logo.png" alt="Logo" class="logo">
                    </div>
                </div>
                <h2 class="modal-title">Welcome Back</h2>
                <p class="modal-subtitle">Sign in to your account</p>
                <button class="modal-close" onclick="closeModal('loginModal')">
                    <span class="close-icon">×</span>
                </button>
            </div>
            
            <div class="modal-body">
                <form class="login-form" id="loginForm">
                    <div class="input-group">
                        <span class="input-icon">📧</span>
                        <input type="email" placeholder="Email address" required>
                    </div>
                    
                    <div class="input-group">
                        <span class="input-icon">🔒</span>
                        <input type="password" placeholder="Password" required>
                        <button type="button" class="password-toggle">
                            <span class="eye-icon">👁️</span>
                        </button>
                    </div>
                    
                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox">
                            <span>Remember me</span>
                        </label>
                        <a href="/forgot-password" class="forgot-link">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-full">Sign In</button>
                </form>
            </div>
            
            <div class="modal-footer">
                <p class="modal-footer-text">
                    Don't have an account? 
                    <a href="/register" class="modal-link">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</div>
```

### Modal CSS Implementation
```css
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition-normal);
}

.modal-overlay.active {
    opacity: 1;
    visibility: visible;
}

.modal-container {
    background-color: var(--bg-primary);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    max-width: 400px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    transform: scale(0.9) translateY(-20px);
    transition: all var(--transition-normal);
}

.modal-overlay.active .modal-container {
    transform: scale(1) translateY(0);
}

.modal-content {
    padding: var(--spacing-xl);
}

.modal-header {
    text-align: center;
    margin-bottom: var(--spacing-xl);
    position: relative;
}

.modal-logo {
    margin-bottom: var(--spacing-lg);
}

.modal-logo .logo-circle {
    width: 60px;
    height: 60px;
    margin: 0 auto;
}

.modal-title {
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--spacing-xs);
}

.modal-subtitle {
    color: var(--text-secondary);
    font-size: var(--font-size-base);
}

.modal-close {
    position: absolute;
    top: 0;
    right: 0;
    background: none;
    border: none;
    font-size: var(--font-size-2xl);
    cursor: pointer;
    color: var(--text-secondary);
    padding: var(--spacing-xs);
    border-radius: var(--radius-sm);
    transition: all var(--transition-fast);
}

.modal-close:hover {
    background-color: var(--bg-secondary);
    color: var(--text-primary);
}

.modal-body {
    margin-bottom: var(--spacing-xl);
}

/* Input Groups */
.input-group {
    position: relative;
    margin-bottom: var(--spacing-lg);
}

.input-icon {
    position: absolute;
    left: var(--spacing-md);
    top: 50%;
    transform: translateY(-50%);
    font-size: var(--font-size-lg);
    color: var(--text-secondary);
}

.input-group .form-input {
    padding-left: 40px;
}

.modal-footer {
    text-align: center;
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--border);
}

.modal-footer-text {
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
}

.modal-link {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
}

.modal-link:hover {
    text-decoration: underline;
}
```

---

## 5. Animation System

### CSS Animations
```css
/* Fade In Animation */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Animation Classes */
.animate-fade-in { animation: fadeIn 0.6s ease-out; }
.animate-fade-up { animation: fadeInUp 0.6s ease-out; }
.animate-fade-down { animation: fadeInDown 0.6s ease-out; }
.animate-slide-left { animation: slideInLeft 0.6s ease-out; }
.animate-slide-right { animation: slideInRight 0.6s ease-out; }
.animate-scale-in { animation: scaleIn 0.6s ease-out; }

/* Staggered Animations */
.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }
.stagger-5 { animation-delay: 0.5s; }
```

### JavaScript Animation Utilities
```javascript
// Animation utility functions
const AnimationUtils = {
    // Fade in element
    fadeIn: (element, duration = 300) => {
        element.style.opacity = '0';
        element.style.display = 'block';
        
        let start = null;
        const animate = (timestamp) => {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const opacity = Math.min(progress / duration, 1);
            
            element.style.opacity = opacity;
            
            if (progress < duration) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    },

    // Slide in from bottom
    slideInUp: (element, duration = 300) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.display = 'block';
        
        let start = null;
        const animate = (timestamp) => {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const ratio = Math.min(progress / duration, 1);
            
            element.style.opacity = ratio;
            element.style.transform = `translateY(${30 * (1 - ratio)}px)`;
            
            if (progress < duration) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    },

    // Scale in
    scaleIn: (element, duration = 300) => {
        element.style.opacity = '0';
        element.style.transform = 'scale(0.9)';
        element.style.display = 'block';
        
        let start = null;
        const animate = (timestamp) => {
            if (!start) start = timestamp;
            const progress = timestamp - start;
            const ratio = Math.min(progress / duration, 1);
            
            element.style.opacity = ratio;
            element.style.transform = `scale(${0.9 + (0.1 * ratio)})`;
            
            if (progress < duration) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    },

    // Intersection Observer for scroll animations
    observeElements: (selector, animationClass) => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(animationClass);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        document.querySelectorAll(selector).forEach(el => {
            observer.observe(el);
        });
    }
};

// Initialize scroll animations
document.addEventListener('DOMContentLoaded', () => {
    AnimationUtils.observeElements('.animate-on-scroll', 'animate-fade-up');
});
```

---

## 6. Responsive Design Patterns

### Mobile-First CSS
```css
/* Base styles (mobile) */
.container {
    padding: 0 var(--spacing-md);
    max-width: 100%;
}

.products-grid {
    grid-template-columns: 1fr;
    gap: var(--spacing-md);
}

.nav {
    display: none; /* Hide on mobile */
}

.mobile-menu-toggle {
    display: block; /* Show on mobile */
}

/* Tablet (768px and up) */
@media (min-width: 768px) {
    .container {
        max-width: var(--container-md);
        margin: 0 auto;
    }
    
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }
    
    .mobile-menu-toggle {
        display: none;
    }
}

/* Desktop (1024px and up) */
@media (min-width: 1024px) {
    .container {
        max-width: var(--container-lg);
    }
    
    .products-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-xl);
    }
    
    .nav {
        display: flex; /* Show on desktop */
    }
}

/* Large Desktop (1280px and up) */
@media (min-width: 1280px) {
    .container {
        max-width: var(--container-xl);
    }
    
    .products-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}
```

### Responsive Typography
```css
/* Mobile-first typography */
.hero-title {
    font-size: var(--font-size-3xl);
    line-height: 1.2;
}

.section-title {
    font-size: var(--font-size-2xl);
}

.product-name {
    font-size: var(--font-size-base);
}

/* Tablet adjustments */
@media (min-width: 768px) {
    .hero-title {
        font-size: var(--font-size-4xl);
    }
    
    .section-title {
        font-size: var(--font-size-3xl);
    }
    
    .product-name {
        font-size: var(--font-size-lg);
    }
}

/* Desktop adjustments */
@media (min-width: 1024px) {
    .hero-title {
        font-size: var(--font-size-5xl);
    }
    
    .section-title {
        font-size: var(--font-size-4xl);
    }
}
```

---

## 7. JavaScript Interaction Patterns

### Event Handling
```javascript
// Event delegation for dynamic content
document.addEventListener('click', (e) => {
    // Product card interactions
    if (e.target.closest('.product-card')) {
        const card = e.target.closest('.product-card');
        const productId = card.dataset.product;
        
        if (e.target.matches('.add-to-cart')) {
            addToCart(productId);
        }
        
        if (e.target.closest('.product-name a')) {
            // Product link clicked - let it navigate naturally
        }
    }
    
    // Modal interactions
    if (e.target.matches('.modal-close') || e.target.closest('.modal-close')) {
        const modal = e.target.closest('.modal-overlay');
        closeModal(modal.id);
    }
    
    // Form submissions
    if (e.target.matches('form')) {
        e.preventDefault();
        handleFormSubmission(e.target);
    }
});

// Form validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        const errorElement = document.getElementById(input.id + 'Error');
        
        if (!input.value.trim()) {
            showFieldError(input, 'This field is required');
            isValid = false;
        } else if (input.type === 'email' && !isValidEmail(input.value)) {
            showFieldError(input, 'Please enter a valid email address');
            isValid = false;
        } else {
            clearFieldError(input);
        }
    });
    
    return isValid;
}

function showFieldError(input, message) {
    const errorElement = document.getElementById(input.id + 'Error');
    if (errorElement) {
        errorElement.textContent = message;
        input.classList.add('error');
    }
}

function clearFieldError(input) {
    const errorElement = document.getElementById(input.id + 'Error');
    if (errorElement) {
        errorElement.textContent = '';
        input.classList.remove('error');
    }
}

// AJAX utilities
const API = {
    async post(url, data) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },
    
    async get(url) {
        try {
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
};

// Notification system
const NotificationSystem = {
    show(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Trigger animation
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, duration);
    }
};
```

---

## 8. Performance Optimization

### CSS Optimization
```css
/* Use CSS transforms instead of position changes */
.hover-lift {
    transform: translateY(0);
    transition: transform var(--transition-normal);
}

.hover-lift:hover {
    transform: translateY(-2px);
}

/* Optimize animations with will-change */
.animated-element {
    will-change: transform, opacity;
}

/* Use hardware acceleration */
.hardware-accelerated {
    transform: translateZ(0);
}

/* Optimize paint operations */
.optimized-paint {
    contain: layout style paint;
}
```

### JavaScript Performance
```javascript
// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Throttle function for scroll events
function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Lazy loading for images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}
```

---

This implementation guide provides all the code patterns and structures used in the UbugeniPalace project. Use these as a foundation for creating modern, responsive, and accessible web applications with consistent design patterns and smooth interactions. 