@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js" referrerpolicy="origin"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') {
        return;
    }

    tinymce.init({
        selector: '#content',
        height: 420,
        menubar: false,
        branding: false,
        promotion: false,
        plugins: 'lists link autolink paste',
        toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright | bullist numlist | link removeformat',
        block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4; Blockquote=blockquote',
        default_link_target: '_blank',
        link_default_protocol: 'https',
        paste_as_text: false,
        content_style: 'body { font-family: inherit; font-size: 16px; line-height: 1.6; }',
        setup: function (editor) {
            editor.on('change keyup', function () {
                editor.save();
            });
        }
    });

    const form = document.getElementById('news-form');
    if (form) {
        form.addEventListener('submit', function () {
            if (tinymce.get('content')) {
                tinymce.get('content').save();
            }
        });
    }
});
</script>
@endpush
