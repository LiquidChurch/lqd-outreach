jQuery(function () {
    jQuery(".VanderTable").VanderTable({
        //showRowOrder : true,
        'allowTableSort': true,
        'disableColReordering': true,
        'disableRowReordering': true,
        onMoveCol: function (obj, pos) {
            jQuery(obj).css('background-color', '#BF360C !important');
        }
    });

    jQuery('#lo-event-form-advanced-option-btn').click(function (e) {
        var btn = jQuery(this);
        jQuery("#lo-event-form-advanced-option").toggle("slow", function () {
            if (btn.text() == 'Show Advanced Options') {
                btn.text('Hide Advanced Options');
            } else {
                btn.text('Show Advanced Options');
            }
        });
    });

    jQuery('ul.nav li.dropdown').hover(function() {
        jQuery(this).addClass('open');
        jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn(500);
    }, function() {
        jQuery(this).removeClass('open');
        jQuery(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut(500);
    });
});

 