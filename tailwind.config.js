import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#1a1a2e',
                    light: '#16213e',
                    dark: '#0f0f1e',
                },
                accent: '#0f3460',
                surface: '#ffffff',
                muted: '#f1f5f9',
                border: '#e2e8f0',
            },
            fontSize: {
                'display': ['2rem', { lineHeight: '1.25', fontWeight: '700' }],
                'heading': ['1.5rem', { lineHeight: '1.33', fontWeight: '700' }],
                'title': ['1.25rem', { lineHeight: '1.4', fontWeight: '600' }],
                'subtitle': ['1rem', { lineHeight: '1.5', fontWeight: '600' }],
                'body': ['0.875rem', { lineHeight: '1.5', fontWeight: '400' }],
                'caption': ['0.875rem', { lineHeight: '1.5', fontWeight: '400' }],
            },
            borderRadius: {
                'card': '12px',
                'button': '8px',
                'input': '8px',
                'modal': '16px',
                'badge': '9999px',
            },
            boxShadow: {
                'card': '0 1px 3px rgba(0,0,0,0.08)',
                'dropdown': '0 4px 6px rgba(0,0,0,0.08)',
                'modal': '0 10px 15px rgba(0,0,0,0.08)',
                'card-hover': '0 4px 12px rgba(0,0,0,0.1)',
            },
            spacing: {
                '4.5': '1.125rem',
                '18': '4.5rem',
            },
            animation: {
                'skeleton': 'skeleton 1.5s ease-in-out infinite',
                'fade-in': 'fadeIn 0.2s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'toast-in': 'toastIn 0.3s ease-out',
            },
            keyframes: {
                skeleton: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.4' },
                },
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                toastIn: {
                    '0%': { opacity: '0', transform: 'translateY(-16px) scale(0.95)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
            },
        },
    },

    plugins: [forms],
};
