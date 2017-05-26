<?php
	$event_post    = $this->get( 'post' );
	$meta_prefix   = 'lo_ccb_events_';
	$register_url  = $event_post->get_meta( $meta_prefix . 'register_url' );
	$info_settings = lo_get_option( 'all' );
?>
<div class="container lo-custom-container">
    <div class="row lo-no-margin">
        <!--Details column-->
        <div class="col-md-8 panel lo-panel-custom">

            <span class="lo-search-heading"><?php echo $event_post->title() ?></span>

            <a href="<?php echo $register_url ?>" <?php echo empty( $register_url ) ? 'disabled' : '' ?>
               class="btn btn-primary pull-left lo-event-register-btn-top">Register Now
            </a>

            <hr/>

            <p class="lo-description">
				<?php
					echo $event_post->featured_image( 'large' );
				?>
            <hr/>
			
			<?php
				echo $event_post->post->post_content;
			?>

            </p>

            <hr/>

            <a href="<?php echo $register_url ?>" <?php echo empty( $register_url ) ? 'disabled' : '' ?>
               class="btn btn-primary pull-left lo-event-register-btn-top">Register Now
            </a>

        </div>
        <!--addtnal info column-->
        <div class="col-md-4">
            <hr/>
            <strong>Additional Information</strong>
            <hr/>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_date_time'] ) || ! isset( $info_settings['lo_events_info_date_time'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-5 " style="vertical-align: top;">Date & Time</div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo date( 'l jS M Y h:iA',
								$event_post->get_meta( $meta_prefix . 'start_date' ) ) ?></div>
                    </div>
					<?php
				}
			?>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_cost'] ) || ! isset( $info_settings['lo_events_info_cost'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-5 ">Cost</div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo '$' . $event_post->get_meta( $meta_prefix .
						                                                              'cost' ) ?></div>
                    </div>
					<?php
				}
			?>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_openings'] ) || ! isset( $info_settings['lo_events_info_openings'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-5 ">Openings</div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6">
							<?php
								$openings = $event_post->get_meta( $meta_prefix . 'openings' );
								if ( $openings == '0' ) {
									echo 'Closed';
								} elseif ( $openings == 'no-limit' ) {
									echo '<span style="font-size: 22px;">&infin;</span>';
								} else {
									echo ucwords( str_replace( '-', ' ',
										$openings ) );
								}
							?>
                        </div>
                    </div>
					<?php
				}
			?>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_categories'] ) || ! isset( $info_settings['lo_events_info_categories'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-5 ">Categories</div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6">
							<?php
								$categories = $event_post->get_event_categories();
								
								if ( ! empty( $categories ) ) {
									foreach ( $categories as $index => $category ) {
										?>
                                        <div class="lo-cat-img">
											<?php echo $category->image ?>
                                        </div>
										<?php
									}
								}
							?>

                        </div>
                    </div>
					<?php
				}
			?>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_kid_friendly'] ) || ! isset( $info_settings['lo_events_info_kid_friendly'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-5 ">Kid friendly</div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo ucfirst( $event_post->get_meta( $meta_prefix .
						                                                                 'kid_friendly' ) ) ?></div>
                    </div>
					<?php
				}
			?>

            <br/>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_team_leader'] ) || ! isset( $info_settings['lo_events_info_team_leader'] ) ) {
					?>
                    <!--team leader block-->
                    <div class="row">
                        <div class="col-md-5 ">Team Leader</div>
                        <div class="col-md-7 "><br/></div>
                    </div>
					
					<?php
					if ( ! empty( $info_settings['lo_events_info_team_leader_name'] ) || ! isset( $info_settings['lo_events_info_team_leader_name'] ) ) {
						?>
                        <div class="row">
                            <div class="col-md-5 -2">Name</div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
								<?php echo $event_post->get_meta( $meta_prefix .
								                                  'team_lead_fname' ) ?>
								<?php echo $event_post->get_meta( $meta_prefix .
								                                  'team_lead_lname' ) ?>
                            </div>
                        </div>
						<?php
					}
					?>
					
					<?php
					if ( ! empty( $info_settings['lo_events_info_team_leader_email'] ) || ! isset( $info_settings['lo_events_info_team_leader_email'] ) ) {
						?>
                        <div class="row">
                            <div class="col-md-5 -2">Email</div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
								<?php echo $event_post->get_meta( $meta_prefix .
								                                  'team_lead_email' ) ?>
                            </div>
                        </div>
						<?php
					}
					?>
					
					<?php
					if ( ! empty( $info_settings['lo_events_info_team_leader_phone'] ) || ! isset( $info_settings['lo_events_info_team_leader_phone'] ) ) {
						?>
                        <div class="row">
                            <div class="col-md-5 -2">Phone</div>
                            <div class="col-md-1">&#8594</div>
                            <div class="col-md-6">
								<?php echo $event_post->get_meta( $meta_prefix .
								                                  'team_lead_phone' ) ?>
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
				if ( ! empty( $info_settings['lo_events_info_partner_organization'] ) || ! isset( $info_settings['lo_events_info_partner_organization'] ) ) {
					?>
                    <div class="row">
						<?php
							$meta_query_args = array(
								array(
									'key'     => 'lo_ccb_event_partner_group_id',
									'value'   => $event_post->get_meta( $meta_prefix . 'group_id' ),
									'compare' => '='
								)
							);
							$query           = new WP_Query( [
								'post_type'  => liquid_outreach()->lo_ccb_event_partners->post_type(),
								'meta_query' => $meta_query_args
							] );
							while ( $query->have_posts() ) {
								$query->the_post();
								global $post;
								?>
                                <div class="col-md-5 ">Partner organization</div>
                                <div class="col-md-1">&#8594</div>
                                <div class="col-md-6"><?php echo $post->post_title ?></div>
								<?php
							}
						?>

                    </div>
					<?php
				}
			?>
			
			<?php
				if ( ! empty( $info_settings['lo_events_info_address'] ) || ! isset( $info_settings['lo_events_info_address'] ) ) {
					?>
                    <div class="row">
                        <div class="col-md-5 ">Address</div>
                        <div class="col-md-1">&#8594</div>
                        <div class="col-md-6"><?php echo $event_post->get_meta( $meta_prefix .
						                                                        'address' ) ?></div>
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
    </div><!--/.row-->
</div>