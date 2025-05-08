import "./bootstrap";
import "./validation";
import "./accessibility";
import "./animations";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

/**
 * Setup lazy loading for images
 */
document.addEventListener("DOMContentLoaded", function () {
    // Check if browser supports Intersection Observer
    if ("IntersectionObserver" in window) {
        // Get all images with loading="lazy" attribute
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');

        const imageObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const img = entry.target;

                        // If the image has a data-src attribute, use it to replace src
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                        }

                        // If the image has srcset data, apply it when visible
                        if (img.dataset.srcset) {
                            img.srcset = img.dataset.srcset;
                        }

                        // Stop observing this image once it's loaded
                        observer.unobserve(img);

                        // Apply a fade-in effect
                        img.classList.add("loaded");
                    }
                });
            },
            {
                // Settings for the observer
                rootMargin: "50px 0px", // Start loading when image is 50px away
                threshold: 0.01, // Trigger when even 1% of the image is visible
            }
        );

        // Start observing each image
        lazyImages.forEach((img) => {
            imageObserver.observe(img);
        });
    } else {
        // Fallback for browsers that don't support Intersection Observer
        // Replace all data-src with src
        document.querySelectorAll("img[data-src]").forEach((img) => {
            img.src = img.dataset.src;
        });

        // Replace all data-srcset with srcset
        document.querySelectorAll("img[data-srcset]").forEach((img) => {
            img.srcset = img.dataset.srcset;
        });
    }
});
