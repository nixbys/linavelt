import initGrapeJS from './grapesjs-editor.js';
import '../css/grapesjs-dark-theme.css';

// Initialize the editor when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const editorContainer = document.getElementById('gjs');
    
    if (editorContainer) {
        const editor = initGrapeJS('gjs', {
            height: '100vh',
            width: 'auto',
            // Additional custom options can be passed here
        });

        // Log editor initialization
        console.log('GrapeJS Editor initialized with FluxUI dark theme');
        
        // Optional: Add custom commands or event listeners
        editor.on('load', () => {
            console.log('Editor loaded successfully');
        });

        // Make editor globally accessible for debugging
        if (typeof window !== 'undefined') {
            window.grapesjsEditor = editor;
        }
    }
});
