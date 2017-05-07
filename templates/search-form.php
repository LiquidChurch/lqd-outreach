<div class="panel panel-default">
    <div class="panel-body">
        <form action="" id="lo-event-search-form" method="GET">
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
                    <div class="form-group col-sm-12">
                        <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                               style="margin-right:0;" for="lo-event-ptype">Project
                            Types</label>
                        <div class="col-sm-8">
                            <select id="lo-event-ptype" name="lo-event-ptype"
                                    class="form-control">
                                <option value="">All Project Types</option>
								<?php
									if ( ! empty( $this->get( 'categories' ) ) ) {
										foreach (
											$this->get( 'categories' ) as $index => $category
										) {
											echo '<option value="' . $category->slug . '">' .
											     $category->name . '</option>';
										}
									}
								?>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                               style="margin-right:0;"
                               for="lo-event-org">Organizations</label>
                        <div class="col-sm-8">
                            <select id="lo-event-org" name="lo-event-org"
                                    class="form-control">
                                <option value="all">Project Category</option>
                                <option>Project Category</option>
                                <option>Project Location</option>
                                <option>Project Day</option>
                                <option>Project Organization</option>
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
                                <option value="all">Project Category</option>
                                <option>Project Location</option>
                                <option>Project Day</option>
                                <option>Project Organization</option>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                               style="margin-right:0;" for="lo-event-loc">Locations</label>
                        <div class="col-sm-8">
                            <select id="lo-event-loc" name="lo-event-loc"
                                    class="form-control">
                                <option value="all">Project Category</option>
                                <option>Project Location</option>
                                <option>Project Day</option>
                                <option>Project Organization</option>
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