<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    {{-- Load Unlayer --}}
    <script src="https://editor.unlayer.com/embed.js"></script>

    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            projectId: '{{ @config('filament-email-templates.unlayer_project_id') }}',
            unlayerReady: false,
            
            initEditor() {
                let isDesignLoaded = false;

                const tryLoadDesign = (stateValue) => {
                    if (isDesignLoaded || !stateValue || !this.unlayerReady) return;
                    try {
                        let parsed = (typeof stateValue === 'string') ? JSON.parse(stateValue) : stateValue;
                        if (parsed && parsed.design) {
                            let design = parsed.design;
                            if (typeof design === 'string') design = JSON.parse(design);
                            unlayer.loadDesign(JSON.parse(JSON.stringify(design)));
                            isDesignLoaded = true;
                        }
                    } catch (err) {
                        console.error('[UnlayerEditor] Failed to load design:', err);
                    }
                };

                unlayer.init({
                    id: 'unlayer-editor-{{ $getId() }}',
                    displayMode: 'email',
                    projectId: this.projectId || null,
                });

                unlayer.addEventListener('editor:ready', () => {
                    this.unlayerReady = true;
                    tryLoadDesign(this.state);
                });

                this.$watch('state', (newVal) => {
                    tryLoadDesign(newVal);
                });

                let saveTimeout;
                unlayer.addEventListener('design:updated', () => {
                    clearTimeout(saveTimeout);
                    saveTimeout = setTimeout(() => {
                        unlayer.exportHtml((data) => {
                            try {
                                this.state = JSON.stringify({
                                    design: data.design,
                                    html: data.html
                                });
                            } catch (e) {
                                console.error('[UnlayerEditor] Failed to save design:', e);
                            }
                        });
                    }, 600);
                });
            },

            loadStarterTemplate() {
                if (!this.unlayerReady) return;

                // Minimalist Starter Template JSON
                const starterDesign = {
                    body: {
                        rows: [
                            {
                                cells: [1],
                                columns: [
                                    {
                                        contents: [
                                            {
                                                type: 'image',
                                                values: {
                                                    src: { url: 'https://cdn.templates.unlayer.com/assets/1597218426091-xx.png' }
                                                }
                                            },
                                            {
                                                type: 'heading',
                                                values: { text: 'Welcome to Our Service!' }
                                            },
                                            {
                                                type: 'text',
                                                values: { text: '<p>Hello <strong>{{user.name}}</strong>,</p><p>We are glad to have you here. This is a dynamic template!</p>' }
                                            },
                                            {
                                                type: 'button',
                                                values: { text: 'Get Started', href: '{{url}}', backgroundColor: '#4F46E5', color: '#ffffff' }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                };
                
                if (confirm('Are you sure? This will replace your current design.')) {
                    unlayer.loadDesign(starterDesign);
                }
            }
        }"
        x-init="initEditor()"
        wire:ignore
        class="border border-gray-300 rounded-lg overflow-hidden shadow-sm dark:border-gray-700 bg-white"
    >
        {{-- Toolbar with Load Template button matching the UI --}}
        <div class="bg-gray-50 border-b border-gray-200 p-2 flex justify-end dark:bg-gray-800 dark:border-gray-700">
            <button 
                type="button" 
                @click="loadStarterTemplate()"
                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Load Starter Template
            </button>
        </div>

        <div id="unlayer-editor-{{ $getId() }}" style="height: 750px; width: 100%;"></div>
    </div>
</x-dynamic-component>
