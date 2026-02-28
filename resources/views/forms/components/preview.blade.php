<div
    x-data="{
        body: $wire.entangle('data.body'),
        theme_html: $wire.entangle('data.theme_html'),
        get html() {
            let content = '';
            if (typeof this.body === 'string') content = this.body;
            else if (this.body && typeof this.body === 'object' && this.body.hasOwnProperty('html')) content = this.body.html;

            if (this.theme_html && typeof this.theme_html === 'string') {
                return this.theme_html.replace('##body_content##', content);
            }
            
            return content;
        }
    }"
    class="prose max-w-none w-full border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 h-[600px] overflow-auto text-black"
>
    <template x-if="html">
        <div class="bg-white rounded shadow-sm p-4 min-h-full">
            <div x-html="html"></div>
        </div>
    </template>
    <template x-if="!html">
        <div class="flex items-center justify-center h-full text-gray-400">
            <span>Enter some content in the 'Content' tab to see a preview.</span>
        </div>
    </template>
</div>
