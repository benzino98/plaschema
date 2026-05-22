<div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4" id="faq-link-builder">
    <h3 class="text-sm font-bold text-gray-800 mb-3">Insert a link into the answer</h3>
    <p class="text-xs text-gray-600 mb-4">Add links to a site page, a downloadable resource, or an external website. Choose whether the link opens in the same tab or a new tab.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="faq-link-text" class="block text-xs font-semibold text-gray-700 mb-1">Link text</label>
            <input type="text" id="faq-link-text" placeholder="e.g. Download enrollment form"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm focus:outline-none focus:shadow-outline">
        </div>

        <div>
            <label for="faq-link-type" class="block text-xs font-semibold text-gray-700 mb-1">Link type</label>
            <select id="faq-link-type"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm focus:outline-none focus:shadow-outline">
                <option value="page">Site page (same website)</option>
                <option value="resource">Download a resource</option>
                <option value="external">External website</option>
                <option value="custom">Custom URL</option>
            </select>
        </div>

        <div id="faq-link-page-wrap">
            <label for="faq-link-page" class="block text-xs font-semibold text-gray-700 mb-1">Site page</label>
            <select id="faq-link-page"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm focus:outline-none focus:shadow-outline">
                <option value="">Select a page</option>
                @foreach($sitePages as $label => $url)
                    <option value="{{ $url }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div id="faq-link-resource-wrap" class="hidden">
            <label for="faq-link-resource" class="block text-xs font-semibold text-gray-700 mb-1">Resource to download</label>
            @if($downloadableResources->isEmpty())
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded px-3 py-2">No published resources yet. Upload resources in the admin panel first, or use a custom URL.</p>
            @else
                <select id="faq-link-resource"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm focus:outline-none focus:shadow-outline">
                    <option value="">Select a resource</option>
                    @foreach($downloadableResources as $resource)
                        <option value="{{ route('resources.download', $resource->slug) }}">{{ $resource->title }}</option>
                    @endforeach
                </select>
            @endif
        </div>

        <div id="faq-link-url-wrap" class="hidden md:col-span-2">
            <label for="faq-link-url" class="block text-xs font-semibold text-gray-700 mb-1">URL</label>
            <input type="url" id="faq-link-url" placeholder="https://example.com/page"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex items-center">
            <label class="flex items-center text-sm text-gray-700">
                <input type="checkbox" id="faq-link-new-tab" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300">
                <span class="ml-2">Open link in a new tab</span>
            </label>
        </div>
    </div>

    <div class="mt-4 flex flex-wrap gap-2">
        <button type="button" id="faq-insert-link-btn"
            class="bg-plaschema hover:bg-plaschema-dark text-white text-sm font-semibold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Insert link at cursor
        </button>
        <p class="text-xs text-gray-500 self-center">Tip: place your cursor in the answer box where you want the link to appear.</p>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const answerField = document.getElementById('answer');
    const linkType = document.getElementById('faq-link-type');
    const pageWrap = document.getElementById('faq-link-page-wrap');
    const resourceWrap = document.getElementById('faq-link-resource-wrap');
    const urlWrap = document.getElementById('faq-link-url-wrap');
    const newTabCheckbox = document.getElementById('faq-link-new-tab');
    const insertButton = document.getElementById('faq-insert-link-btn');

    if (!answerField || !linkType) {
        return;
    }

    function toggleLinkFields() {
        const type = linkType.value;
        pageWrap.classList.toggle('hidden', type !== 'page');
        resourceWrap.classList.toggle('hidden', type !== 'resource');
        urlWrap.classList.toggle('hidden', type !== 'external' && type !== 'custom');

        if (type === 'external') {
            newTabCheckbox.checked = true;
        } else if (type === 'page') {
            newTabCheckbox.checked = false;
        } else if (type === 'resource') {
            newTabCheckbox.checked = true;
        }
    }

    function resolveUrl() {
        const type = linkType.value;

        if (type === 'page') {
            return document.getElementById('faq-link-page').value;
        }

        if (type === 'resource') {
            const resourceSelect = document.getElementById('faq-link-resource');
            return resourceSelect ? resourceSelect.value : '';
        }

        return document.getElementById('faq-link-url').value.trim();
    }

    function insertAtCursor(field, text) {
        const start = field.selectionStart ?? field.value.length;
        const end = field.selectionEnd ?? field.value.length;
        const before = field.value.substring(0, start);
        const after = field.value.substring(end);
        field.value = before + text + after;
        const cursor = start + text.length;
        field.focus();
        field.setSelectionRange(cursor, cursor);
    }

    linkType.addEventListener('change', toggleLinkFields);
    toggleLinkFields();

    insertButton.addEventListener('click', function () {
        const label = document.getElementById('faq-link-text').value.trim();
        const url = resolveUrl();

        if (!label) {
            alert('Please enter the link text.');
            return;
        }

        if (!url) {
            alert('Please choose a destination for the link.');
            return;
        }

        const openInNewTab = newTabCheckbox.checked;
        const targetAttribute = openInNewTab ? ' target="_blank" rel="noopener noreferrer"' : '';
        const html = `<a href="${url}"${targetAttribute}>${label}</a>`;

        insertAtCursor(answerField, html);
    });
});
</script>
@endpush
