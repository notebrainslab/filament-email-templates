<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <!-- Include Unlayer script only once if multiple editors -->
    @once
        <script src="https://editor.unlayer.com/embed.js"></script>
    @endonce

    <div
        x-data="{
            state: $wire.entangle('{{ $getStatePath() }}'),
            projectId: '{{ @config('filament-email-templates.unlayer_project_id') }}',
            editor: null,
            initEditor() {
                var container = this.$refs.editorContainer;
                
                this.editor = unlayer.createEditor({
                    id: container.id || 'editor-' + Math.random().toString(36).substring(7),
                    projectId: this.projectId || null,
                    displayMode: 'email',
                    ready: () => {
                        if (this.state && this.state.json) {
                            this.editor.loadDesign(this.state.json);
                        }
                    }
                });

                // When design changes, update state
                this.editor.addEventListener('design:updated', (updates) => {
                    this.saveDesign();
                });
            },
            saveDesign() {
                this.editor.exportHtml((data) => {
                    var { design, html } = data;
                    this.state = {
                        json: design,
                        html: html
                    };
                });
            }
        }"
        x-init="initEditor()"
        wire:ignore
        class="border border-gray-300 rounded-lg overflow-hidden shadow-sm dark:border-gray-700 bg-white"
        style="height: 800px; min-height: 50vh;"
    >
        <div x-ref="editorContainer" id="unlayer-container" style="height: 100%; width: 100%;"></div>
    </div>
</x-dynamic-component>
