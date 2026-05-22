@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('news-form');
    const textarea = document.getElementById('content');

    if (!form || !textarea) {
        return;
    }

    function syncEditorToTextarea() {
        if (typeof tinymce !== 'undefined') {
            tinymce.triggerSave();
        }
    }

    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#content',
            height: 420,
            menubar: false,
            branding: false,
            promotion: false,
            plugins: 'lists link autolink paste',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link removeformat',
            block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Blockquote=blockquote',
            default_link_target: '_blank',
            link_default_protocol: 'https',
            paste_as_text: false,
            content_style: 'body { font-family: inherit; font-size: 16px; line-height: 1.6; }',
            setup: function (editor) {
                editor.on('change keyup blur', function () {
                    editor.save();
                });
            }
        });
    }

    let allowSubmit = false;

    form.addEventListener('submit', function (event) {
        syncEditorToTextarea();

        const plain = textarea.value.replace(/<[^>]+>/g, '').replace(/&nbsp;/gi, ' ').trim();

        if (!plain) {
            event.preventDefault();
            alert('Please enter the news article content.');
            return;
        }

        if (allowSubmit) {
            return;
        }

        event.preventDefault();
        allowSubmit = true;
        form.requestSubmit();
    });
});
</script>
@endpush
