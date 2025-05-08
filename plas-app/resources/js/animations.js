/**
 * PLASCHEMA Animation Helper Functions
 *
 * This file contains JavaScript utilities to support the animations.css framework,
 * including scroll-based animations, reduced motion detection, and performance optimizations.
 */

/**
 * Check if the user has requested reduced motion
 * @returns {boolean} True if reduced motion is preferred
 */
export function prefersReducedMotion() {
    return window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

/**
 * Check if the device is likely a low-end device based on various factors
 * @returns {boolean} True if the device is considered low-end
 */
export function isLowEndDevice() {
    // Check for memory constraints (if available)
    if ("deviceMemory" in navigator) {
        if (navigator.deviceMemory < 4) {
            return true;
        }
    }

    // Check for CPU core count (if available)
    if ("hardwareConcurrency" in navigator) {
        if (navigator.hardwareConcurrency < 4) {
            return true;
        }
    }

    // If we can't detect directly, use viewport as a proxy for device capability
    // Mobile devices with small viewports are more likely to be lower-end
    const viewportWidth = Math.max(
        document.documentElement.clientWidth,
        window.innerWidth || 0
    );
    if (
        viewportWidth < 768 &&
        "maxTouchPoints" in navigator &&
        navigator.maxTouchPoints > 0
    ) {
        return true;
    }

    return false;
}

/**
 * Setup scroll-based animations using Intersection Observer
 * @param {string} selector - CSS selector for elements to animate
 * @param {string} animationClass - CSS class to apply when element is visible
 * @param {Object} options - Additional options for the observer
 */
export function setupScrollAnimations(
    selector = ".animate-on-scroll",
    animationClass = "fade-in",
    options = {}
) {
    // Don't set up animations if user prefers reduced motion
    if (prefersReducedMotion()) {
        // Make all elements visible without animation
        document.querySelectorAll(selector).forEach((el) => {
            el.style.opacity = "1";
        });
        return;
    }

    // Use simpler animations for low-end devices
    const isLowPower = isLowEndDevice();

    // Default options
    const defaultOptions = {
        root: null, // viewport
        rootMargin: "0px",
        threshold: 0.1, // 10% of the element visible
    };

    // Merge default options with provided options
    const observerOptions = { ...defaultOptions, ...options };

    // Create the observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const el = entry.target;

                // Get animation class from data attribute if specified, otherwise use the default
                const animation = el.dataset.animation || animationClass;

                // Add animation class, possibly with a simpler version for low-end devices
                if (isLowPower && el.dataset.simpleAnimation) {
                    el.classList.add(el.dataset.simpleAnimation);
                } else {
                    el.classList.add(animation);
                }

                // Add any delay specified in data-delay attribute
                if (el.dataset.delay) {
                    el.style.animationDelay = `${el.dataset.delay}ms`;
                }

                // Stop observing this element once it's animated
                observer.unobserve(el);
            }
        });
    }, observerOptions);

    // Start observing elements
    document.querySelectorAll(selector).forEach((el) => {
        // Hide element initially
        el.style.opacity = "0";
        observer.observe(el);
    });
}

/**
 * Initialize animations when the DOM is loaded
 */
document.addEventListener("DOMContentLoaded", () => {
    // Setup animation system with performance adaptations
    if (!prefersReducedMotion()) {
        // Initialize scroll animations for different components with appropriate classes

        // Cards with staggered effect
        setupScrollAnimations(".card-animate", "scale-in", {
            threshold: 0.1,
            rootMargin: "50px",
        });

        // Hero section elements
        setupScrollAnimations(".hero-animate", "fade-in", {
            threshold: 0,
            rootMargin: "0px",
        });

        // List items with staggered animation
        document.querySelectorAll(".stagger-container").forEach((container) => {
            const items = container.querySelectorAll(".stagger-item");
            items.forEach((item, index) => {
                item.style.opacity = "0";
                item.style.animationDelay = `${index * 50}ms`;

                const observer = new IntersectionObserver(
                    (entries) => {
                        entries.forEach((entry) => {
                            if (entry.isIntersecting) {
                                item.classList.add("fade-in");
                                observer.unobserve(item);
                            }
                        });
                    },
                    { threshold: 0.1 }
                );

                observer.observe(item);
            });
        });
    } else {
        // For users who prefer reduced motion, make all elements visible without animation
        document
            .querySelectorAll(
                ".animate-on-scroll, .card-animate, .hero-animate, .stagger-item"
            )
            .forEach((el) => {
                el.style.opacity = "1";
            });
    }
});

/**
 * Throttle function to limit how often a function can be called
 * Useful for scroll and resize events
 *
 * @param {Function} func - Function to throttle
 * @param {number} limit - Time limit in ms
 * @returns {Function} Throttled function
 */
export function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), limit);
        }
    };
}

/**
 * Apply shadow classes based on scroll position
 * Useful for navigation bars that change elevation on scroll
 *
 * @param {string} selector - Element to apply shadow to
 * @param {string} shadowClass - Shadow class to apply
 * @param {number} scrollThreshold - Scroll position to trigger the shadow
 */
export function applyShadowOnScroll(
    selector = ".navbar",
    shadowClass = "shadow-md",
    scrollThreshold = 50
) {
    const element = document.querySelector(selector);
    if (!element) return;

    const handleScroll = throttle(() => {
        if (window.scrollY > scrollThreshold) {
            element.classList.add(shadowClass);
        } else {
            element.classList.remove(shadowClass);
        }
    }, 100); // Throttle to every 100ms

    window.addEventListener("scroll", handleScroll);

    // Initial check
    handleScroll();
}
