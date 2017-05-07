<!-- Page Content -->
<div class="lo-full">
    <img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/header_c.jpg' ?>" width="100%"
         height="300px;">
</div>
<!-- /.container -->
<!-- Navigation -->
<nav class="navbar navbar-default lo-nav-custom" role="navigation">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="lo-navbar-toggle navbar-toggle collapsed" data-toggle="collapse"
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
                    <a href="#">Search Projects</a>
                </li>
                <li>
                    <a href="#">Project Categories</a>
                </li>
                <li>
                    <a href="#">Projects by City</a>
                </li>
                <li>
                    <a href="#">Days of the Week</a>
                </li>
                <li>
                    <a href="#">Partner Organizations</a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<!--Search field-->
<div class="lo-full">
    <div id="lo-filter-panel" class="lo-filter-panel">
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
                        <div class="form-group col-sm-12">
                            <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                                   style="margin-right:0;" for="lo-event-ptype">Project
                                Types</label>
                            <div class="col-sm-8">
                                <select id="lo-event-ptype" name="lo-event-ptype"
                                        class="form-control">
                                    <option value="all">All Project Types</option>
                                    <option value="all">Project Category</option>
                                    <option value="all">Project Location</option>
                                    <option value="all">Project Day</option>
                                    <option value="all">Project Organization</option>
                                </select>
                            </div>
                        </div> <!-- form group [order by] -->
                        <div class="form-group col-sm-12">
                            <label class="lo-filter-col col-sm-2 control-label lo-text-align-left"
                                   style="margin-right:0;" for="lo-event-org">Organizations</label>
                            <div class="col-sm-8">
                                <select id="lo-event-org" name="lo-event-org" class="form-control">
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
                                <select id="lo-event-day" name="lo-event-day" class="form-control">
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
                                <select id="lo-event-loc" name="lo-event-loc" class="form-control">
                                    <option value="all">Project Category</option>
                                    <option>Project Location</option>
                                    <option>Project Day</option>
                                    <option>Project Organization</option>
                                </select>
                            </div>
                        </div> <!-- form group [order by] -->
                    </div>
                </form>
            </div>
        </div>


        <!-- Page Content -->
        <div class="container">
            <div class="row">
                <h1>Select a Project Category</h1>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="lo-f1-container">
                        <div class="lo-f1-card">
                            <div class="lo-front lo-face">
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"/>
                                <span class="lo-project-heading">Hands On</span>
                            </div>
                            <div class="lo-back lo-face center">
                                <p><span class="lo-project-heading">Hands On</span></p>
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"
                                     width="75px"/>
                                <p>These Projects just need a willing pair of hands.</p>
                                <a href="#" class="lo-view-proj-num">View Projects</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="lo-f1-container">
                        <div class="lo-f1-card">
                            <div class="lo-front lo-face">
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"/>
                                <span class="lo-project-heading">Serve Meals</span>
                            </div>
                            <div class="lo-back lo-face center">
                                <p><span class="lo-project-heading">Hands On</span></p>
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"
                                     width="75px"/>
                                <p>These Projects just need a willing pair of hands.</p>
                                <a href="#" class="lo-view-proj-num">View Projects</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="lo-f1-container">
                        <div class="lo-f1-card">
                            <div class="lo-front lo-face">
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"/>
                                <span class="lo-project-heading">Donations</span>
                            </div>
                            <div class="lo-back lo-face center">
                                <p><span class="lo-project-heading">Hands On</span></p>
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"
                                     width="75px"/>
                                <p>These Projects just need a willing pair of hands.</p>
                                <a href="#" class="lo-view-proj-num">View Projects</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="lo-f1-container">
                        <div class="lo-f1-card">
                            <div class="lo-front lo-face">
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"/>
                                <span class="lo-project-heading">Family Friendly</span>
                            </div>
                            <div class="lo-back lo-face center">
                                <p><span class="lo-project-heading">Hands On</span></p>
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"
                                     width="75px"/>
                                <p>These Projects just need a willing pair of hands.</p>
                                <a href="#" class="lo-view-proj-num">View Projects</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="lo-f1-container">
                        <div class="lo-f1-card">
                            <div class="lo-front lo-face">
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"/>
                                <span class="lo-project-heading">Individual Options</span>
                            </div>
                            <div class="lo-back lo-face center">
                                <p><span class="lo-project-heading">Hands On</span></p>
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"
                                     width="75px"/>
                                <p>These Projects just need a willing pair of hands.</p>
                                <a href="#" class="lo-view-proj-num">View Projects</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-sm-12">
                    <div class="lo-f1-container">
                        <div class="lo-f1-card">
                            <div class="lo-front lo-face">
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"/>
                                <span class="lo-project-heading">Urgent Needs</span>
                            </div>
                            <div class="lo-back lo-face center">
                                <p><span class="lo-project-heading">Hands On</span></p>
                                <img src="<?php echo Liquid_Outreach::$url .
								                     '/assets/css/img/HandsOn.png' ?>"
                                     width="75px"/>
                                <p>These Projects just need a willing pair of hands.</p>
                                <a href="#" class="lo-view-proj-num">View Projects</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
</div>