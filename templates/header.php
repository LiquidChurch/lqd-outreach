<?php
$event_cat_tax = liquid_outreach()->lo_ccb_event_categories->taxonomy();
if (is_category($event_cat_tax) || is_tax($event_cat_tax)) {
    $term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
    $term_aug = liquid_outreach()->lo_ccb_event_categories->get($term);
    $header_img = $term_aug->header_image_url;
}

if (!isset($header_img) || empty($header_img)) {
    $image_id = lo_get_option('page', 'lo_events_page_default_header_image_id');
    if (!empty($image_id)) {
        $header_img = wp_get_attachment_image_url($image_id, 'full');
    }
}

if (!empty($header_img)) {
    ?>
    <div class="lo-full">
        <img src="<?php echo $header_img ?>" width="100%">
    </div>
    <?php
}
