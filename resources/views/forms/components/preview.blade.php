<div
    x-data="{
        html: $wire.entangle('data.body')
    }"
    class="prose max-w-none w-full border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-800 h-[600px] overflow-auto text-black"
>
    <div x-html="html"></div>
</div>
