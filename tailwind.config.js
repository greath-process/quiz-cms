const colors = require('tailwindcss/colors')

module.exports = {
  content: [
      './resources/**/*.blade.php',
      './vendor/filament/**/*.blade.php',
  ],
  theme: {
    fontFamily: {
      sans: ["haboro-soft", "ui-sans-serif", "system-ui", "-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "Helvetica Neue", "Arial", "Noto Sans", "sans-serif", "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"],
      mono: ["ui-monospace", "SFMono-Regular", "Menlo", "Monaco", "Consolas", "Liberation Mono", "Courier New", "monospace"],
    },
    fontSize: {
      xs: '.75rem',
      sm: '1rem',
      base: '1.25rem',
      lg: '1.125rem',
      xl: '1.25rem',
      '2xl': '1.5rem',
      '3xl': '1.875rem',
      '4xl': '2.25rem',
      '5xl': '3rem',
      '6xl': '4rem',
    },
    extend: {
      colors: {
        brain: {
          400: '#FFE2E1',
          500: '#F8BAC7',
          600: '#F499AF',
          700: '#F1839F',
          900: '#863A68',
        },
        danger: colors.red,
        primary: colors.blue,
        success: colors.green,
        warning: colors.yellow,
      },
      boxShadow: {
        button: '0 0.25rem 0 rgba(0, 0, 0, 0.3)',
      }
    },
  },
  plugins: [
      require('@tailwindcss/forms'),
      require('@tailwindcss/typography'),
  ],
  darkMode: 'class'
}

