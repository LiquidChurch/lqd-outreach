<?php
	$events     = $this->get( 'events' );
	$pagination = $this->get( 'pagination' );
	$meta_prefix = 'lo_ccb_events_';
?>
<div class="container">
    <div class="row">
        <h1>Upcoming Projects</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <!--Tables result for search-->
            <table class="VanderTable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Project</th>
                    <th>Category</th>
                    <th>Day</th>
                    <th>Location</th>
                    <th>Openings</th>
                </tr>
                </thead>
                <tbody>
				<?php
					foreach ( $events as $key => $event ) {
						?>
                        <tr>
                            <td><?php echo date('Y-m-d H:i:s', $event->get_meta($meta_prefix . 'start_date')) ?></td>
                            <td><?php echo $event->title() ?></td>
                            <td>hands on</td>
                            <td>12</td>
                            <td>carolina</td>
                            <td>Closed</td>
                        </tr>
						<?php
					}
				?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-12">
            <div class="page-nation">
                <ul class="pagination pagination-large">
                    <?php
                    if(!empty($pagination['prev_link'])) {
	                    echo '<li class="">'.$pagination['prev_link'].'</li>';
                    }
                    
                    if(!empty($pagination['next_link'])) {
                     echo '<li class="next">'.$pagination['next_link'].'</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>