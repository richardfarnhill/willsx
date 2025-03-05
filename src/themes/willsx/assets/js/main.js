/**
 * WillsX Theme JavaScript
 * Main JavaScript file for theme functionality
 */

(function() {
    'use strict';

    // DOM Elements
    const body = document.body;
    const header = document.querySelector('.site-header');
    const menuToggle = document.querySelector('.menu-toggle');
    const primaryMenuContainer = document.querySelector('.primary-menu-container');
    const themeToggle = document.querySelector('.theme-toggle');
    const cookieNotice = document.querySelector('.cookie-notice');
    const cookieButtons = document.querySelectorAll('.cookie-notice-button');
    const preferencesModal = document.querySelector('.cookie-preferences-modal');
    const preferencesButtons = document.querySelectorAll('.cookie-preferences-button');

    /**
     * Toggle Mobile Menu
     */
    function setupMobileMenu() {
        if (!menuToggle) return;

        menuToggle.addEventListener('click', function() {
            const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', !isExpanded);
            primaryMenuContainer.classList.toggle('toggled');
            
            // Toggle ARIA attributes
            if (primaryMenuContainer.classList.contains('toggled')) {
                menuToggle.setAttribute('aria-expanded', 'true');
                menuToggle.setAttribute('aria-label', 'Close menu');
            } else {
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Open menu');
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (primaryMenuContainer && primaryMenuContainer.classList.contains('toggled') && 
                !primaryMenuContainer.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                primaryMenuContainer.classList.remove('toggled');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.setAttribute('aria-label', 'Open menu');
            }
        });
    }

    /**
     * Sticky Header
     */
    function setupStickyHeader() {
        if (!header) return;

        let lastScrollTop = 0;
        const scrollThreshold = 50;

        window.addEventListener('scroll', function() {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (currentScrollTop > scrollThreshold) {
                header.classList.add('is-sticky');
            } else {
                header.classList.remove('is-sticky');
            }

            // Hide/show header based on scroll direction
            if (currentScrollTop > lastScrollTop && currentScrollTop > header.offsetHeight) {
                // Scrolling down, hide header
                header.classList.add('is-hidden');
            } else {
                // Scrolling up, show header
                header.classList.remove('is-hidden');
            }
            
            lastScrollTop = currentScrollTop;
        });
    }

    /**
     * Theme Switcher (Dark/Light mode)
     */
    function setupThemeSwitcher() {
        if (!themeToggle) return;

        // Check for saved theme preference or use user's system preference
        const savedTheme = localStorage.getItem('theme');
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
        
        // Set initial theme
        if (savedTheme === 'dark' || (!savedTheme && prefersDarkScheme.matches)) {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
        }
        
        // Toggle theme on button click
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update button ARIA label
            themeToggle.setAttribute('aria-label', newTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        });
        
        // Listen for system preference changes
        prefersDarkScheme.addEventListener('change', function(e) {
            if (!localStorage.getItem('theme')) {
                const newTheme = e.matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', newTheme);
            }
        });
    }

    /**
     * Smooth Scroll for anchor links
     */
    function setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    window.scrollTo({
                        top: targetElement.offsetTop - 100, // Offset for fixed header
                        behavior: 'smooth'
                    });
                    
                    // Update URL hash without jumping
                    history.pushState(null, null, targetId);
                }
            });
        });
    }

    /**
     * Lazy Load Images
     */
    function setupLazyLoading() {
        if ('loading' in HTMLImageElement.prototype) {
            // Browser supports native lazy loading
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            lazyImages.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback for browsers that don't support lazy loading
            const lazyImageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const lazyImage = entry.target;
                        if (lazyImage.dataset.src) {
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.removeAttribute('data-src');
                        }
                        observer.unobserve(lazyImage);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                lazyImageObserver.observe(img);
            });
        }
    }

    /**
     * Cookie Notice Handling
     */
    function setupCookieNotice() {
        if (!cookieNotice) return;

        // Check if user has already made cookie choices
        const cookieConsent = getCookie('willsx_cookie_consent');
        
        if (!cookieConsent) {
            // Show cookie notice if no consent is found
            cookieNotice.classList.add('is-active');
        }
        
        // Handle cookie notice buttons
        if (cookieButtons) {
            cookieButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    
                    if (action === 'accept-all') {
                        // Accept all cookies
                        setCookieConsent('all');
                        hideCookieNotice();
                    } else if (action === 'accept-necessary') {
                        // Accept only necessary cookies
                        setCookieConsent('necessary');
                        hideCookieNotice();
                    } else if (action === 'preferences') {
                        // Show preferences modal
                        showPreferencesModal();
                    }
                });
            });
        }
        
        // Handle preferences modal buttons
        if (preferencesButtons) {
            preferencesButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const action = this.dataset.action;
                    
                    if (action === 'save') {
                        // Save preferences
                        const analyticsConsent = document.getElementById('cookie-analytics').checked;
                        const marketingConsent = document.getElementById('cookie-marketing').checked;
                        
                        setCookieConsent('custom', {
                            analytics: analyticsConsent,
                            marketing: marketingConsent
                        });
                        
                        hidePreferencesModal();
                        hideCookieNotice();
                    } else if (action === 'close') {
                        // Close modal without saving
                        hidePreferencesModal();
                    }
                });
            });
        }
    }
    
    /**
     * Set cookie consent preferences
     * @param {string} level - Consent level ('all', 'necessary', 'custom')
     * @param {object} customPreferences - Custom cookie preferences
     */
    function setCookieConsent(level, customPreferences = {}) {
        const consentData = {
            level: level,
            necessary: true, // Always true - necessary cookies
            analytics: level === 'all' || (level === 'custom' && customPreferences.analytics),
            marketing: level === 'all' || (level === 'custom' && customPreferences.marketing),
            timestamp: new Date().getTime()
        };
        
        // Set cookie that expires in 1 year
        setCookie('willsx_cookie_consent', JSON.stringify(consentData), 365);
        
        // Dispatch consent event for analytics and marketing scripts
        const consentEvent = new CustomEvent('willsxCookieConsent', { detail: consentData });
        document.dispatchEvent(consentEvent);
    }
    
    /**
     * Hide cookie notice
     */
    function hideCookieNotice() {
        if (cookieNotice) {
            cookieNotice.classList.remove('is-active');
        }
    }
    
    /**
     * Show preferences modal
     */
    function showPreferencesModal() {
        if (preferencesModal) {
            preferencesModal.style.display = 'flex';
            
            // Set focus on first focusable element
            const focusableElements = preferencesModal.querySelectorAll('button, [href], input, select, textarea');
            if (focusableElements.length) {
                focusableElements[0].focus();
            }
            
            // Close modal when clicking outside
            preferencesModal.addEventListener('click', function(event) {
                if (event.target === preferencesModal) {
                    hidePreferencesModal();
                }
            });
            
            // Close modal with Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    hidePreferencesModal();
                }
            });
        }
    }
    
    /**
     * Hide preferences modal
     */
    function hidePreferencesModal() {
        if (preferencesModal) {
            preferencesModal.style.display = 'none';
        }
    }
    
    /**
     * Set a cookie
     * @param {string} name - Cookie name
     * @param {string} value - Cookie value
     * @param {number} days - Days until expiration
     */
    function setCookie(name, value, days) {
        let expires = '';
        
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        
        document.cookie = name + '=' + encodeURIComponent(value) + expires + '; path=/; SameSite=Lax';
    }
    
    /**
     * Get a cookie value
     * @param {string} name - Cookie name
     * @returns {string|null} Cookie value or null if not found
     */
    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
        }
        
        return null;
    }

    /**
     * Handle animations
     */
    function setupAnimations() {
        // Only run if Intersection Observer is supported
        if (!('IntersectionObserver' in window)) return;

        const animatedElements = document.querySelectorAll('.animate-on-scroll');
        
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animated');
                    animationObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1, // Trigger when 10% of the element is visible
            rootMargin: '0px 0px -50px 0px' // Adjusts the trigger point
        });
        
        animatedElements.forEach(element => {
            animationObserver.observe(element);
        });
    }

    /**
     * Initialize all functionality
     */
    function init() {
        // Initialize core functionality
        setupMobileMenu();
        setupStickyHeader();
        setupThemeSwitcher();
        setupSmoothScroll();
        setupLazyLoading();
        setupCookieNotice();
        setupAnimations();

        // Remove 'no-js' class
        document.documentElement.classList.remove('no-js');
        document.documentElement.classList.add('js');
    }

    // Run when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})(); 