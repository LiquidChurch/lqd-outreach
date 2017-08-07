<?php
$event_post     = $this->get('post');
$meta_prefix    = 'lo_ccb_events_';
$register_gform = Liquid_Outreach::$enable_ccb_gravity ? $event_post->get_meta($meta_prefix . 'gform') : FALSE;
$register_url   = $event_post->get_meta($meta_prefix . 'register_url');
$info_settings  = lo_get_option('additional-info', 'all');
?>
<div class="container-fluid lo-custom-container panel lo-panel-custom">
    <div class="row lo-no-margin">

        <div class="row">
            <div class="col-md-8"><span
                        class="lo-search-heading"><?php echo $event_post->title() ?></span></div>
            <div class="col-md-4"><strong class="lo-font-24">Additional Information</strong></div>
            <hr/>
        </div>

        <div class="row">

            <!--Details column-->
            <div class="col-md-8">

                <?php
                if ( ! empty($register_gform))
                {

                }
                else if ( ! empty($register_url))
                {
                    ?>
                    <a href="<?php echo $register_url ?>" <?php echo empty($register_url) ? 'disabled' : '' ?>
                       class="btn btn-primary pull-left lo-event-register-btn-top">Register Now
                    </a>
                    <hr/>
                    <?php
                }
                ?>


                <div class="lo-description">
                    <?php
                    $events_image = $event_post->get_meta($meta_prefix . 'image');
                    if ( ! empty($events_image))
                    {
                        ?>
                        <img src="<?php echo $events_image ?>" alt="<?php echo $event_post->title() ?>"/>
                        <hr/>
                        <?php
                    }
                    ?>

                    <?php
                    echo $event_post->post->post_content;
                    if ( ! empty($event_post->post->post_content))
                    {
                        ?>
                        <hr/>
                        <?php
                    }
                    ?>

                </div>


                <?php
                if ( ! empty($register_gform))
                {
                    ?>
                    <div class="row">
                        <div class="col-md-12">
                            <a href="javascript:void(0);"
                               id="lo-show-gravity-form-btn"
                               class="btn btn-primary pull-left lo-event-register-btn-top">
                                Show Register Form
                            </a>
                        </div>
                    </div>
                    <?php
                }
                else if ( ! empty($register_url))
                {
                    ?>
                    <a href="<?php echo $register_url ?>" <?php echo empty($register_url) ? 'disabled' : '' ?>
                       class="btn btn-primary pull-left lo-event-register-btn-top">Register Now
                    </a>
                    <?php
                }
                ?>

            </div>

            <!--addtnal info column-->
            <div class="col-md-4">
                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_date_time');
                if ($show == '1')
                {
                    ?>
                    <div class="row">
                        <div class="col-md-5 " style="vertical-align: top;"><strong>Date &
                                Time</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo date('l F jS Y <br/>g:i A',
                                $event_post->get_meta($meta_prefix . 'start_date')) ?></div>
                    </div>
                    <?php
                }
                ?>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_cost');
                if ($show == '1')
                {
                    $cost = ! empty($event_post->get_meta($meta_prefix . 'cost')) ? "$/{$event_post->get_meta($meta_prefix . 'cost')}" : __('None', 'liquid-outreach');
                    ?>
                    <div class="row">
                        <div class="col-md-5 "><strong>Cost</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo $cost ?></div>
                    </div>
                    <?php
                }
                ?>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_openings');
                if ($show == '1')
                {
                    ?>
                    <div class="row">
                        <div class="col-md-5 "><strong>Openings</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6">
                            <?php
                            $openings = $event_post->get_meta($meta_prefix . 'openings');
                            if ($openings == '0')
                            {
                                echo 'Closed';
                            }
                            else if ($openings == 'no-limit')
                            {
                                echo '<span style="font-size: 22px;">&infin;</span>';
                            }
                            else
                            {
                                echo ucwords(str_replace('-', ' ',
                                    $openings));
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_categories');
                if ($show == '1')
                {
                    $categories = $event_post->get_event_categories();

                    if ( ! empty($categories))
                    {
                        ?>
                        <div class="row">
                            <div class="col-md-5 "><strong>Categories</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
                                <?php

                                foreach ($categories as $index => $category)
                                {
                                    ?>
                                    <div class="lo-cat-img">
                                        <?php echo $category->image ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_kid_friendly');
                if ($show == '1')
                {
                    ?>
                    <div class="row">
                        <div class="col-md-5 "><strong>Kid friendly</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo ucfirst($event_post->get_meta($meta_prefix .
                                                                                       'kid_friendly')) ?></div>
                    </div>
                    <?php
                }
                ?>

                <br/>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_team_leader');
                if ($show == '1')
                {
                    ?>
                    <!--team leader block-->
                    <div class="row">
                        <div class="col-md-5 "><strong><u>Team Leader</u></strong></div>
                        <div class="col-md-7 "><br/></div>
                    </div>

                    <?php
                    $show
                        = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                        'lo_events_info_team_leader_name');
                    if ($show == '1')
                    {
                        ?>
                        <div class="row">
                            <div class="col-md-offset-1 col-md-4 add-info-label-2">
                                <strong>Name</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
                                <?php echo $event_post->get_meta($meta_prefix .
                                                                 'team_lead_fname') ?>
                                <?php echo $event_post->get_meta($meta_prefix .
                                                                 'team_lead_lname') ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    $show
                        = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                        'lo_events_info_team_leader_email');
                    if ($show == '1')
                    {
                        ?>
                        <div class="row">
                            <div class="col-md-offset-1 col-md-4 add-info-label-2">
                                <strong>Email</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
                                <?php echo $event_post->get_meta($meta_prefix .
                                                                 'team_lead_email') ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    $show
                        = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                        'lo_events_info_team_leader_phone');
                    if ($show == '1')
                    {
                        ?>
                        <div class="row">
                            <div class="col-md-offset-1 col-md-4 add-info-label-2">
                                <strong>Phone</strong></div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
                                <?php echo $event_post->get_meta($meta_prefix .
                                                                 'team_lead_phone') ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <!--/team leader block-->
                    <br/>
                    <?php
                }
                ?>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_partner_organization');
                if ($show == '1')
                {
                    ?>
                    <div class="row">
                        <?php
                        $meta_query_args = array(
                            array(
                                'key'     => 'lo_ccb_event_partner_group_id',
                                'value'   => $event_post->get_meta($meta_prefix .
                                                                   'group_id'),
                                'compare' => '='
                            )
                        );
                        $query           = new WP_Query([
                            'post_type'  => liquid_outreach()->lo_ccb_event_partners->post_type(),
                            'meta_query' => $meta_query_args
                        ]);
                        while ($query->have_posts())
                        {
                            $query->the_post();
                            global $post;
                            ?>
                            <div class="col-md-5 "><strong>Partner organization</strong>
                            </div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
                                <a href="<?php echo home_url("search-projects/?lo-event-org=" .
                                                             get_post_meta($post->ID,
                                                                 'lo_ccb_event_partner_group_id',
                                                                 TRUE)) ?>">
                                    <?php echo $post->post_title ?>
                                </a>
                            </div>
                            <?php
                        }
                        wp_reset_postdata();
                        ?>

                    </div>
                    <?php
                }
                ?>

                <?php
                $show
                    = LO_Ccb_Base_Function::check_details_display_enabled($event_post->post->ID,
                    'lo_events_info_address');
                if ($show == '1')
                {
                    ?>
                    <div class="row">
                        <div class="col-md-5 "><strong>Address</strong></div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo $event_post->get_meta($meta_prefix .
                                                                               'address') ?></div>
                    </div>
                    <?php
                }
                ?>
                <!--<div class="row">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3682.9756853490367!2d88.41061141444428!3d22.6173831370264!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39f89e1c998b806b%3A0x261de3892096fcf2!2sNosyworld+Solution+Pvt.+Ltd.!5e0!3m2!1sen!2sin!4v1494178462570"
                            width="350" height="350" frameborder="0" style="border:0"
                            allowfullscreen></iframe>
                </div>-->
            </div>

        </div>

        <?php
        if ( ! empty($register_gform))
        {
            ?>
            <div class="row lo-gravity-form-container" <?php echo (isset($_POST) && ! empty($_POST)) ? 'style="display: block;"' : 'style="display: none;"' ?>>
                <?php
                if ( ! CCB_GRAVITY_manage_session::if_user_logged_in())
                {
                    $login_gform = ccb_gravity_get_option('option', 'ccb_gravity_option_ccb_login_gform');
                    ?>
                    <div class="col-md-12">
                        <?php
                        echo do_shortcode("[gravityform id={$login_gform} title=true description=true ajax=false]");
                        ?>
                    </div>
                    <?php
                }
                else
                {
                    $args = array(
                        'user_data' => isset($_SESSION['ccb_plugin']['user_profile']) ? $_SESSION['ccb_plugin']['user_profile'] : array()
                    );
                    echo '<div class="col-md-12">';
                    echo CCB_GRAVITY_Template_Loader::get_template('gform/ccb-gform-user-logged-in', $args);
                    echo '</div>';
                    ?>
                    <?php
                }
                ?>
                <div class="col-md-12">
                    <?php
                    echo do_shortcode("[gravityform id={$register_gform} title=true description=false ajax=true]");
                    ?>
                </div>
            </div>
            <?php
        }
        ?>

    </div><!--/.row-->
</div>

<script type="text/javascript">
    (function ($)
    {
        $(document).ready(function ()
        {
            $("#lo-show-gravity-form-btn").click(function ()
            {
                if (typeof $(this).data('form-show') == 'undefined' || $(this).data('form-show') == 0)
                {
                    $(this).data('form-show', 1);
                    $(this).text('Hide Register Form');
                } else
                {
                    $(this).data('form-show', 0);
                    $(this).text('Show Register Form');
                }
                $(".lo-gravity-form-container").toggle(300);
            });

            <?php
            if(isset($_POST) && ! empty($_POST)) {
            ?>
            $('html, body').animate({
                scrollTop: $(".lo-gravity-form-container").offset().top
            }, 2000);
            <?php
            }
            ?>
        });
    })(jQuery)
</script>