<!-- Page Content -->
<div class="full">
    <img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/header_c.jpg' ?>" width="100%"
         height="300px;">
</div>
<!-- /.container -->
<!-- Navigation -->
<nav class="navbar nav-custom" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Liquid Church</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
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
<div id="filter-panel" class="container filter-panel">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="" role="form">
                <div class="form-horizontal">
                    <div class="form-group col-sm-12">
                        <label class="filter-col col-sm-2 control-label text-align-left event-search-label"
                               style="margin-right:0;" for="pref-orderby">Search</label>
                        <div class="col-sm-8 event-search-input">
                            <input type="text" class="form-control" id="">
                        </div>
                        <div class="form-group col-sm-2 text-center">
                            <button type="submit"
                                    class="btn btn-success filter-col event-search-btn">
                                <span class="glyphicon glyphicon-search"></span> Search
                            </button>
                        </div>
                    </div> <!-- form group [order by] -->
                </div>

                <hr/>

                <div class="form-horizontal">
                    <div class="form-group col-sm-12">
                        <label class="filter-col col-sm-2 control-label text-align-left"
                               style="margin-right:0;" for="pref-orderby">Project
                            Types</label>
                        <div class="col-sm-8">
                            <select id="pref-orderby" class="form-control">
                                <option>Project Category</option>
                                <option>Project Location</option>
                                <option>Project Day</option>
                                <option>Project Organization</option>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="filter-col col-sm-2 control-label text-align-left"
                               style="margin-right:0;" for="pref-orderby">Organizations</label>
                        <div class="col-sm-8">
                            <select id="pref-orderby" class="form-control">
                                <option>Project Category</option>
                                <option>Project Location</option>
                                <option>Project Day</option>
                                <option>Project Organization</option>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="filter-col col-sm-2 control-label text-align-left"
                               style="margin-right:0;"
                               for="pref-orderby">Days</label>
                        <div class="col-sm-8">
                            <select id="pref-orderby" class="form-control">
                                <option>Project Category</option>
                                <option>Project Location</option>
                                <option>Project Day</option>
                                <option>Project Organization</option>
                            </select>
                        </div>
                    </div> <!-- form group [order by] -->
                    <div class="form-group col-sm-12">
                        <label class="filter-col col-sm-2 control-label text-align-left"
                               style="margin-right:0;" for="pref-orderby">Locations</label>
                        <div class="col-sm-8">
                            <select id="pref-orderby" class="form-control">
                                <option>Project Category</option>
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
    </div>


    <!-- Page Content -->
    <div class="container">
        <div class="row">
            <h1>Select a Project Category</h1>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-12">
                <div id="f1_container">
                    <div id="f1_card" class="shadow">
                        <div class="front face">
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>"/>
                            <span class="project-heading">Hands On</span>
                        </div>
                        <div class="back face center">
                            <p><span class="project-heading">Hands On</span></p>
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>" width="75px"/>
                            <p>These Projects just need a willing pair of hands.</p>
                            <a href="#" class="view-proj-num">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div id="f1_container">
                    <div id="f1_card" class="shadow">
                        <div class="front face">
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>"/>
                            <span class="project-heading">Serve Meals</span>
                        </div>
                        <div class="back face center">
                            <p><span class="project-heading">Hands On</span></p>
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>" width="75px"/>
                            <p>These Projects just need a willing pair of hands.</p>
                            <a href="#" class="view-proj-num">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div id="f1_container">
                    <div id="f1_card" class="shadow">
                        <div class="front face">
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>"/>
                            <span class="project-heading">Donations</span>
                        </div>
                        <div class="back face center">
                            <p><span class="project-heading">Hands On</span></p>
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>" width="75px"/>
                            <p>These Projects just need a willing pair of hands.</p>
                            <a href="#" class="view-proj-num">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div id="f1_container">
                    <div id="f1_card" class="shadow">
                        <div class="front face">
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>"/>
                            <span class="project-heading">Family Friendly</span>
                        </div>
                        <div class="back face center">
                            <p><span class="project-heading">Hands On</span></p>
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>" width="75px"/>
                            <p>These Projects just need a willing pair of hands.</p>
                            <a href="#" class="view-proj-num">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div id="f1_container">
                    <div id="f1_card" class="shadow">
                        <div class="front face">
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>"/>
                            <span class="project-heading">Individual Options</span>
                        </div>
                        <div class="back face center">
                            <p><span class="project-heading">Hands On</span></p>
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>" width="75px"/>
                            <p>These Projects just need a willing pair of hands.</p>
                            <a href="#" class="view-proj-num">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-sm-12">
                <div id="f1_container">
                    <div id="f1_card" class="shadow">
                        <div class="front face">
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>"/>
                            <span class="project-heading">Urgent Needs</span>
                        </div>
                        <div class="back face center">
                            <p><span class="project-heading">Hands On</span></p>
                            <img src="<?php echo Liquid_Outreach::$url .
							                     '/assets/css/img/HandsOn.png' ?>" width="75px"/>
                            <p>These Projects just need a willing pair of hands.</p>
                            <a href="#" class="view-proj-num">View Projects</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->