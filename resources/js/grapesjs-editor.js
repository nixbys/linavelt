import grapesjs from 'grapesjs';
import 'grapesjs/dist/css/grapes.min.css';
import presetWebpage from 'grapesjs-preset-webpage';

/**
 * Initialize GrapeJS editor with FluxUI dark theme
 * @param {string} containerId - The ID of the container element
 * @param {object} options - Additional GrapeJS options
 * @returns {object} GrapeJS editor instance
 */
export function initGrapeJS(containerId = 'gjs', options = {}) {
    const editor = grapesjs.init({
        container: `#${containerId}`,
        fromElement: true,
        height: '100vh',
        width: 'auto',
        storageManager: {
            type: 'local',
            autosave: true,
            autoload: true,
            stepsBeforeSave: 1,
        },
        plugins: [presetWebpage],
        pluginsOpts: {
            [presetWebpage]: {
                blocks: ['column1', 'column2', 'column3', 'text', 'link', 'image', 'video'],
                modalImportTitle: 'Import Template',
                modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
                modalImportContent: function(editor) {
                    return editor.getHtml() + '<style>' + editor.getCss() + '</style>';
                },
            },
        },
        canvas: {
            styles: [
                'https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css',
            ],
        },
        styleManager: {
            sectors: [
                {
                    name: 'General',
                    open: true,
                    buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
                },
                {
                    name: 'Flex',
                    open: false,
                    buildProps: ['flex-direction', 'flex-wrap', 'justify-content', 'align-items', 'align-content', 'order', 'flex-basis', 'flex-grow', 'flex-shrink', 'align-self'],
                },
                {
                    name: 'Dimension',
                    open: false,
                    buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                },
                {
                    name: 'Typography',
                    open: false,
                    buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-shadow'],
                },
                {
                    name: 'Decorations',
                    open: false,
                    buildProps: ['border-radius', 'border', 'background-color', 'background', 'box-shadow', 'opacity'],
                },
                {
                    name: 'Extra',
                    open: false,
                    buildProps: ['transition', 'perspective', 'transform'],
                },
            ],
        },
        // Apply dark theme colors based on FluxUI and app.css
        ...getFluxDarkTheme(),
        ...options,
    });

    // Add custom FluxUI-inspired blocks
    addFluxUIBlocks(editor);

    return editor;
}

/**
 * Get FluxUI-inspired dark theme configuration
 * Based on app.css and FluxUI design system
 */
function getFluxDarkTheme() {
    return {
        // Dark theme colors matching app.css
        colorPicker: {
            appendTo: 'parent',
            offset: { top: 26, left: -166 },
        },
        // Custom theme CSS for GrapeJS interface
        baseCss: `
            * {
                box-sizing: border-box;
            }
            html, body {
                height: 100%;
                margin: 0;
                font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            }
            body {
                background-color: #171717;
                color: #f5f5f5;
            }
            
            /* FluxUI-inspired component styles */
            .flux-card {
                background: #262626;
                border: 1px solid #404040;
                border-radius: 0.75rem;
                padding: 1.5rem;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
            }
            
            .flux-button {
                background: #f5f5f5;
                color: #171717;
                padding: 0.5rem 1rem;
                border-radius: 0.5rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.2s;
            }
            
            .flux-button:hover {
                background: #e5e5e5;
                transform: translateY(-1px);
            }
            
            .flux-input {
                background: #262626;
                border: 1px solid #404040;
                color: #f5f5f5;
                padding: 0.5rem 0.75rem;
                border-radius: 0.5rem;
                width: 100%;
                transition: all 0.2s;
            }
            
            .flux-input:focus {
                outline: none;
                border-color: #737373;
                box-shadow: 0 0 0 3px rgba(115, 115, 115, 0.1);
            }
        `,
    };
}

/**
 * Add custom FluxUI-inspired blocks to the editor
 */
function addFluxUIBlocks(editor) {
    const blockManager = editor.BlockManager;

    // FluxUI Card Block
    blockManager.add('flux-card', {
        label: 'Flux Card',
        category: 'FluxUI',
        content: `
            <div class="flux-card">
                <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">Card Title</h3>
                <p style="color: #a3a3a3;">Card description goes here. This is a FluxUI-inspired card component.</p>
            </div>
        `,
        attributes: { class: 'fa fa-square-o' },
    });

    // FluxUI Button Block
    blockManager.add('flux-button', {
        label: 'Flux Button',
        category: 'FluxUI',
        content: '<button class="flux-button">Click Me</button>',
        attributes: { class: 'fa fa-square' },
    });

    // FluxUI Input Block
    blockManager.add('flux-input', {
        label: 'Flux Input',
        category: 'FluxUI',
        content: '<input type="text" class="flux-input" placeholder="Enter text..." />',
        attributes: { class: 'fa fa-text-width' },
    });

    // FluxUI Hero Section
    blockManager.add('flux-hero', {
        label: 'Flux Hero',
        category: 'FluxUI',
        content: `
            <section style="background: linear-gradient(to bottom, #262626, #171717); padding: 4rem 2rem; text-align: center;">
                <h1 style="font-size: 3rem; font-weight: 700; margin-bottom: 1rem; background: linear-gradient(to right, #f5f5f5, #a3a3a3); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                    Welcome to FluxUI
                </h1>
                <p style="font-size: 1.25rem; color: #a3a3a3; margin-bottom: 2rem;">
                    Build beautiful dark-themed interfaces with ease
                </p>
                <button class="flux-button">Get Started</button>
            </section>
        `,
        attributes: { class: 'fa fa-window-maximize' },
    });

    // FluxUI Feature Grid
    blockManager.add('flux-features', {
        label: 'Flux Features',
        category: 'FluxUI',
        content: `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; padding: 2rem;">
                <div class="flux-card">
                    <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Feature One</h4>
                    <p style="color: #a3a3a3; font-size: 0.875rem;">Description of the first feature</p>
                </div>
                <div class="flux-card">
                    <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Feature Two</h4>
                    <p style="color: #a3a3a3; font-size: 0.875rem;">Description of the second feature</p>
                </div>
                <div class="flux-card">
                    <h4 style="font-weight: 600; margin-bottom: 0.5rem;">Feature Three</h4>
                    <p style="color: #a3a3a3; font-size: 0.875rem;">Description of the third feature</p>
                </div>
            </div>
        `,
        attributes: { class: 'fa fa-th' },
    });
}

export default initGrapeJS;
