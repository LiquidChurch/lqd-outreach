<?php
$partner_post = $this->get('post');
$meta_prefix = 'lo_ccb_event_partner_';
$info_settings = lo_get_option('additional-info', 'all');
$image = $partner_post->featured_image('thumbnail');
?>

<div class="container-fluid lo-custom-container panel lo-panel-custom" >
    <div class="row lo-no-margin">
        <!--Details column-->
        <div class="row">
            <div class="col-md-8"><span class="lo-search-heading"><?php echo $partner_post->title() ?></span></div>
            <div class="col-md-4"><strong class="lo-font-24">Organization Information</strong></div>
            <hr/>
        </div>
        <div class="row">
            <div class="col-md-8" style="border-right: 1px solid #e2e2e2; min-height: 300px;">
                <div class="lo-description">
                    <?php
                    if (!empty($image)) {
                        ?>
                        <div class="col-md-4 text-center">
                            <?php echo $image; ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="<?php echo empty($image) ? 'col-md-12' : 'col-md-8' ?>">
                        <?php
                        echo $partner_post->post->post_content;
                        ?>
                    </div>
                </div>
            </div>
            <!--addtnal info column-->
            <div class="col-md-4">
                <?php
                if (!empty($info_settings['lo_events_info_partner_address']) || !isset($info_settings['lo_events_info_partner_address'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-5 " style="vertical-align: top;"><strong>Address</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6">
                            <?php
                            $address = $partner_post->get_meta($meta_prefix . 'location');
                            if (!empty($address))
                                if (is_array($address)) {
                                    echo implode('<br/>', $address);
                                } else {
                                    echo $address;
                                }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if (!empty($info_settings['lo_events_info_partner_website']) || !isset($info_settings['lo_events_info_partner_website'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-5 " style="vertical-align: top;"><strong>Website</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo $partner_post->get_meta($meta_prefix .
                                'website') ?></div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if (!empty($info_settings['lo_events_info_partner_team_leader']) || !isset($info_settings['lo_events_info_partner_team_leader'])) {
                    ?>
                    <div class="row">
                        <div class="col-md-5 add-info-label"><strong><u>Team Leader</u></strong></div>
                        <div class="col-md-7 add-info-label"><br/></div>

                        <?php
                        if (!empty($info_settings['lo_events_info_partner_team_leader']) || !isset($info_settings['lo_events_info_partner_team_leader'])) {
                            ?>
                            <div class="col-md-offset-1 col-md-4 add-info-label-2"><strong>Name</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6"><?php echo $partner_post->get_meta($meta_prefix .
                                    'team_leader') ?><br/></div>
                            <?php
                        }
                        ?>

                        <?php
                        if (!empty($info_settings['lo_events_info_partner_email']) || !isset($info_settings['lo_events_info_partner_email'])) {
                            ?>
                            <div class="col-md-offset-1 col-md-4 add-info-label-2"><strong>Email</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6"><?php echo $partner_post->get_meta($meta_prefix .
                                    'email') ?><br/></div>
                            <?php
                        }
                        ?>

                        <?php
                        if (!empty($info_settings['lo_events_info_partner_phone']) || !isset($info_settings['lo_events_info_partner_phone'])) {
                            ?>
                            <div class="col-md-offset-1 col-md-4 add-info-label-2"><strong>Phone</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6"><?php echo $partner_post->get_meta($meta_prefix .
                                    'phone') ?><br/></div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div><!--/.row-->

</div>