import 'grapesjs/dist/css/grapes.min.css';
import grapesjs from 'grapesjs';
import { registerFluxBlocks } from './blocks.js';

let editor = null;

function initEditor() {
    const container = document.getElementById('gjs');
    if (!container) return;

    const designId  = container.dataset.designId  || null;
    const projectId = container.dataset.projectId || null;
    const saveUrl   = container.dataset.saveUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    editor = grapesjs.init({
        container: '#gjs',
        height: '100%',
        width: 'auto',
        fromElement: false,
        storageManager: false,
        undoManager: { trackSelection: false },

        panels: { defaults: [] },

        blockManager: {
            appendTo: '#gjs-blocks',
            blocks: [],
        },

        layerManager: {
            appendTo: '#gjs-layers',
        },

        styleManager: {
            appendTo: '#gjs-styles',
            sectors: [
                {
                    name: 'Dimension',
                    open: false,
                    properties: [
                        { property: 'width', type: 'integer', units: ['px', '%', 'vw', 'rem'] },
                        { property: 'height', type: 'integer', units: ['px', '%', 'vh', 'rem'] },
                        { property: 'max-width', type: 'integer', units: ['px', '%', 'rem'] },
                        { property: 'min-height', type: 'integer', units: ['px', 'rem'] },
                        {
                            property: 'padding', type: 'composite',
                            properties: [
                                { property: 'padding-top', type: 'integer', units: ['px', 'rem', '%'] },
                                { property: 'padding-right', type: 'integer', units: ['px', 'rem', '%'] },
                                { property: 'padding-bottom', type: 'integer', units: ['px', 'rem', '%'] },
                                { property: 'padding-left', type: 'integer', units: ['px', 'rem', '%'] },
                            ],
                        },
                        {
                            property: 'margin', type: 'composite',
                            properties: [
                                { property: 'margin-top', type: 'integer', units: ['px', 'rem', '%'] },
                                { property: 'margin-right', type: 'integer', units: ['px', 'rem', 'auto'] },
                                { property: 'margin-bottom', type: 'integer', units: ['px', 'rem', '%'] },
                                { property: 'margin-left', type: 'integer', units: ['px', 'rem', 'auto'] },
                            ],
                        },
                    ],
                },
                {
                    name: 'Typography',
                    open: false,
                    properties: [
                        {
                            property: 'font-family', type: 'select',
                            options: [
                                { id: 'inherit', label: 'Inherit' },
                                { id: 'ui-sans-serif, system-ui, sans-serif', label: 'System UI' },
                                { id: 'ui-serif, Georgia, serif', label: 'Serif' },
                                { id: 'ui-monospace, monospace', label: 'Monospace' },
                                { id: 'Inter, sans-serif', label: 'Inter' },
                            ],
                        },
                        { property: 'font-size', type: 'integer', units: ['px', 'rem', 'em', '%'] },
                        {
                            property: 'font-weight', type: 'select',
                            options: [
                                { id: '300', label: 'Light' },
                                { id: '400', label: 'Regular' },
                                { id: '500', label: 'Medium' },
                                { id: '600', label: 'Semibold' },
                                { id: '700', label: 'Bold' },
                            ],
                        },
                        { property: 'color', type: 'color' },
                        {
                            property: 'text-align', type: 'radio',
                            options: [
                                { id: 'left', label: 'L' },
                                { id: 'center', label: 'C' },
                                { id: 'right', label: 'R' },
                            ],
                        },
                        { property: 'line-height', type: 'integer', units: ['', 'px', 'em'] },
                        { property: 'letter-spacing', type: 'integer', units: ['px', 'em', 'rem'] },
                        {
                            property: 'text-transform', type: 'select',
                            options: [
                                { id: 'none', label: 'None' },
                                { id: 'uppercase', label: 'Uppercase' },
                                { id: 'lowercase', label: 'Lowercase' },
                                { id: 'capitalize', label: 'Capitalize' },
                            ],
                        },
                    ],
                },
                {
                    name: 'Decorations',
                    open: false,
                    properties: [
                        { property: 'background-color', type: 'color' },
                        { property: 'border-radius', type: 'integer', units: ['px', 'rem', '%'] },
                        {
                            property: 'border', type: 'composite',
                            properties: [
                                { property: 'border-width', type: 'integer', units: ['px'] },
                                {
                                    property: 'border-style', type: 'select',
                                    options: [
                                        { id: 'none', label: 'None' },
                                        { id: 'solid', label: 'Solid' },
                                        { id: 'dashed', label: 'Dashed' },
                                        { id: 'dotted', label: 'Dotted' },
                                    ],
                                },
                                { property: 'border-color', type: 'color' },
                            ],
                        },
                        { property: 'opacity', type: 'slider', min: 0, max: 1, step: 0.01 },
                        { property: 'box-shadow', label: 'Box Shadow' },
                    ],
                },
                {
                    name: 'Flex',
                    open: false,
                    properties: [
                        {
                            property: 'display', type: 'select',
                            options: [
                                { id: 'block', label: 'Block' },
                                { id: 'flex', label: 'Flex' },
                                { id: 'grid', label: 'Grid' },
                                { id: 'inline-block', label: 'Inline Block' },
                                { id: 'none', label: 'Hidden' },
                            ],
                        },
                        {
                            property: 'flex-direction', type: 'radio',
                            options: [{ id: 'row', label: 'Row' }, { id: 'column', label: 'Col' }],
                        },
                        {
                            property: 'justify-content', type: 'select',
                            options: [
                                { id: 'flex-start', label: 'Start' },
                                { id: 'center', label: 'Center' },
                                { id: 'flex-end', label: 'End' },
                                { id: 'space-between', label: 'Space Between' },
                                { id: 'space-around', label: 'Space Around' },
                            ],
                        },
                        {
                            property: 'align-items', type: 'select',
                            options: [
                                { id: 'flex-start', label: 'Start' },
                                { id: 'center', label: 'Center' },
                                { id: 'flex-end', label: 'End' },
                                { id: 'stretch', label: 'Stretch' },
                            ],
                        },
                        { property: 'gap', type: 'integer', units: ['px', 'rem'] },
                        {
                            property: 'flex-wrap', type: 'radio',
                            options: [{ id: 'nowrap', label: 'No Wrap' }, { id: 'wrap', label: 'Wrap' }],
                        },
                    ],
                },
            ],
        },

        traitManager: {
            appendTo: '#gjs-traits',
        },

        deviceManager: {
            devices: [
                { id: 'desktop', name: 'Desktop', width: '' },
                { id: 'tablet', name: 'Tablet', width: '768px', widthMedia: '992px' },
                { id: 'mobile', name: 'Mobile', width: '375px', widthMedia: '480px' },
            ],
        },

        canvas: {
            styles: [
                'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
            ],
            scripts: [
                'https://cdn.jsdelivr.net/npm/@tailwindcss/cdn@4/dist/index.min.js',
            ],
        },
    });

    registerFluxBlocks(editor);

    const ctx = { saveUrl, designId, csrfToken };
    wireToolbar(editor, ctx);
    wirePanelTabs('left-tab', 'left-panel');
    wirePanelTabs('right-tab', 'right-panel');

    const idToLoad = projectId
        ? { type: 'project', id: projectId }
        : designId
            ? { type: 'design', id: designId }
            : null;

    if (idToLoad) loadContent(editor, idToLoad, csrfToken);
}

/* ── Toolbar wiring ─────────────────────────────────────────────── */
function wireToolbar(editor, { saveUrl, csrfToken }) {
    on('btn-undo', 'click', () => editor.Commands.run('core:undo'));
    on('btn-redo', 'click', () => editor.Commands.run('core:redo'));

    on('btn-desktop', 'click', () => { editor.setDevice('desktop'); setActiveViewport('btn-desktop'); });
    on('btn-tablet',  'click', () => { editor.setDevice('tablet');  setActiveViewport('btn-tablet'); });
    on('btn-mobile',  'click', () => { editor.setDevice('mobile');  setActiveViewport('btn-mobile'); });

    on('btn-preview', 'click', () => editor.Commands.run('core:preview'));

    on('btn-clear', 'click', () => {
        if (confirm('Clear the canvas? This cannot be undone.')) {
            editor.Commands.run('core:canvas-clear');
        }
    });

    on('btn-save', 'click', () => saveContent(editor, saveUrl, csrfToken));

    // Live code panel updates
    const updateCode = () => {
        const htmlEl = document.getElementById('code-html');
        const cssEl  = document.getElementById('code-css');
        if (htmlEl) htmlEl.textContent = editor.getHtml();
        if (cssEl)  cssEl.textContent  = editor.getCss();
    };

    editor.on('change:changesCount', () => {
        updateCode();
        setSaveState('unsaved');
    });
    editor.on('component:selected', updateCode);
    editor.on('load', updateCode);

    // Debounced auto-save
    let saveTimer;
    editor.on('change:changesCount', () => {
        clearTimeout(saveTimer);
        saveTimer = setTimeout(() => saveContent(editor, saveUrl, csrfToken), 4000);
    });
}

function on(id, event, handler) {
    document.getElementById(id)?.addEventListener(event, handler);
}

/* ── Persistence ────────────────────────────────────────────────── */
function saveContent(editor, saveUrl, csrfToken) {
    const gjsEl    = document.getElementById('gjs');
    const projectId = gjsEl?.dataset.projectId || null;
    const designId  = gjsEl?.dataset.designId  || null;

    setSaveState('saving');

    const body = projectId
        ? { project_id: projectId, project_data: editor.getProjectData(), html: editor.getHtml(), css: editor.getCss() }
        : { design_id: designId,  project_data: editor.getProjectData(), html: editor.getHtml(), css: editor.getCss() };

    fetch(saveUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
    })
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            setSaveState('saved');
            if (data.design_id && !designId && gjsEl) {
                gjsEl.dataset.designId = data.design_id;
                const url = new URL(window.location.href);
                url.pathname = url.pathname.replace(/\/create$/, `/${data.design_id}/edit`);
                window.history.replaceState({}, '', url);
            }
        })
        .catch(() => setSaveState('error'));
}

async function loadContent(editor, { type, id }, csrfToken) {
    const url = type === 'project'
        ? `/projects/${id}/data`
        : `/builder/designs/${id}/data`;
    try {
        const resp = await fetch(url, {
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        });
        if (!resp.ok) return;
        const { project_data } = await resp.json();
        if (project_data) editor.loadProjectData(project_data);
    } catch {
        // start with empty canvas
    }
}

/* ── UI helpers ─────────────────────────────────────────────────── */
function setSaveState(state) {
    const el = document.getElementById('save-state');
    if (!el) return;
    const map = {
        saving:  { text: 'Saving…', cls: 'text-zinc-400' },
        saved:   { text: 'Saved',   cls: 'text-emerald-400' },
        unsaved: { text: 'Unsaved', cls: 'text-amber-400' },
        error:   { text: 'Error — try again', cls: 'text-rose-400' },
    };
    const s = map[state] ?? map.unsaved;
    el.textContent = s.text;
    el.className = `text-xs font-medium transition-colors ${s.cls}`;
}

function setActiveViewport(activeId) {
    ['btn-desktop', 'btn-tablet', 'btn-mobile'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const isActive = id === activeId;
        el.setAttribute('data-active', String(isActive));
        el.classList.toggle('bg-zinc-700', isActive);
        el.classList.toggle('text-zinc-100', isActive);
        el.classList.toggle('text-zinc-400', !isActive);
    });
}

function wirePanelTabs(tabAttr, panelAttr) {
    const tabs   = document.querySelectorAll(`[data-${tabAttr}]`);
    const panels = document.querySelectorAll(`[data-${panelAttr}]`);

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.dataset[camelCase(tabAttr)];
            tabs.forEach(t => {
                t.setAttribute('aria-selected', 'false');
                t.classList.remove('border-b-2', 'border-zinc-400', 'text-zinc-100');
                t.classList.add('text-zinc-500');
            });
            panels.forEach(p => p.classList.add('hidden'));

            tab.setAttribute('aria-selected', 'true');
            tab.classList.add('border-b-2', 'border-zinc-400', 'text-zinc-100');
            tab.classList.remove('text-zinc-500');
            document.querySelector(`[data-${panelAttr}="${target}"]`)?.classList.remove('hidden');
        });
    });
}

function camelCase(str) {
    return str.replace(/-([a-z])/g, (_, c) => c.toUpperCase());
}

document.addEventListener('DOMContentLoaded', initEditor);
