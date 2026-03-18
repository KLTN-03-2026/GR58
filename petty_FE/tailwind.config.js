/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        nunito: ['"Nunito"', 'sans-serif'],
        nunitoSans: ['"Nunito Sans"', 'sans-serif'],
        montserrat: ['"Montserrat Alternates"', 'sans-serif'],
        sans: ['"Nunito Sans"', 'sans-serif'],
      },
      colors: {
        primary: {
          500: '#5a9690',
          600: '#4a7f79', // hover
        },
        text: {
          dark: '#432323',
          medium: '#393e46',
          light: '#222831',
        },
        teal: {
          50: '#f0fdfa',
          100: '#cbfbf1',
          300: '#5eead4',
          400: '#2dd4bf',
          500: '#14b8a6',
          600: '#0d9488',
          700: '#00786f',
          800: '#0b4f4a',
          900: '#009689',
          
          600: '#2f5755',
          700: '#5a9690',
        },

        gray: {
          50: '#f8fafc',
          200: '#eeeeee',
          300: '#e0d9d9',
          400: '#a1a1aa',
          500: '#4a5565',
        },
    },
    
  },
  plugins: [],
}
}
