# GrapeJS Integration with FluxUI Dark Theme

This project includes a fully integrated GrapeJS page builder with a custom dark theme inspired by FluxUI design system.

## Features

### 🎨 FluxUI Dark Theme
- Custom dark color scheme matching the application's design
- Based on the color palette from `app.css`
- Inspired by the modern design of [FluxUI](https://fluxui.dev/)
- Uses Instrument Sans font family for consistency

### 🧱 Custom FluxUI Components
The editor includes several pre-built FluxUI-inspired components:

1. **Flux Card** - Dark-themed card component with shadows
2. **Flux Button** - Stylish button with hover effects
3. **Flux Input** - Form input with focus states
4. **Flux Hero** - Hero section with gradient background
5. **Flux Features** - Responsive feature grid layout

### 🛠️ Editor Features
- Drag-and-drop interface
- Visual component editing
- Responsive design tools
- Local storage for auto-save
- Custom style manager with dark theme
- Preset webpage plugin included

## Installation

Install dependencies:

```bash
npm install
```

This will install:
- `grapesjs` - Core page builder
- `grapesjs-preset-webpage` - Webpage building blocks
- `vitest` - Testing framework
- `jsdom` - DOM testing environment

## Usage

### Accessing the Editor

Visit the editor page in your Laravel application:
```
/editor
```

### Programmatic Usage

```javascript
import initGrapeJS from './resources/js/grapesjs-editor.js';

// Initialize with default settings
const editor = initGrapeJS('container-id');

// Initialize with custom options
const editor = initGrapeJS('container-id', {
    height: '800px',
    width: 'auto',
    // ... other GrapeJS options
});
```

### Using FluxUI Components

1. Open the editor
2. Click on the "FluxUI" category in the blocks panel
3. Drag and drop components onto the canvas
4. Customize using the style manager

## Testing

Run tests:
```bash
npm test
```

Run tests with UI:
```bash
npm run test:ui
```

Generate coverage report:
```bash
npm run test:coverage
```

### Test Structure

Tests are organized in `/tests/javascript/`:
- `grapesjs-editor.test.js` - Main editor tests
- `setup.js` - Test configuration

## Dark Theme Customization

The dark theme is defined in:
- `/resources/css/grapesjs-dark-theme.css` - GrapeJS interface styling
- `/resources/js/grapesjs-editor.js` - Component styles and editor config

### Color Palette (from app.css)

```css
--color-zinc-50: #fafafa;   /* Lightest */
--color-zinc-100: #f5f5f5;  /* Very light */
--color-zinc-200: #e5e5e5;
--color-zinc-300: #d4d4d4;
--color-zinc-400: #a3a3a3;  /* Medium gray */
--color-zinc-500: #737373;
--color-zinc-600: #525252;
--color-zinc-700: #404040;  /* Dark gray */
--color-zinc-800: #262626;  /* Very dark */
--color-zinc-900: #171717;  /* Almost black */
--color-zinc-950: #0a0a0a;  /* Darkest */
```

### Customizing Components

Edit `/resources/js/grapesjs-editor.js` to modify or add components:

```javascript
blockManager.add('custom-block', {
    label: 'Custom Block',
    category: 'FluxUI',
    content: '<div class="custom-class">Content</div>',
    attributes: { class: 'fa fa-icon' },
});
```

## File Structure

```
resources/
├── css/
│   ├── app.css                    # Main app styles
│   └── grapesjs-dark-theme.css   # GrapeJS dark theme
├── js/
│   ├── grapesjs-editor.js        # Main editor module
│   └── editor-app.js             # Editor initialization
└── views/
    └── editor.blade.php          # Editor page template

tests/
└── javascript/
    ├── setup.js                  # Test setup
    └── grapesjs-editor.test.js  # Editor tests

vitest.config.js                  # Testing configuration
```

## Integration with Laravel

### Routes

Add to your `routes/web.php`:

```php
Route::get('/editor', function () {
    return view('editor');
})->name('editor');
```

### Middleware

Add authentication if needed:

```php
Route::get('/editor', function () {
    return view('editor');
})->middleware(['auth'])->name('editor');
```

## Browser Support

- Modern browsers with ES6+ support
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

## Contributing

When adding new components or modifying the theme:

1. Update tests in `/tests/javascript/grapesjs-editor.test.js`
2. Run tests to ensure nothing breaks: `npm test`
3. Update this README if needed
4. Follow the existing dark theme color palette

## License

This integration follows the same license as the main Laravel application.

## Resources

- [GrapeJS Documentation](https://grapesjs.com/docs/)
- [FluxUI Website](https://fluxui.dev/)
- [Vitest Documentation](https://vitest.dev/)
