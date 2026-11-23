import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

// Theme tokens for the admin / app area are CSS variables defined in
// resources/css/app.css (:root). Change colours there, not here.
const token = (name) => `rgb(var(--${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
export default {
    // Dark mode only when a parent has the "dark" class (the app area is light).
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/livewire/livewire/src/Features/SupportPagination/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Public website (dark IPL theme) – unchanged
                'primary-bg': '#0B0F19',
                'card-bg': '#141B2D',
                'accent-gold': '#FFC800',
                'accent-red': '#E63946',
                'success-green': '#00C853',

                // Admin / app theme (light SaaS)
                brand: {
                    DEFAULT: token('color-brand'),
                    hover: token('color-brand-hover'),
                    soft: token('color-brand-soft'),
                    deep: token('color-brand-deep'),
                },
                accent: {
                    DEFAULT: token('color-accent'),
                    hover: token('color-accent-hover'),
                    soft: token('color-accent-soft'),
                },
                canvas: token('color-canvas'),
                surface: {
                    DEFAULT: token('color-surface'),
                    muted: token('color-surface-muted'),
                    subtle: token('color-surface-subtle'),
                },
                line: {
                    DEFAULT: token('color-line'),
                    strong: token('color-line-strong'),
                },
                ink: {
                    DEFAULT: token('color-ink'),
                    soft: token('color-ink-soft'),
                },
                muted: token('color-muted'),
                faint: token('color-faint'),
                danger: token('color-danger'),
                success: token('color-success'),
                warning: token('color-warning'),
            },
            // Radius and shadow read CSS variables so the admin area (.theme-admin in
            // resources/css/app.css) can square them off; the fallbacks are Tailwind's
            // defaults, so the public site is unchanged.
            borderRadius: {
                sm: 'var(--radius-sm, 0.125rem)',
                DEFAULT: 'var(--radius, 0.25rem)',
                md: 'var(--radius-md, 0.375rem)',
                lg: 'var(--radius-lg, 0.5rem)',
                xl: 'var(--radius-xl, 0.75rem)',
                '2xl': 'var(--radius-2xl, 1rem)',
                '3xl': 'var(--radius-3xl, 1.5rem)',
            },
            boxShadow: {
                sm: 'var(--shadow-sm, 0 1px 2px 0 rgb(0 0 0 / 0.05))',
                DEFAULT: 'var(--shadow, 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1))',
                md: 'var(--shadow-md, 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1))',
                card: 'var(--shadow-card, 0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.04))',
                pop: 'var(--shadow-pop, 0 10px 30px -10px rgb(15 23 42 / 0.20), 0 4px 6px -4px rgb(15 23 42 / 0.06))',
            },
        },
    },

    plugins: [forms],
};
