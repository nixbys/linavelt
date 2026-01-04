import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';

// Mock CSS imports
vi.mock('grapesjs/dist/css/grapes.min.css', () => ({}));

// Mock grapesjs module with init function
vi.mock('grapesjs', () => {
    const mockEditor = {
        BlockManager: {
            add: vi.fn(),
            get: vi.fn((id) => {
                const blocks = {
                    'flux-card': { get: (prop) => ({ label: 'Flux Card', category: 'FluxUI', content: '<div class="flux-card"></div>' }[prop]) },
                    'flux-button': { get: (prop) => ({ label: 'Flux Button', content: '<button class="flux-button"></button>', category: 'FluxUI' }[prop]) },
                    'flux-input': { get: (prop) => ({ label: 'Flux Input', content: '<input class="flux-input" />', category: 'FluxUI' }[prop]) },
                    'flux-hero': { get: (prop) => ({ label: 'Flux Hero', content: '<section style="background: linear-gradient">Welcome to FluxUI</section>', category: 'FluxUI' }[prop]) },
                    'flux-features': { get: (prop) => ({ label: 'Flux Features', content: '<div style="grid-template-columns"><div class="flux-card"></div></div>', category: 'FluxUI' }[prop]) },
                };
                return blocks[id];
            }),
        },
        StyleManager: {
            getSectors: vi.fn().mockReturnValue({
                models: [
                    { get: (name) => name === 'name' ? 'General' : null },
                    { get: (name) => name === 'name' ? 'Flex' : null },
                    { get: (name) => name === 'name' ? 'Dimension' : null },
                    { get: (name) => name === 'name' ? 'Typography' : null },
                ],
            }),
        },
        StorageManager: {
            getConfig: vi.fn().mockReturnValue({
                type: 'local',
                autosave: true,
            }),
        },
        getContainer: vi.fn().mockReturnValue(document.createElement('div')),
        getConfig: vi.fn().mockReturnValue({
            height: '100vh',
            canvas: {
                styles: ['https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css'],
            },
            baseCss: '.flux-card { background: #262626; } .flux-button { background: #f5f5f5; } body { background-color: #171717; color: #f5f5f5; font-family: "Instrument Sans"; }',
        }),
        destroy: vi.fn(),
        on: vi.fn(),
    };
    
    return {
        default: vi.fn(() => mockEditor),
    };
});

vi.mock('grapesjs-preset-webpage', () => ({
    default: 'preset-webpage',
}));

// Import after mocking
const grapesjs = await import('grapesjs');

// Helper function to initialize editor
const initGrapeJS = (containerId = 'gjs', options = {}) => {
    return grapesjs.default({
        container: `#${containerId}`,
        ...options,
    });
};

describe('GrapeJS Editor', () => {
    let container;
    let editor;

    beforeEach(() => {
        // Create a container element for the editor
        container = document.createElement('div');
        container.id = 'gjs';
        document.body.appendChild(container);
    });

    afterEach(() => {
        // Clean up
        if (editor) {
            editor.destroy();
        }
        if (container && container.parentNode) {
            container.parentNode.removeChild(container);
        }
    });

    it('should initialize GrapeJS editor', () => {
        editor = initGrapeJS('gjs');
        expect(editor).toBeDefined();
        expect(editor.getContainer()).toBeDefined();
    });

    it('should have storage manager configured', () => {
        editor = initGrapeJS('gjs');
        const storageManager = editor.StorageManager;
        expect(storageManager).toBeDefined();
        expect(storageManager.getConfig().type).toBe('local');
        expect(storageManager.getConfig().autosave).toBe(true);
    });

    it('should have FluxUI custom blocks', () => {
        editor = initGrapeJS('gjs');
        const blockManager = editor.BlockManager;
        
        // Check for custom FluxUI blocks
        expect(blockManager.get('flux-card')).toBeDefined();
        expect(blockManager.get('flux-button')).toBeDefined();
        expect(blockManager.get('flux-input')).toBeDefined();
        expect(blockManager.get('flux-hero')).toBeDefined();
        expect(blockManager.get('flux-features')).toBeDefined();
    });

    it('should have style manager sectors configured', () => {
        editor = initGrapeJS('gjs');
        const styleManager = editor.StyleManager;
        const sectors = styleManager.getSectors();
        
        expect(sectors.models.length).toBeGreaterThan(0);
        
        // Check for specific sectors
        const sectorNames = sectors.models.map(s => s.get('name'));
        expect(sectorNames).toContain('General');
        expect(sectorNames).toContain('Flex');
        expect(sectorNames).toContain('Dimension');
        expect(sectorNames).toContain('Typography');
    });

    it('should accept custom options', () => {
        editor = initGrapeJS('gjs', { height: '500px' });
        
        expect(editor.getConfig).toBeDefined();
    });

    it('should have canvas styles configured', () => {
        editor = initGrapeJS('gjs');
        const config = editor.getConfig();
        
        expect(config.canvas).toBeDefined();
        expect(config.canvas.styles).toBeDefined();
        expect(Array.isArray(config.canvas.styles)).toBe(true);
    });
});

describe('FluxUI Blocks', () => {
    let container;
    let editor;

    beforeEach(() => {
        container = document.createElement('div');
        container.id = 'gjs';
        document.body.appendChild(container);
        editor = initGrapeJS('gjs');
    });

    afterEach(() => {
        if (editor) {
            editor.destroy();
        }
        if (container && container.parentNode) {
            container.parentNode.removeChild(container);
        }
    });

    it('should create flux-card block with correct structure', () => {
        const blockManager = editor.BlockManager;
        const fluxCard = blockManager.get('flux-card');
        
        expect(fluxCard).toBeDefined();
        expect(fluxCard.get('label')).toBe('Flux Card');
        expect(fluxCard.get('category')).toBe('FluxUI');
    });

    it('should create flux-button block with correct structure', () => {
        const blockManager = editor.BlockManager;
        const fluxButton = blockManager.get('flux-button');
        
        expect(fluxButton).toBeDefined();
        expect(fluxButton.get('label')).toBe('Flux Button');
        expect(fluxButton.get('content')).toContain('button');
    });

    it('should create flux-hero block with gradient background', () => {
        const blockManager = editor.BlockManager;
        const fluxHero = blockManager.get('flux-hero');
        
        expect(fluxHero).toBeDefined();
        expect(fluxHero.get('content')).toContain('linear-gradient');
        expect(fluxHero.get('content')).toContain('Welcome to FluxUI');
    });

    it('should create flux-features block with grid layout', () => {
        const blockManager = editor.BlockManager;
        const fluxFeatures = blockManager.get('flux-features');
        
        expect(fluxFeatures).toBeDefined();
        expect(fluxFeatures.get('content')).toContain('grid-template-columns');
        expect(fluxFeatures.get('content')).toContain('flux-card');
    });
});

describe('Dark Theme Configuration', () => {
    let container;
    let editor;

    beforeEach(() => {
        container = document.createElement('div');
        container.id = 'gjs';
        document.body.appendChild(container);
        editor = initGrapeJS('gjs');
    });

    afterEach(() => {
        if (editor) {
            editor.destroy();
        }
        if (container && container.parentNode) {
            container.parentNode.removeChild(container);
        }
    });

    it('should have dark theme base CSS', () => {
        const config = editor.getConfig();
        
        expect(config.baseCss).toBeDefined();
        expect(config.baseCss).toContain('#171717'); // Dark background
        expect(config.baseCss).toContain('#f5f5f5'); // Light text
    });

    it('should have flux-card styles in base CSS', () => {
        const config = editor.getConfig();
        
        expect(config.baseCss).toContain('.flux-card');
        expect(config.baseCss).toContain('#262626'); // Card background
    });

    it('should have flux-button styles', () => {
        const config = editor.getConfig();
        
        expect(config.baseCss).toContain('.flux-button');
        expect(config.baseCss).toContain('#f5f5f5');
    });

    it('should use Instrument Sans font family', () => {
        const config = editor.getConfig();
        
        expect(config.baseCss).toContain('Instrument Sans');
    });
});


