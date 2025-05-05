/**
 * PLASCHEMA Accessibility Enhancements
 * Improves accessibility through keyboard navigation, focus management, and ARIA support
 */

document.addEventListener("DOMContentLoaded", function () {
    // Add skip to content link if it doesn't exist
    if (!document.querySelector(".skip-to-content")) {
        const skipLink = document.createElement("a");
        skipLink.className = "skip-to-content";
        skipLink.href = "#main-content";
        skipLink.textContent = "Skip to main content";
        document.body.insertBefore(skipLink, document.body.firstChild);

        // Make sure the main content has an id to target
        const mainContent =
            document.querySelector("main") || document.querySelector("#app");
        if (mainContent && !mainContent.id) {
            mainContent.id = "main-content";
        }
    }

    // Enhance keyboard navigation for dropdown menus
    setupDropdownAccessibility();

    // Ensure all interactive elements are keyboard accessible
    ensureKeyboardAccessibility();

    // Add ARIA attributes to improve screen reader experience
    enhanceAriaSupport();

    // Add focus trap for modals
    setupModalFocusTrap();

    // Make tables more accessible with proper markup
    enhanceTableAccessibility();

    /**
     * Set up keyboard accessibility for dropdown menus
     */
    function setupDropdownAccessibility() {
        const dropdownToggles = document.querySelectorAll(
            "[data-dropdown-toggle]"
        );

        dropdownToggles.forEach((toggle) => {
            // Add appropriate ARIA attributes
            toggle.setAttribute("aria-haspopup", "true");
            toggle.setAttribute("aria-expanded", "false");

            // Get the target dropdown
            const targetId = toggle.getAttribute("data-dropdown-toggle");
            const dropdown = document.getElementById(targetId);

            if (dropdown) {
                // Make sure dropdown has role="menu"
                dropdown.setAttribute("role", "menu");

                // Find all dropdown items and enhance them
                const dropdownItems = dropdown.querySelectorAll("a, button");
                dropdownItems.forEach((item, index) => {
                    item.setAttribute("role", "menuitem");
                    item.setAttribute("tabindex", "-1"); // Only focused items get tabindex 0
                });

                // Add keyboard navigation
                toggle.addEventListener("keydown", function (event) {
                    // Toggle dropdown on Enter or Space
                    if (event.key === "Enter" || event.key === " ") {
                        event.preventDefault();
                        toggle.click();

                        // If opening the dropdown, focus the first item
                        if (toggle.getAttribute("aria-expanded") === "false") {
                            toggle.setAttribute("aria-expanded", "true");
                            if (dropdownItems.length > 0) {
                                setTimeout(() => {
                                    dropdownItems[0].focus();
                                }, 100);
                            }
                        }
                    }
                });

                // Add keyboard navigation within the dropdown
                dropdown.addEventListener("keydown", function (event) {
                    // Find all dropdown items
                    const items = Array.from(
                        dropdown.querySelectorAll('[role="menuitem"]')
                    );
                    const currentIndex = items.indexOf(document.activeElement);

                    if (event.key === "ArrowDown") {
                        event.preventDefault();
                        const nextIndex = (currentIndex + 1) % items.length;
                        items[nextIndex].focus();
                    } else if (event.key === "ArrowUp") {
                        event.preventDefault();
                        const prevIndex =
                            (currentIndex - 1 + items.length) % items.length;
                        items[prevIndex].focus();
                    } else if (event.key === "Escape") {
                        event.preventDefault();
                        toggle.click(); // Close dropdown
                        toggle.focus(); // Return focus to toggle
                    } else if (
                        event.key === "Tab" &&
                        !event.shiftKey &&
                        currentIndex === items.length - 1
                    ) {
                        // Close dropdown when tabbing out
                        toggle.setAttribute("aria-expanded", "false");
                    } else if (
                        event.key === "Tab" &&
                        event.shiftKey &&
                        currentIndex === 0
                    ) {
                        // Close dropdown when tabbing out backwards
                        toggle.setAttribute("aria-expanded", "false");
                    }
                });

                // Update ARIA expanded state when dropdown is toggled
                toggle.addEventListener("click", function () {
                    const isExpanded =
                        toggle.getAttribute("aria-expanded") === "true";
                    toggle.setAttribute(
                        "aria-expanded",
                        (!isExpanded).toString()
                    );
                });

                // Close dropdown when clicking outside
                document.addEventListener("click", function (event) {
                    if (
                        !dropdown.contains(event.target) &&
                        !toggle.contains(event.target)
                    ) {
                        toggle.setAttribute("aria-expanded", "false");
                    }
                });
            }
        });
    }

    /**
     * Ensure all interactive elements are keyboard accessible
     */
    function ensureKeyboardAccessibility() {
        // Find all possibly interactive elements without tabindex
        const elements = document.querySelectorAll(
            "div[onclick], span[onclick], a:not([href]):not([tabindex])"
        );

        elements.forEach((element) => {
            // If it's clickable but not focusable, make it focusable
            if (!element.hasAttribute("tabindex")) {
                element.setAttribute("tabindex", "0");

                // Add appropriate role if missing
                if (!element.hasAttribute("role")) {
                    element.setAttribute("role", "button");
                }

                // Add keyboard event handlers
                element.addEventListener("keydown", function (event) {
                    if (event.key === "Enter" || event.key === " ") {
                        event.preventDefault();
                        element.click();
                    }
                });
            }
        });
    }

    /**
     * Add ARIA attributes to improve screen reader experience
     */
    function enhanceAriaSupport() {
        // Ensure all images have alt text
        document.querySelectorAll("img:not([alt])").forEach((img) => {
            img.setAttribute("alt", ""); // Empty alt for decorative images
        });

        // Ensure form fields have labels or aria-label
        document
            .querySelectorAll("input, select, textarea")
            .forEach((field) => {
                const id = field.getAttribute("id");
                if (id) {
                    const hasLabel = document.querySelector(
                        `label[for="${id}"]`
                    );
                    if (!hasLabel && !field.hasAttribute("aria-label")) {
                        const placeholder = field.getAttribute("placeholder");
                        if (placeholder) {
                            field.setAttribute("aria-label", placeholder);
                        } else {
                            // Extract a name from the id as fallback
                            const name = id
                                .replace(/([A-Z])/g, " $1")
                                .replace(/[_-]/g, " ")
                                .replace(/^\w/, (c) => c.toUpperCase());
                            field.setAttribute("aria-label", name);
                        }
                    }
                }
            });

        // Add appropriate roles to common UI elements
        document.querySelectorAll("header:not([role])").forEach((header) => {
            header.setAttribute("role", "banner");
        });

        document.querySelectorAll("nav:not([role])").forEach((nav) => {
            nav.setAttribute("role", "navigation");
        });

        document.querySelectorAll("main:not([role])").forEach((main) => {
            main.setAttribute("role", "main");
        });

        document.querySelectorAll("footer:not([role])").forEach((footer) => {
            footer.setAttribute("role", "contentinfo");
        });

        document.querySelectorAll("section:not([role])").forEach((section) => {
            section.setAttribute("role", "region");
            if (
                !section.hasAttribute("aria-label") &&
                !section.hasAttribute("aria-labelledby")
            ) {
                const heading = section.querySelector("h1, h2, h3, h4, h5, h6");
                if (heading) {
                    if (!heading.id) {
                        heading.id =
                            "section-heading-" +
                            Math.random().toString(36).substring(2, 9);
                    }
                    section.setAttribute("aria-labelledby", heading.id);
                }
            }
        });
    }

    /**
     * Set up focus trap for modal dialogs
     */
    function setupModalFocusTrap() {
        // Find all modals
        const modals = document.querySelectorAll('[role="dialog"], .modal');

        modals.forEach((modal) => {
            // Ensure the modal has the dialog role
            if (!modal.hasAttribute("role")) {
                modal.setAttribute("role", "dialog");
            }

            // Ensure the modal is labeled
            if (
                !modal.hasAttribute("aria-labelledby") &&
                !modal.hasAttribute("aria-label")
            ) {
                const heading = modal.querySelector("h1, h2, h3, h4, h5, h6");
                if (heading) {
                    if (!heading.id) {
                        heading.id =
                            "modal-heading-" +
                            Math.random().toString(36).substring(2, 9);
                    }
                    modal.setAttribute("aria-labelledby", heading.id);
                } else {
                    modal.setAttribute("aria-label", "Dialog");
                }
            }

            // Find all focusable elements
            const focusableElements = modal.querySelectorAll(
                'a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])'
            );

            if (focusableElements.length > 0) {
                const firstElement = focusableElements[0];
                const lastElement =
                    focusableElements[focusableElements.length - 1];

                // Trap focus within the modal
                modal.addEventListener("keydown", function (event) {
                    if (event.key === "Tab") {
                        if (
                            event.shiftKey &&
                            document.activeElement === firstElement
                        ) {
                            event.preventDefault();
                            lastElement.focus();
                        } else if (
                            !event.shiftKey &&
                            document.activeElement === lastElement
                        ) {
                            event.preventDefault();
                            firstElement.focus();
                        }
                    }
                });
            }
        });
    }

    /**
     * Make tables more accessible with proper markup
     */
    function enhanceTableAccessibility() {
        // Find all tables
        const tables = document.querySelectorAll(
            'table:not([role="presentation"])'
        );

        tables.forEach((table) => {
            // Ensure the table has proper markup
            if (!table.querySelector("caption")) {
                // Try to find a heading just before the table
                let heading = table.previousElementSibling;
                if (heading && /^h[1-6]$/i.test(heading.tagName)) {
                    // Create a visually hidden caption with the heading text
                    const caption = document.createElement("caption");
                    caption.textContent = heading.textContent;
                    caption.style.position = "absolute";
                    caption.style.width = "1px";
                    caption.style.height = "1px";
                    caption.style.padding = "0";
                    caption.style.margin = "-1px";
                    caption.style.overflow = "hidden";
                    caption.style.clip = "rect(0, 0, 0, 0)";
                    caption.style.whiteSpace = "nowrap";
                    caption.style.border = "0";

                    table.prepend(caption);
                }
            }

            // Ensure table headers have scope attribute
            const headerCells = table.querySelectorAll("th");
            headerCells.forEach((th) => {
                if (!th.hasAttribute("scope")) {
                    // Determine if this is a column or row header
                    let isColumnHeader = false;
                    const headerRow = th.closest("tr");
                    if (
                        headerRow &&
                        headerRow.parentElement &&
                        headerRow.parentElement.tagName === "THEAD"
                    ) {
                        isColumnHeader = true;
                    } else {
                        // Check if this is the first cell in a row
                        const cellIndex = Array.from(
                            headerRow.children
                        ).indexOf(th);
                        isColumnHeader = cellIndex === 0;
                    }

                    th.setAttribute("scope", isColumnHeader ? "col" : "row");
                }
            });
        });
    }
});
