import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import aspectRatio from "@tailwindcss/aspect-ratio";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                plaschema: {
                    DEFAULT: "#10B981",
                    dark: "#059669",
                    light: "#34D399",
                },
            },
            textColor: {
                plaschema: "#10B981",
            },
            borderColor: {
                plaschema: "#10B981",
            },
            backgroundColor: {
                plaschema: "#10B981",
            },
            variables: {
                "--color-plaschema-rgb": "16, 185, 129",
            },
            // Animation extensions
            animation: {
                "fade-in":
                    "fadeIn var(--animation-duration-medium) var(--animation-easing-out) forwards",
                "fade-out":
                    "fadeOut var(--animation-duration-medium) var(--animation-easing-in) forwards",
                "slide-up":
                    "slideUp var(--animation-duration-medium) var(--animation-easing-out) forwards",
                "slide-down":
                    "slideDown var(--animation-duration-medium) var(--animation-easing-out) forwards",
                "slide-right":
                    "slideRight var(--animation-duration-medium) var(--animation-easing-out) forwards",
                "slide-left":
                    "slideLeft var(--animation-duration-medium) var(--animation-easing-out) forwards",
                "scale-in":
                    "scaleIn var(--animation-duration-medium) var(--animation-easing-out) forwards",
                "scale-out":
                    "scaleOut var(--animation-duration-medium) var(--animation-easing-in) forwards",
                pulse: "pulse var(--animation-duration-slow) var(--animation-easing-in-out) infinite",
            },
            keyframes: {
                fadeIn: {
                    from: { opacity: "0" },
                    to: { opacity: "1" },
                },
                fadeOut: {
                    from: { opacity: "1" },
                    to: { opacity: "0" },
                },
                slideUp: {
                    from: { transform: "translateY(20px)", opacity: "0" },
                    to: { transform: "translateY(0)", opacity: "1" },
                },
                slideDown: {
                    from: { transform: "translateY(-20px)", opacity: "0" },
                    to: { transform: "translateY(0)", opacity: "1" },
                },
                slideRight: {
                    from: { transform: "translateX(-20px)", opacity: "0" },
                    to: { transform: "translateX(0)", opacity: "1" },
                },
                slideLeft: {
                    from: { transform: "translateX(20px)", opacity: "0" },
                    to: { transform: "translateX(0)", opacity: "1" },
                },
                scaleIn: {
                    from: { transform: "scale(0.9)", opacity: "0" },
                    to: { transform: "scale(1)", opacity: "1" },
                },
                scaleOut: {
                    from: { transform: "scale(1)", opacity: "1" },
                    to: { transform: "scale(0.9)", opacity: "0" },
                },
                pulse: {
                    "0%": { transform: "scale(1)" },
                    "50%": { transform: "scale(1.05)" },
                    "100%": { transform: "scale(1)" },
                },
            },
            // Shadow system extensions
            boxShadow: {
                sm: "var(--shadow-sm)",
                md: "var(--shadow-md)",
                lg: "var(--shadow-lg)",
                "plaschema-glow": "var(--shadow-plaschema-glow)",
            },
            transitionProperty: {
                shadow: "box-shadow",
            },
            transitionDuration: {
                fast: "var(--animation-duration-fast)",
                medium: "var(--animation-duration-medium)",
                slow: "var(--animation-duration-slow)",
            },
            transitionTimingFunction: {
                default: "var(--animation-easing-default)",
                in: "var(--animation-easing-in)",
                out: "var(--animation-easing-out)",
                "in-out": "var(--animation-easing-in-out)",
            },
        },
    },

    plugins: [forms, aspectRatio],
};
