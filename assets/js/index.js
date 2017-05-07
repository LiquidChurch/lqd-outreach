jQuery(function () {
    jQuery(".VanderTable").VanderTable({
        //showRowOrder : true,
        'allowTableSort': false,
        'disableColReordering': false,
        'disableRowReordering': false,
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
});

 