/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",
    "./public/**/*.php",
    "./public/assets/js/**/*.js",
    "./node_modules/preline/dist/*.js"
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        brand: {
          50: '#eff6ff',
          100: '#dbeafe',
          200: '#bfdbfe',
          500: '#1f6bf0',
          600: '#1553c7',
          700: '#0f3fa0',
        }
      }
    },
  },
  plugins: [
    require('preline/plugin'),
  ],
  corePlugins: {
    preflight: false,
  }
};
