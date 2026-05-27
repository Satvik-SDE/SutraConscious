import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/**/*.php',
        './resources/js/**/*.js',
    ],

    safelist: [
        'is-revealed',
        'media-primary',
        'media-alt',
        'media-solo',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    blue: '#267696',
                    blueDark: '#1B5A75',
                    blueLight: '#3a8eae',
                    skin: '#EED6C3',
                    skinDeep: '#E0BFA6',
                    black: '#0E0F0F',
                },
                surface: {
                    cream: '#FBFAF6',
                    line: '#EEE8DE',
                    mute: '#F4EFE8',
                },
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Russo One"', ...defaultTheme.fontFamily.sans],
                script: ['"Caveat Brush"', 'cursive'],
            },
            fontSize: {
                'display-xl': ['clamp(3.5rem, 8vw, 7rem)', { lineHeight: '0.95', letterSpacing: '-0.02em' }],
                'display-lg': ['clamp(2.5rem, 5vw, 4.5rem)', { lineHeight: '1', letterSpacing: '-0.02em' }],
                'display-md': ['clamp(2rem, 3.5vw, 3rem)', { lineHeight: '1.05', letterSpacing: '-0.015em' }],
                'script-xl': ['clamp(4rem, 9vw, 8rem)', { lineHeight: '0.85' }],
                'script-lg': ['clamp(3rem, 6vw, 5rem)', { lineHeight: '0.9' }],
                'eyebrow': ['0.6875rem', { lineHeight: '1.2', letterSpacing: '0.3em' }],
            },
            aspectRatio: {
                'product': '4 / 5',
                'editorial': '3 / 4',
                'hero': '4 / 5',
            },
            spacing: {
                'section': 'clamp(4rem, 8vw, 8rem)',
                'section-sm': 'clamp(2.5rem, 5vw, 5rem)',
            },
            container: {
                center: true,
                padding: {
                    DEFAULT: '1rem',
                    sm: '1.5rem',
                    lg: '2rem',
                },
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(24px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'slide-in-right': {
                    '0%': { transform: 'translateX(100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                'marquee': {
                    '0%': { transform: 'translateX(0)' },
                    '100%': { transform: 'translateX(-50%)' },
                },
                'shimmer': {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'underline-grow': {
                    '0%': { transform: 'scaleX(0)' },
                    '100%': { transform: 'scaleX(1)' },
                },
            },
            animation: {
                'fade-up': 'fade-up 800ms cubic-bezier(0.22, 1, 0.36, 1) both',
                'fade-in': 'fade-in 600ms ease-out both',
                'slide-in-right': 'slide-in-right 400ms cubic-bezier(0.22, 1, 0.36, 1)',
                'marquee': 'marquee 28s linear infinite',
                'shimmer': 'shimmer 2s linear infinite',
            },
            transitionTimingFunction: {
                'silk': 'cubic-bezier(0.22, 1, 0.36, 1)',
            },
            boxShadow: {
                'soft': '0 1px 2px rgba(14, 15, 15, 0.04), 0 8px 24px -8px rgba(14, 15, 15, 0.08)',
                'lift': '0 4px 12px rgba(14, 15, 15, 0.06), 0 16px 32px -12px rgba(14, 15, 15, 0.10)',
            },
        },
    },

    plugins: [forms, typography],
};
