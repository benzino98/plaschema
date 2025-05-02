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
        },
    },

    plugins: [forms, aspectRatio],
};
