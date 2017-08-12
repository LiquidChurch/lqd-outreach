<?php
  // TODO: The search isn't robust, returns results for Halloween but not for Nutley, and there are definitely Nutley events. Why?
   /* $page_link = $this->get('page_link');
?>
<div class="panel panel-default">
    <div class="panel-body">
        <?php
            $lo_events_page_settings = lo_get_option('page', 'all');
            $projects_srch_link      = ! empty($lo_events_page_settings['lo_events_page_lo_search_page'])
                ? get_permalink($lo_events_page_settings['lo_events_page_lo_search_page'])
                : get_permalink(get_page_by_path('search-projects'));
        ?>
        <form action="<?php echo $projects_srch_link ?>" id="lo-event-search-form" method="GET">
            <?php
                if ( ! empty($page_link['page_query_arr']['lo-cat-page']))
                {
                    echo '<input type="hidden" name="lo-cat-page" value="' . $page_link['page_query_arr']['lo-cat-page'] . '" />';
                }
            ?>
            <div class="form-horizontal">
                <div class="form-group col-sm-12">
                    <label class="lo-filter-col col-sm-2 control-label lo-text-align-left lo-event-search-label"
                           style="margin-right:0;" for="pref-orderby">Search</label>
                    <div class="col-sm-8 lo-event-search-input">
                        <input type="text" class="form-control" id="lo-event-s"
                               name="lo-event-s">
                    </div>
                    <div class="form-group col-sm-2 text-center">
                        <button type="submit"
                                class="btn btn-success lo-filter-col lo-event-search-btn">
                            <span class="glyphicon glyphicon-search"></span> Search
                        </button>
                    </div>
                </div> <!-- form group [order by] -->
            </div>

            <hr/>

            <div class="form-horizontal">
                <div id="lo-event-form-advanced-option" class="lo-event-form-advanced-option">
                    <?php
                        if (empty($page_link['page_query_arr']['lo-cat-page']))
                        {
                            ?>
                            <div class="form-group col-sm-12">
                                <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                                       style="margin-right:0;" for="lo-event-cat">Project Types
                                </label>
                                <div class="col-sm-8">
                                    <select id="lo-event-cat" name="lo-event-cat"
                                            class="form-control">
                                        <option value="">All Project Types</option>
                                        <?php
                                            if ( ! empty($this->get('categories')))
                                            {
                                                foreach (
                                                    $this->get('categories') as $index => $category
                                                )
                                                {
                                                    echo '<option value="' . $category->term_id . '">' .
                                                         $category->name . '</option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div> <!-- form group [order by] -->
                            <?php
                        }
                    ?>
                    <div class="form-group col-sm-12">
                        <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                               style="margin-right:0;"
                               for="lo-event-org">Partners</label>
                        <div class="col-sm-8">
                            <select id="lo-event-org" name="lo-event-org"
                                    class="form-control">
                                <option value="">All Project Partners</option>
                                <?php
                                    if ( ! empty($this->get('partners')))
                                    {
                                        foreach (
                                            $this->get('partners') as $index => $partner
                                        )
                                        {
                                            if ($partner instanceof WP_Post)
                                            {

                                                echo '<option value="' . get_post_meta($partner->ID, 'lo_ccb_event_partner_group_id', 1) .
                                                     '">' .
                                                     $partner->post_title . '</option>';
                                            } else
                                            {

                                                echo '<option value="' . $partner->get_meta('lo_ccb_event_partner_group_id') .
                                                     '">' .
                                                     $partner->title() . '</option>';
                                            }

                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                               style="margin-right:0;"
                               for="lo-event-day">Days</label>
                        <div class="col-sm-8">
                            <select id="lo-event-day" name="lo-event-day"
                                    class="form-control">
                                <option value="">All Days</option>
                                <option value="sunday">Sunday</option>
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                               style="margin-right:0;" for="lo-event-loc">Locations</label>
                        <div class="col-sm-8">
                            <select id="lo-event-loc" name="lo-event-loc"
                                    class="form-control">
                                <option value="">All Locations</option>
                                <?php
                                    if ( ! empty($this->get('cities')))
                                    {
                                        echo '<ul class="dropdown-menu lo-dropdown-menu">';
                                        foreach ($this->get('cities') as $val)
                                        {
                                            echo '<option>' . $val . '</option>';
                                        }
                                        echo '</ul>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                </div>
                <div class="form-group col-sm-12" style="margin-bottom: 0;">
                    <div class="col-sm-2 col-sm-offset-10 text-center">
                                <span id="lo-event-form-advanced-option-btn"
                                      class="lo-event-form-advanced-option-btn">Show Advanced Options</span>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
   */