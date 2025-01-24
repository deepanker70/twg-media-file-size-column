(function ($) {
    $(document).on('click', '.twgp-refresh-size', function (e) {
        e.preventDefault();
        const button = $(this);
        const postId = button.data('post-id');

        button.html('<span class="twgp-loading"></span>');

        $.post(
            twgp_ajax_object.ajax_url,
            {
                action: 'twgp_get_file_size',
                security: twgp_ajax_object.twgp_nonce,
                post_id: postId,
            },
            function (response) {
                if (response.success) {
                    button.closest('td').html(response.data.file_size);
                } else {
                    alert(response.data || 'An error occurred.');
                    button.text('Retry');
                }
            }
        );
    });
})(jQuery);
