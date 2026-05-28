module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  safelist: [
    { pattern: /btn-poll-(edit|delete)/ },
  ],
  theme: {
    extend: {},
  },
  plugins: [],
};
