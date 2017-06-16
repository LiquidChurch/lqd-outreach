/**
 * Created by surajgupta on 16/6/17.
 */
jQuery(function () {

    if(typeof current_screen != 'undefined' && typeof current_screen.id != 'undefined') {

        if(current_screen.id == 'lo-events') {
            var additional_metabox = jQuery("#lo_ccb_events_additional_meta");
            var events_leader_metabox = jQuery("#lo_ccb_events_leader");
            var metabox_parent = additional_metabox.parent();

            metabox_parent.prepend(events_leader_metabox);
            metabox_parent.prepend(additional_metabox);
        }
        else if(current_screen.id == "lo-event-partners") {
            var additional_metabox = jQuery("#lo_ccb_event_partner_additional_meta");
            var metabox_parent = additional_metabox.parent();

            metabox_parent.prepend(additional_metabox);
        }
    }

});