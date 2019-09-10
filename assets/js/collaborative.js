jQuery(document).ready(function () {

    // if (BuddyFormsHooks && buddyformsGlobal) {
    //     BuddyFormsHooks.addFilter('buddyforms_global_delete_text', function(value,option){
    //
    //         return 'boom';
    //     },10);
    // }

    jQuery(document.body).on('click', '.bf_remove_as_editor', function () {
        var post_id = jQuery(this).attr('id');

        if (confirm( 'Remove as Editor' )) { // todo need il18n
            jQuery.ajax({
                type: 'POST',
                url: buddyformsGlobal.admin_url,
                data: {"action": "buddyforms_ajax_delete_post", "post_id": post_id},
                success: function (data) {
                    if (isNaN(data)) {
                        alert(data);
                    } else {
                        var id = "#bf_post_li_";
                        var li = id + data;
                        li = li.replace(/\s+/g, '');
                        jQuery(li).remove();
                    }
                },
                error: function (request) {
                    alert(request.responseText);
                }
            });
        } else {
            return false;
        }
        return false;
    });
});