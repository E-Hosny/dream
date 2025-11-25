/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        'cairo': ['Cairo', 'sans-serif'],
      },
      colors: {
        'brand': {
          DEFAULT: '#18b596',
          'dark': '#16a085',
          'light': '#1dd1a1',
        },
      },
    },
  },
  plugins: [
    require('tailwindcss-rtl'),
  ],
}
