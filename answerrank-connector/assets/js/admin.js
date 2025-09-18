(function($){
    $(function(){
        $('#arc-test-connection').on('click', function(){
            const $btn = $(this);
            const $out = $('#arc-conn-result');
            $btn.prop('disabled', true);
            $out.text('Testing...');

            $.ajax({
                method: 'GET',
                url: ARC_ADMIN.rest_url + '/ping',
                beforeSend: function(xhr){ xhr.setRequestHeader('X-WP-Nonce', ARC_ADMIN.nonce); }
            }).done(function(res){
                $out.text('OK: ' + (res && res.backend ? res.backend : 'connected'));
            }).fail(function(xhr){
                $out.text('Failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'error'));
            }).always(function(){
                $btn.prop('disabled', false);
            });
        });

        $('#arc-open-connect').on('click', function(e){
            e.preventDefault();
            // Open backend connect page in new tab using saved base URL
            try {
                const base = $('#backend_base_url').val().replace(/\/+$/,'');
                if(!base) { alert('Please save a Backend Base URL first.'); return; }
                const url = base + '/connect?origin=' + encodeURIComponent(window.location.origin);
                window.open(url, '_blank', 'noopener');
            } catch(err) {
                alert('Could not open connect page: ' + err.message);
            }
        });
    });
})(jQuery);
