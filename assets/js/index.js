jQuery(function () {
    jQuery(".VanderTable").VanderTable({
        //showRowOrder : true,
        allowTableSort: true,
        onMoveCol: function (obj, pos) {
            jQuery(obj).css('background-color', '#BF360C !important');
            alert('Old Position: ' + pos.old + 'New Position: ' + pos.new);
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

 