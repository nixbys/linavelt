const icon = (path) =>
    `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="${path}"/></svg>`;

const featureTile = () => `
<div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6">
    <div class="mb-4 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-zinc-800">
        <svg class="h-5 w-5 text-zinc-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"/>
        </svg>
    </div>
    <h3 class="text-base font-semibold text-zinc-100">Feature Title</h3>
    <p class="mt-2 text-sm text-zinc-400 leading-relaxed">Feature description explaining the value of this capability.</p>
</div>`;

const statTile = (num, label) => `
<div class="rounded-xl border border-zinc-700 bg-zinc-900 py-8 px-4 text-center">
    <p class="text-4xl font-bold text-zinc-100">${num}</p>
    <p class="mt-2 text-sm text-zinc-400">${label}</p>
</div>`;

export function registerFluxBlocks(editor) {
    const bm = editor.BlockManager;
    bm.getAll().reset();

    /* ── Layout ─────────────────────────────────────────────────── */
    bm.add('section', {
        label: 'Section',
        category: 'Layout',
        media: icon('M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 01-1.125-1.125v-3.75zM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-8.25zM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 01-1.125-1.125v-2.25z'),
        content: {
            tagName: 'section',
            attributes: { class: 'py-16 px-6' },
            components: [{ tagName: 'div', attributes: { class: 'mx-auto max-w-6xl' } }],
        },
    });

    bm.add('container', {
        label: 'Container',
        category: 'Layout',
        media: icon('M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'),
        content: `<div class="mx-auto max-w-5xl px-6 py-8"></div>`,
    });

    bm.add('columns-2', {
        label: '2 Cols',
        category: 'Layout',
        media: icon('M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125z'),
        content: `<div class="grid grid-cols-2 gap-6 px-6 py-8">
    <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-6 min-h-24"></div>
    <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-6 min-h-24"></div>
</div>`,
    });

    bm.add('columns-3', {
        label: '3 Cols',
        category: 'Layout',
        media: icon('M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z'),
        content: `<div class="grid grid-cols-3 gap-6 px-6 py-8">
    <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-6 min-h-24"></div>
    <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-6 min-h-24"></div>
    <div class="rounded-xl border border-zinc-700 bg-zinc-800/50 p-6 min-h-24"></div>
</div>`,
    });

    /* ── Typography ─────────────────────────────────────────────── */
    bm.add('heading-1', {
        label: 'Heading 1',
        category: 'Typography',
        media: icon('M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5'),
        content: `<h1 class="text-5xl font-semibold tracking-tight text-zinc-100">Page Heading</h1>`,
    });

    bm.add('heading-2', {
        label: 'Heading 2',
        category: 'Typography',
        media: icon('M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12'),
        content: `<h2 class="text-3xl font-semibold tracking-tight text-zinc-100">Section Heading</h2>`,
    });

    bm.add('heading-3', {
        label: 'Heading 3',
        category: 'Typography',
        media: icon('M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h7.5'),
        content: `<h3 class="text-xl font-semibold text-zinc-200">Subsection Heading</h3>`,
    });

    bm.add('paragraph', {
        label: 'Paragraph',
        category: 'Typography',
        media: icon('M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12'),
        content: `<p class="text-base leading-relaxed text-zinc-300">Enter your text content here. Click to edit this paragraph and replace it with your own copy.</p>`,
    });

    bm.add('label', {
        label: 'Label',
        category: 'Typography',
        media: icon('M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z'),
        content: `<p class="text-xs font-semibold uppercase tracking-widest text-zinc-400">Label Text</p>`,
    });

    bm.add('link', {
        label: 'Link',
        category: 'Typography',
        media: icon('M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244'),
        content: `<a href="#" class="text-sm font-medium text-zinc-300 underline underline-offset-2 hover:text-zinc-100 transition-colors">Link text</a>`,
    });

    /* ── Flux UI ────────────────────────────────────────────────── */
    bm.add('flux-button', {
        label: 'Button',
        category: 'Flux UI',
        media: icon('M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5l-3.9 19.5m-2.1-19.5l-3.9 19.5'),
        content: `<button class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium bg-zinc-800 text-zinc-100 border border-zinc-700 hover:bg-zinc-700 transition-colors">Button Label</button>`,
    });

    bm.add('flux-button-primary', {
        label: 'Btn Primary',
        category: 'Flux UI',
        media: icon('M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z'),
        content: `<button class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium bg-white text-zinc-900 hover:bg-zinc-100 transition-colors">Primary Action</button>`,
    });

    bm.add('flux-card', {
        label: 'Card',
        category: 'Flux UI',
        media: icon('M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z'),
        content: `<div class="rounded-xl border border-zinc-700 bg-zinc-900 p-6 space-y-3">
    <h3 class="text-base font-semibold text-zinc-100">Card Title</h3>
    <p class="text-sm text-zinc-400 leading-relaxed">Card description text. Add your content inside this card component.</p>
    <button class="inline-flex items-center rounded-lg px-3 py-1.5 text-xs font-medium bg-zinc-800 text-zinc-100 border border-zinc-700 hover:bg-zinc-700 transition-colors">Action</button>
</div>`,
    });

    bm.add('flux-badge', {
        label: 'Badge',
        category: 'Flux UI',
        media: icon('M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z'),
        content: `<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-zinc-800 text-zinc-200 border border-zinc-700">Badge</span>`,
    });

    bm.add('flux-badge-success', {
        label: 'Badge OK',
        category: 'Flux UI',
        media: icon('M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'),
        content: `<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-900/40 text-emerald-300 border border-emerald-700/50">Published</span>`,
    });

    bm.add('flux-input', {
        label: 'Input',
        category: 'Flux UI',
        media: icon('M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125'),
        content: `<div class="space-y-1.5">
    <label class="block text-sm font-medium text-zinc-300">Label</label>
    <input type="text" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500" placeholder="Enter value…" />
</div>`,
    });

    bm.add('flux-textarea', {
        label: 'Textarea',
        category: 'Flux UI',
        media: icon('M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'),
        content: `<div class="space-y-1.5">
    <label class="block text-sm font-medium text-zinc-300">Message</label>
    <textarea rows="4" class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-zinc-100 placeholder-zinc-500 focus:outline-none focus:ring-1 focus:ring-zinc-500 resize-none" placeholder="Enter message…"></textarea>
</div>`,
    });

    bm.add('flux-select', {
        label: 'Select',
        category: 'Flux UI',
        media: icon('M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9'),
        content: `<div class="space-y-1.5">
    <label class="block text-sm font-medium text-zinc-300">Option</label>
    <select class="w-full rounded-lg border border-zinc-700 bg-zinc-800 px-3 py-2 text-sm text-zinc-100 focus:outline-none focus:ring-1 focus:ring-zinc-500">
        <option>Option 1</option>
        <option>Option 2</option>
        <option>Option 3</option>
    </select>
</div>`,
    });

    bm.add('flux-separator', {
        label: 'Separator',
        category: 'Flux UI',
        media: icon('M5 12h14'),
        content: `<hr class="border-zinc-700 my-6" />`,
    });

    bm.add('flux-navlist', {
        label: 'Nav List',
        category: 'Flux UI',
        media: icon('M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5'),
        content: `<nav class="space-y-1">
    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-100 bg-zinc-800">
        <svg class="h-4 w-4 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
        Dashboard
    </a>
    <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-zinc-400 hover:bg-zinc-800 hover:text-zinc-100 transition-colors">
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        Settings
    </a>
</nav>`,
    });

    /* ── Patterns ───────────────────────────────────────────────── */
    bm.add('hero', {
        label: 'Hero',
        category: 'Patterns',
        media: icon('M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v4.5c0 .621.504 1.125 1.125 1.125m7.5-5.625c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125m0 0h7.5m-7.5 0c-.621 0-1.125.504-1.125-1.125m8.625 0c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125m-1.5 5.625v-1.5a1.125 1.125 0 00-1.125-1.125h-1.5a1.125 1.125 0 00-1.125 1.125v1.5m3.75 0a1.125 1.125 0 01-1.125 1.125h-1.5a1.125 1.125 0 01-1.125-1.125m3.75 0v-1.5a1.125 1.125 0 00-1.125-1.125h-1.5a1.125 1.125 0 00-1.125 1.125v1.5'),
        content: `<section class="py-24 px-6 text-center bg-zinc-950">
    <p class="mb-4 text-xs font-semibold uppercase tracking-widest text-zinc-500">Platform</p>
    <h1 class="text-5xl font-semibold tracking-tight text-zinc-100">Build faster.<br/>Ship with confidence.</h1>
    <p class="mt-6 mx-auto max-w-2xl text-lg text-zinc-400 leading-relaxed">
        A unified stack for modern web experiences that scales with your team and gets out of your way.
    </p>
    <div class="mt-10 flex items-center justify-center gap-4">
        <button class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium bg-white text-zinc-900 hover:bg-zinc-100 transition-colors">Get started</button>
        <button class="inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium bg-zinc-800 text-zinc-100 border border-zinc-700 hover:bg-zinc-700 transition-colors">Learn more</button>
    </div>
</section>`,
    });

    bm.add('feature-grid', {
        label: 'Features',
        category: 'Patterns',
        media: icon('M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15'),
        content: `<section class="py-16 px-6 bg-zinc-950">
    <div class="mx-auto max-w-6xl">
        <h2 class="text-3xl font-semibold text-zinc-100 text-center mb-12">Features</h2>
        <div class="grid grid-cols-3 gap-6">
            ${featureTile()}${featureTile()}${featureTile()}
        </div>
    </div>
</section>`,
    });

    bm.add('cta', {
        label: 'CTA Banner',
        category: 'Patterns',
        media: icon('M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 001.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 010 3.46'),
        content: `<section class="py-12 px-6 bg-zinc-950">
    <div class="mx-auto max-w-4xl rounded-2xl border border-zinc-700 bg-zinc-900 p-8 flex items-center justify-between gap-6">
        <div>
            <h3 class="text-xl font-semibold text-zinc-100">Ready to get started?</h3>
            <p class="mt-2 text-sm text-zinc-400">Join thousands of teams building with Linavelt.</p>
        </div>
        <button class="shrink-0 inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium bg-white text-zinc-900 hover:bg-zinc-100 transition-colors">Get started free</button>
    </div>
</section>`,
    });

    bm.add('stats', {
        label: 'Stats',
        category: 'Patterns',
        media: icon('M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z'),
        content: `<section class="py-12 px-6 bg-zinc-950">
    <div class="mx-auto max-w-5xl grid grid-cols-3 gap-6 text-center">
        ${statTile('10k+', 'Active users')}
        ${statTile('99.9%', 'Uptime SLA')}
        ${statTile('<300ms', 'Avg response')}
    </div>
</section>`,
    });

    bm.add('testimonial', {
        label: 'Testimonial',
        category: 'Patterns',
        media: icon('M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z'),
        content: `<blockquote class="rounded-2xl border border-zinc-700 bg-zinc-900 p-8 max-w-2xl mx-auto">
    <p class="text-lg text-zinc-200 leading-relaxed">"Linavelt completely transformed how we ship features. The tooling is exactly what a modern team needs."</p>
    <footer class="mt-6 flex items-center gap-3">
        <div class="h-10 w-10 rounded-full bg-zinc-700 flex items-center justify-center text-zinc-300 text-sm font-semibold">JD</div>
        <div>
            <p class="text-sm font-semibold text-zinc-100">Jane Doe</p>
            <p class="text-xs text-zinc-400">CTO, Acme Corp</p>
        </div>
    </footer>
</blockquote>`,
    });

    /* ── Media ──────────────────────────────────────────────────── */
    bm.add('image', {
        label: 'Image',
        category: 'Media',
        media: icon('M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z'),
        content: `<figure class="overflow-hidden rounded-xl border border-zinc-700">
    <img src="https://placehold.co/800x400/18181b/52525b?text=Image" alt="Image placeholder" class="w-full object-cover" />
</figure>`,
    });

    bm.add('video', {
        label: 'Video',
        category: 'Media',
        media: icon('M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 010 1.972l-11.54 6.347a1.125 1.125 0 01-1.667-.986V5.653z'),
        content: `<div class="relative overflow-hidden rounded-xl border border-zinc-700 bg-zinc-900 aspect-video flex items-center justify-center">
    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-zinc-800 border border-zinc-700">
        <svg class="h-7 w-7 text-zinc-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7L8 5z"/></svg>
    </div>
    <p class="absolute bottom-4 left-0 right-0 text-center text-xs text-zinc-500">Click to set video source</p>
</div>`,
    });

    bm.add('divider', {
        label: 'Divider',
        category: 'Media',
        media: icon('M5 12h14'),
        content: `<hr class="border-zinc-700 my-8" />`,
    });

    bm.add('spacer', {
        label: 'Spacer',
        category: 'Media',
        media: icon('M12 3v18m-4-4l4 4 4-4M8 7l4-4 4 4'),
        content: `<div style="height:64px;"></div>`,
    });
}
