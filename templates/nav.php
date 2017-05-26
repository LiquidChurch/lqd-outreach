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
                <li>
                    <a href="<?php echo get_permalink(get_page_by_path('projects')) ?>">Projects</a>
                </li>
                <li>
                    <a href="<?php echo get_permalink(get_page_by_path('search-projects')) ?>">Search
                        Projects</a>
                </li>
                <li class="dropdown lo-dropdown-submenu">
                    <a href="<?php echo get_permalink(get_page_by_path('project-categories')) ?>"
                       onclick="location.href = '<?php echo get_permalink(get_page_by_path('project-categories')) ?>'"
                       class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false">Project Categories <span
                                class="caret"></span></a>
                    <?php
                    if (!empty($this->get('categories'))) {
                        echo '<ul class="dropdown-menu lo-dropdown-menu">';
                        foreach ($this->get('categories') as $val) {
                            echo '<li><a href="' . $val->term_link . '">' . $val->name .
                                '</a></li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </li>
                <li class="dropdown lo-dropdown-submenu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false">Projects by City <span
                                class="caret"></span></a>
                    <?php
                    if (!empty($this->get('cities'))) {
                        echo '<ul class="dropdown-menu lo-dropdown-menu">';
                        foreach ($this->get('cities') as $val) {
                            echo '<li><a href="' .
                                get_permalink(get_page_by_path('search-projects')) .
                                '?lo-event-loc=' . $val . '">' .
                                ucwords($val) . '</a></li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </li>
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

                        foreach ($weekdays as $weekday) {
                            echo '<li><a href="' .
                                get_permalink(get_page_by_path('search-projects')) .
                                '?lo-event-day=' . $weekday . '">' . $weekday . '</a></li>';
                        }
                        ?>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo home_url('outreach-partners') ?>">Partner Organizations</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>