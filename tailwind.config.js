const colors = require('tailwindcss/colors')

const vatsimIndigo = {
  DEFAULT: '#2B3990',
  '50': '#949EDF',
  '100': '#8490DA',
  '200': '#6574D1',
  '300': '#4557C7',
  '400': '#3445AF',
  '500': '#2B3990',
  '600': '#1E2865',
  '700': '#11173A',
  '800': '#04060E',
  '900': '#000000'
}

const vatsimGreen = {
  DEFAULT: '#29B473',
  '50': '#A9EBCC',
  '100': '#99E8C3',
  '200': '#77E0AF',
  '300': '#56D99C',
  '400': '#35D188',
  '500': '#29B473',
  '600': '#1F8656',
  '700': '#145939',
  '800': '#0A2B1B',
  '900': '#000000'
}

const vatsimBlue = {
  DEFAULT: '#2483C5',
  '50': '#B0D6F1',
  '100': '#9FCDED',
  '200': '#7CBBE7',
  '300': '#5AA9E1',
  '400': '#3798DB',
  '500': '#2483C5',
  '600': '#1B6396',
  '700': '#134466',
  '800': '#0A2437',
  '900': '#010507'
}

module.exports = {
  content: [
    './resources/**/*.blade.php',
    './vendor/filament/**/*.blade.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        'vatsim-indigo': vatsimIndigo,
        'vatsim-blue': vatsimBlue,
        'vatsim-green': vatsimGreen,
        danger: colors.rose,
        primary: vatsimGreen,
        success: colors.green,
        warning: colors.amber,
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}