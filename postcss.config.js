module.exports = {
  plugins: [
    require('tailwindcss'),
    require('autoprefixer'),
    // Add plugins for custom at-rules if needed
    require('@tailwindcss/forms'), // Example plugin for forms
    require('@tailwindcss/typography'), // Example plugin for typography
    require('@tailwindcss/aspect-ratio'), // Example plugin for aspect ratio
  ],
};
