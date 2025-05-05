/**
 * PLASCHEMA Form Validation
 * Handles client-side form validation with immediate feedback
 */

document.addEventListener("DOMContentLoaded", function () {
    // Find all forms that need validation
    const forms = document.querySelectorAll('form[data-validate="true"]');

    forms.forEach((form) => {
        // Add validation to each input in the form
        setupFormValidation(form);
    });

    /**
     * Set up validation for all inputs in a form
     * @param {HTMLFormElement} form - The form element to validate
     */
    function setupFormValidation(form) {
        // Find all inputs that require validation
        const inputs = form.querySelectorAll(
            'input[required], select[required], textarea[required], [data-validate="true"]'
        );

        // Set up validation for each input
        inputs.forEach((input) => {
            // Add event listeners for real-time validation
            input.addEventListener("blur", validateField);
            input.addEventListener("input", debounce(validateField, 500));

            // Add visual indicator for required fields
            const label = form.querySelector(`label[for="${input.id}"]`);
            if (label && input.hasAttribute("required")) {
                label.classList.add("required-field");
            }

            // Set ARIA attributes for accessibility
            input.setAttribute("aria-invalid", "false");

            // Add validation status icons container
            const wrapper = document.createElement("div");
            wrapper.className = "input-wrapper";
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);

            const statusIcon = document.createElement("div");
            statusIcon.className = "status-icon";
            wrapper.appendChild(statusIcon);
        });

        // Validate entire form on submit
        form.addEventListener("submit", function (event) {
            let isValid = true;

            // Validate each required input before submission
            inputs.forEach((input) => {
                if (!validateInput(input)) {
                    isValid = false;
                }
            });

            // Prevent submission if validation fails
            if (!isValid) {
                event.preventDefault();

                // Focus on the first invalid input
                const firstInvalid = form.querySelector(".is-invalid");
                if (firstInvalid) {
                    firstInvalid.focus();
                }

                // Scroll to the form top
                form.scrollIntoView({ behavior: "smooth", block: "start" });

                // Show form-level error message
                const formError = document.createElement("div");
                formError.className = "form-error-message";
                formError.setAttribute("role", "alert");
                formError.textContent =
                    "Please correct the errors in the form before submitting.";

                // Remove any existing form error messages
                const existingError = form.querySelector(".form-error-message");
                if (existingError) {
                    existingError.remove();
                }

                form.insertBefore(formError, form.firstChild);

                // Auto-dismiss the message after 5 seconds
                setTimeout(() => {
                    if (formError.parentNode) {
                        formError.classList.add("fade-out");
                        setTimeout(() => {
                            if (formError.parentNode) {
                                formError.remove();
                            }
                        }, 500);
                    }
                }, 5000);
            }
        });
    }

    /**
     * Validate an individual form field and show feedback
     * @param {Event} event - The triggering event
     */
    function validateField(event) {
        const input = event.target;
        validateInput(input);
    }

    /**
     * Validate an input and update its visual state
     * @param {HTMLElement} input - The input element to validate
     * @returns {boolean} - Whether the input is valid
     */
    function validateInput(input) {
        // Skip validation for inputs that aren't visible or are disabled
        if (input.style.display === "none" || input.disabled) {
            return true;
        }

        const wrapper = input.closest(".input-wrapper");
        const statusIcon = wrapper
            ? wrapper.querySelector(".status-icon")
            : null;
        let isValid = true;
        let errorMessage = "";

        // Clear previous validation state
        input.classList.remove("is-valid", "is-invalid");
        if (statusIcon) {
            statusIcon.innerHTML = "";
            statusIcon.classList.remove("valid-icon", "invalid-icon");
        }

        // Remove existing error message
        const existingError =
            input.parentNode.querySelector(".validation-error");
        if (existingError) {
            existingError.remove();
        }

        // Check standard HTML validation
        if (input.validity) {
            isValid = input.validity.valid;

            // Generate appropriate error messages based on validation state
            if (!isValid) {
                if (input.validity.valueMissing) {
                    errorMessage = `This field is required.`;
                } else if (input.validity.typeMismatch) {
                    if (input.type === "email") {
                        errorMessage = `Please enter a valid email address.`;
                    } else if (input.type === "url") {
                        errorMessage = `Please enter a valid URL.`;
                    } else {
                        errorMessage = `Please enter a valid value.`;
                    }
                } else if (input.validity.tooShort) {
                    errorMessage = `Please enter at least ${input.minLength} characters.`;
                } else if (input.validity.tooLong) {
                    errorMessage = `Please enter no more than ${input.maxLength} characters.`;
                } else if (input.validity.patternMismatch) {
                    errorMessage =
                        input.dataset.patternError ||
                        `Please match the requested format.`;
                }
            }
        }

        // Apply custom validation if defined via data attributes
        if (input.dataset.type) {
            const customValidation = validateByType(input, input.dataset.type);
            if (!customValidation.valid) {
                isValid = false;
                errorMessage = customValidation.message;
            }
        }

        // Update input appearance based on validity
        if (input.value) {
            if (isValid) {
                input.classList.add("is-valid");
                input.setAttribute("aria-invalid", "false");
                if (statusIcon) {
                    statusIcon.classList.add("valid-icon");
                    statusIcon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"></path></svg>';
                }
            } else {
                input.classList.add("is-invalid");
                input.setAttribute("aria-invalid", "true");

                // Display error message
                const errorElement = document.createElement("div");
                errorElement.className = "validation-error";
                errorElement.setAttribute("id", `${input.id}-error`);
                errorElement.textContent = errorMessage;
                input.parentNode.appendChild(errorElement);

                // Connect error message to input with aria-describedby
                input.setAttribute("aria-describedby", `${input.id}-error`);

                if (statusIcon) {
                    statusIcon.classList.add("invalid-icon");
                    statusIcon.innerHTML =
                        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"></path></svg>';
                }
            }
        }

        return isValid;
    }

    /**
     * Perform custom validation based on data type
     * @param {HTMLElement} input - The input element
     * @param {string} type - The validation type
     * @returns {Object} - Validation result and message
     */
    function validateByType(input, type) {
        const value = input.value.trim();

        // Skip validation for empty non-required fields
        if (!value && !input.hasAttribute("required")) {
            return { valid: true, message: "" };
        }

        switch (type) {
            case "phone":
                // Simple phone validation - can be enhanced for international formats
                const phonePattern =
                    /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/;
                return {
                    valid: phonePattern.test(value),
                    message: "Please enter a valid phone number.",
                };

            case "name":
                // Name validation - at least 2 characters, letters, spaces, hyphens
                return {
                    valid: value.length >= 2 && /^[A-Za-z\s'-]+$/.test(value),
                    message:
                        "Please enter a valid name (letters, spaces, hyphens only).",
                };

            case "zipcode":
                // Simple postal code validation
                return {
                    valid: /^[0-9]{5}(?:-[0-9]{4})?$/.test(value),
                    message:
                        "Please enter a valid postal code (e.g., 12345 or 12345-6789).",
                };

            case "password":
                // Strong password validation
                const hasUppercase = /[A-Z]/.test(value);
                const hasLowercase = /[a-z]/.test(value);
                const hasNumber = /[0-9]/.test(value);
                const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(value);
                const isLongEnough = value.length >= 8;

                const valid =
                    hasUppercase &&
                    hasLowercase &&
                    hasNumber &&
                    hasSpecial &&
                    isLongEnough;

                let message =
                    "Password must contain at least 8 characters including:";
                if (!hasUppercase) message += " an uppercase letter,";
                if (!hasLowercase) message += " a lowercase letter,";
                if (!hasNumber) message += " a number,";
                if (!hasSpecial) message += " a special character,";

                // Clean up the message formatting
                message = message.replace(/,$/, ".");
                if (message.endsWith("including:.")) {
                    message = "Password must be at least 8 characters.";
                }

                return { valid, message };

            default:
                return { valid: true, message: "" };
        }
    }

    /**
     * Debounce function to limit how often a function can be called
     * @param {Function} func - The function to debounce
     * @param {number} wait - The debounce delay in milliseconds
     * @returns {Function} - The debounced function
     */
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
});
