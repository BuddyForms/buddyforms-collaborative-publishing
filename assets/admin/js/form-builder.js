jQuery(document).ready(function (jQuery) {


    jQuery(document.body).on('change', '.bf_enable_moderation_hidden_checkbox', function () {

        var input = jQuery(this).find("input");
        var ids = input.attr('bf_hidden_checkbox');
        var id = input.attr('id');

        if (!ids)
            return;

        if (jQuery(input).is(':checked')) {
            ids = ids.split(" ");

            ids.forEach(function (entry) {
                jQuery('.' + entry).removeClass('hidden');

            });
        } else {
            ids = ids.split(" ");
            ids.forEach(function (entry) {
                jQuery('.' + entry).addClass('hidden');
            });
        }

    });

    jQuery(document.body).on('change', '.bf_enable_teams_hidden_checkbox', function () {

        var input = jQuery(this).find("input");
        var ids = input.attr('bf_hidden_checkbox');
        var id = input.attr('id');

        if (!ids)
            return;

        if (jQuery(input).is(':checked')) {
            ids = ids.split(" ");

            ids.forEach(function (entry) {
                jQuery('.' + entry).removeClass('hidden');

            });
        } else {
            ids = ids.split(" ");
            ids.forEach(function (entry) {
                jQuery('.' + entry).addClass('hidden');
            });
        }

    });


});