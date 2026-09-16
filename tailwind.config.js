import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Design tokens extracted from the Figma file "Modernize - Powerful UI Kit
 * Design System" (node tree: 👋 01. Components).
 *
 * These are added as NEW, namespaced keys (ds* / inter / control / card…)
 * rather than overriding Tailwind's default scale (sm, base, blue, etc.),
 * so nothing in the existing views changes until they opt in explicitly.
 *
 * NOTE on fidelity: hover/active/focus states in the source file render
 * visually identical to their default state (the Figma component variants
 * don't expose distinct colors for them), so no hover/active tokens are
 * included here — derive interaction states with Tailwind's opacity /
 * brightness utilities instead of guessing colors.
 *
 * NOTE on navbar/sidebar/shadow: previously inferred (approximated) because
 * the Figma MCP had hit its rate limit. Re-verified directly against the
 * "01 dashboard" node — boxShadow.card, navbar height (68px) and sidebar
 * width (250px, see design-system.css) now reflect the real extracted
 * values. .ds-table remains an inferred approximation (not yet re-verified).
 */
/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                ds: {
                    primary: {
                        DEFAULT: '#1e5eff',
                        light: '#d9e4ff',
                    },
                    secondary: {
                        DEFAULT: '#5a607f',
                        light: '#e6e9f4',
                    },
                    success: {
                        DEFAULT: '#1fd286',
                        light: '#c4f8e2',
                        text: '#06a561',
                    },
                    danger: {
                        DEFAULT: '#f0142f',
                        solid: '#f34359',
                        light: '#fcd5d9',
                    },
                    warning: {
                        DEFAULT: '#f99600',
                        light: '#fff4c9',
                    },
                    text: {
                        heading: '#131523',
                        body: '#131523',
                        label: '#5a607f',
                        muted: '#a1a7c4',
                        white: '#ffffff',
                    },
                    border: {
                        DEFAULT: '#d7dbec',
                        input: '#d9e1ec',
                    },
                    surface: {
                        white: '#ffffff',
                        disabled: '#f1f4fa',
                    },
                    bg: {
                        canvas: '#f5f6fa',
                    },
                },
            },

            fontSize: {
                'ds-heading': ['24px', { lineHeight: '36px', fontWeight: '700' }],
                'ds-body': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'ds-small': ['14px', { lineHeight: '20px', fontWeight: '400' }],
            },

            borderRadius: {
                control: '4px', // buttons, inputs, badges
                card: '6px',    // cards
            },

            height: {
                'control-lg': '48px',
                'control-md': '40px',
                'control-sm': '36px',
            },

            boxShadow: {
                // Real value from Figma "01 dashboard" node (card BG layer).
                card: '0 1px 4px 0px rgba(21, 34, 50, 0.08)',
            },
        },
    },

    plugins: [forms],
};
