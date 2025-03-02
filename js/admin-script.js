jQuery(document).ready(function($) {
    $('.delete-media').on('click', function() {
        if (!confirm('Are you sure you want to delete this media?')) return;

        var $button = $(this);
        var attachmentId = $button.data('id');

        $.ajax({
            url: unusedMedia.ajax_url,
            method: 'POST',
            data: {
                action: 'delete_unused_media',
                attachment_id: attachmentId,
                nonce: unusedMedia.nonce
            },
            success: function(response) {
                if (response.success) {
                    $button.closest('tr').remove();
                    alert('Media deleted successfully');
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred');
            }
        });
    });
});