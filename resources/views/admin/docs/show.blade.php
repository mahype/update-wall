<x-app-layout>
    <x-slot name="header">API-Dokumentation</x-slot>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Sidebar Navigation --}}
        <nav class="lg:w-56 shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 p-4 lg:sticky lg:top-24">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Seiten</h3>
                <ul class="space-y-1">
                    @foreach($pages as $page)
                        <li>
                            <a href="{{ route('admin.docs.show', $page['slug']) }}"
                               class="block px-3 py-2 text-sm rounded-lg transition {{ $page['slug'] === $current['slug'] ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                {{ $page['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-xl border border-gray-200 p-6 sm:p-8">
                <div x-data="{ copied: false }" class="flex items-center justify-end mb-4">
                    <button @click="navigator.clipboard.writeText($refs.markdown.value).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg border transition"
                            :class="copied ? 'bg-green-50 border-green-200 text-green-700' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'">
                        <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9.75a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" />
                        </svg>
                        <svg x-show="copied" x-cloak class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        <span x-text="copied ? 'Kopiert!' : 'Markdown kopieren'"></span>
                    </button>
                    <textarea x-ref="markdown" class="hidden">{{ $content }}</textarea>
                </div>
                <div class="prose prose-gray max-w-none
                            prose-headings:font-semibold
                            prose-h1:text-2xl prose-h1:border-b prose-h1:border-gray-200 prose-h1:pb-3 prose-h1:mb-6
                            prose-h2:text-xl prose-h2:mt-8
                            prose-h3:text-lg
                            prose-a:text-indigo-600 prose-a:no-underline hover:prose-a:underline
                            prose-code:text-sm prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:before:content-none prose-code:after:content-none
                            prose-pre:bg-gray-900 prose-pre:text-gray-100 prose-pre:rounded-xl
                            [&_pre_code]:bg-transparent [&_pre_code]:text-inherit [&_pre_code]:p-0
                            prose-table:text-sm
                            prose-th:bg-gray-50 prose-th:px-4 prose-th:py-2
                            prose-td:px-4 prose-td:py-2
                            prose-tr:border-b prose-tr:border-gray-200">
                    {!! $html !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
