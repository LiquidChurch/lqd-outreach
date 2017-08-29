<?php
$events         = $this->get('events');
$pagination     = $this->get('pagination');
$force_cat_page = $this->get('force_cat_page');
$meta_prefix    = 'lo_ccb_events_';
?>
<div class="container">
    <div class="row">
        <h1 style="margin-top: 30px; margin-bottom: 0;">Upcoming Projects</h1>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?php
            if ( ! empty($events))
            {
                ?>
                <!--Tables result for search-->
                <table class="VanderTable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Project</th>
                        <th>Category</th>
                        <th>Day</th>
                        <th>Location</th>
                        <th>Campus</th>
                        <th>Openings</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($events as $key => $event)
                    {
                        $meta_start_date = $event->get_meta($meta_prefix . 'start_date');
                        ?>
                        <tr>
                            <td>
                                <div class="lo-date-cal">
                                    <div class="lo-month"><?php echo date('M', $meta_start_date) ?></div>
                                    <div class="lo-date"><?php echo date('d', $meta_start_date) ?></div>
                                </div>
                            </td>
                            <td>
                                <?php
                                $permalink = $force_cat_page ? $event->permalink() . '?lo-cat-page=care-packages-donations' : $event->permalink();
                                ?>
                                <a href="<?php echo $permalink ?>"><?php echo $event->title() ?></a>
                            </td>
                            <td>
                                <?php
                                $categories = $event->get_event_categories([
                                    'image_size' => 40
                                ]);
                                if ( ! empty($categories))
                                {
                                    foreach ($categories as $category)
                                    {
                                        ?>
                                        <div class="lo-cat-img">
                                            <?php
                                            echo $category->image;
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <td><?php echo date('l', $meta_start_date) ?></td>
                            <td><?php echo ucwords($event->get_meta($meta_prefix . 'city')) ?></td>
                            <td>
                                <?php
                                $campus = $event->get_meta($meta_prefix . 'campus');
                                $campus_arr = explode('|', $campus);
                                if (empty($campus_arr[1]))
                                {
                                    echo '';
                                }
                                else
                                {
                                    echo ucwords($campus_arr[1]);
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $openings = $event->get_meta($meta_prefix . 'openings');
                                if ($openings == '0')
                                {
                                    echo 'Closed';
                                }
                                else if ($openings == 'no-limit')
                                {
                                    echo '<span style="font-size: 24px;">&infin;</span>';
                                }
                                else
                                {
                                    echo ucwords(str_replace('-', ' ', $openings));
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
                <?php
            }
            else
            {
                echo '<h4>' . $this->get('event_empty_msg') . '</h4>';
            }
            ?>
        </div>
        <div class="col-lg-12">
            <div class="page-nation">
                <ul class="pagination pagination-large">
                    <?php
                    if ( ! empty($pagination['prev_link']))
                    {
                        echo '<li class="">' . $pagination['prev_link'] . '</li>';
                    }

                    if ( ! empty($pagination['next_link']))
                    {
                        echo '<li class="next">' . $pagination['next_link'] . '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>