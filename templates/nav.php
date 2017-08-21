<?php
$page_settings = $this->get('page_settings');
$page_link     = $this->get('page_link');
?>

<nav class="navbar navbar-default lo-nav-custom" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="lo-navbar-toggle navbar-toggle collapsed"
                    data-toggle="collapse"
                    data-target="#lo-events-navbar" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="lo-events-navbar">
            <ul class="nav navbar-nav lo-navbar-nav">
                <?php
                if ('true' == $this->get('menu_option_index'))
                {
                    ?>
                    <li>
                        <a href="<?php echo $page_link['main'] ?>">Projects</a>
                    </li>
                    <?php
                }
                if ('true' == $this->get('menu_option_search'))
                {
                    ?>
                    <li>
                        <!-- Changed this from Search to Browse temporarily. -->
                        <a href="<?php echo $page_link['search'] ?>">Browse
                            Projects</a>
                    </li>
                    <?php
                }
                if ('true' == $this->get('menu_option_categories'))
                {
                    ?>
                    <li class="dropdown lo-dropdown-submenu">
                        <a href="<?php echo $page_link['cat'] ?>"
                           onclick="location.href = '<?php echo $page_link['cat'] ?>'"
                           class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true" aria-expanded="false">Project Categories <span
                                    class="caret"></span></a>
                        <?php
                        if ( ! empty($this->get('categories')))
                        {
                            echo '<ul class="dropdown-menu lo-dropdown-menu">';
                            foreach ($this->get('categories') as $val)
                            {
                                $link = $val->term_link;
                                if ( ! empty($page_link['page_query_arr']))
                                {
                                    $link = $val->term_link . '?' . http_build_query($page_link['page_query_arr']);
                                }
                                echo '<li><a href="' . $link . '">' . $val->name . '</a></li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                    </li>
                    <?php
                }
                if ('true' == $this->get('menu_option_city'))
                {
                    ?>
                    <li class="dropdown lo-dropdown-submenu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true" aria-expanded="false">Projects by City <span
                                    class="caret"></span></a>
                        <?php
                        if ( ! empty($this->get('cities')))
                        {
                            echo '<ul class="dropdown-menu lo-dropdown-menu">';
                            foreach ($this->get('cities') as $val)
                            {
                                if ( ! empty($page_link['page_query_arr']))
                                {
                                    $link = $page_link['search'] . "&lo-event-loc={$val}";
                                }
                                else
                                {
                                    $link = $page_link['search'] . "?lo-event-loc={$val}";
                                }
                                echo '<li><a href="' . $link . '">' . ucwords($val) . '</a></li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                    </li>
                    <?php
                }
                if ('true' == $this->get('menu_option_days'))
                {
                    ?>
                    <li class="dropdown lo-dropdown-submenu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                           aria-haspopup="true" aria-expanded="false">Days of the Week <span
                                    class="caret"></span></a>
                        <ul class="dropdown-menu lo-dropdown-menu">
                            <?php
                            $weekdays = [
                                'Sunday',
                                'Monday',
                                'Tuesday',
                                'Wednesday',
                                'Thursday',
                                'Friday',
                                'Saturday',
                            ];

                            foreach ($weekdays as $weekday)
                            {
                                if ( ! empty($page_link['page_query_arr']))
                                {
                                    $link = $page_link['search'] . "&lo-event-day={$weekday}";
                                }
                                else
                                {
                                    $link = $page_link['search'] . "?lo-event-day={$weekday}";
                                }
                                echo '<li><a href="' . $link . '">' . $weekday . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
                if ('true' == $this->get('menu_option_partners'))
                {
                    ?>
                    <li>
                        <?php
                        $page_settings = get_option('liquid_outreach_ccb_events_page_settings');
                        $slug_base     = ! empty($page_settings['lo_events_page_permalink_base']) ? $page_settings['lo_events_page_permalink_base'] . '/partners' : 'partners';
                        if ( ! empty($page_link['page_query_arr']))
                        {
                            $link = $slug_base . '?' . http_build_query($page_link['page_query_arr']);
                        }
                        else
                        {
                            $link = $slug_base;
                        }
                        ?>
                         <a href="<?php echo home_url($link) ?>">Partner Organizations</a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>