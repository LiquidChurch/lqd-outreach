<!-- Page Content -->
<div class="full">
	<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/header_c.jpg' ?>" width="100%" height="300px;">
</div>
<!-- /.container -->
<!-- Navigation -->
<nav class="navbar nav-custom" role="navigation">
	<div class="container">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
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
			<form class="form-inline" role="form">
				<!-- form group [rows] -->
				<div class="form-group col-md-6">
					<label class="filter-col" style="margin-right:0;" for="pref-search">Search:</label>
					<input type="text" class="form-control input-sm" id="pref-search">
				</div><!-- form group [search] -->
				<div class="form-group col-md-4">
					<label class="filter-col" style="margin-right:0;" for="pref-orderby">Search by:</label>
					<select id="pref-orderby" class="form-control">
						<option>Project Category</option>
						<option>Project Location</option>
						<option>Project Day</option>
						<option>Project Organization</option>
					</select>
				</div> <!-- form group [order by] -->
				<div class="form-group col-md-2">
					<button type="submit" class="btn btn-success filter-col">
						<span class="glyphicon glyphicon-search"></span> Search
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Page Content -->
<div class="container">
	<div class="row">
		<div class="col-md-1">
			<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="75px"/>
		</div>
		
		<div class="col-md-11">
			<span class="search-heading">Hands On</span>
			<ol class="breadcrumb breadcrumb-arrow">
				<li><a href="#">Home</a></li>
				<li><a href="#">Category</a></li>
				<li class="active"><span>Upcoming Projects</span></li>
			</ol>
		</div>
	</div>
	<!-- /.row -->
</div>
<!-- /.container -->
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<!--Tables result for search-->
			<table class="VanderTable">
				<thead>
				<tr>
					<th>Date</th>
					<th>Project</th>
					<th>Category</th>
					<th>Day(s)</th>
					<th>Location</th>
					<th>Openings</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>
					<div class="lo-date-cal">
						<div class="lo-month">May</div>
						<div class="lo-date">7</div>
					</div>
					</td>
					<td>Get help for my kids tuition</td>
					<td>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					</td>
					<td>12</td>
					<td>carolina</td>
					<td>Closed</td>
				</tr>
				<tr>
					<td>
					<div class="lo-date-cal">
						<div class="lo-month">May</div>
						<div class="lo-date">7</div>
					</div>
					</td>
					<td>Get help for my kids tuition</td>
					<td>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					</td>
					<td>8</td>
					<td>carolina</td>
					<td>open</td>
				</tr>
				<tr>
					<td>
					<div class="lo-date-cal">
						<div class="lo-month">May</div>
						<div class="lo-date">7</div>
					</div>
					</td>
					<td>Get help for my kids tuition</td>
					<td>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					</td>
					<td>5</td>
					<td>carolina</td>
					<td>open</td>
				</tr>
				<tr>
					<td>
					<div class="lo-date-cal">
						<div class="lo-month">May</div>
						<div class="lo-date">7</div>
					</div>
					</td>
					<td>Get help for my kids tuition</td>
					<td>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					<div class="lo-cat-img">
						<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/HandsOn.png' ?>" width="25px"/>
					</div>
					</td>
					<td>6</td>
					<td>carolina</td>
					<td>open</td>
				</tr>
				
				</tbody>
			</table>
		</div>
		<div class="col-lg-12">
			<div class="page-nation">
				<ul class="pagination pagination-large">
					<li class="disabled"><span>«</span></li>
					<li class="active"><span>1</span></li>
					<li><a href="#">2</a></li>
					<li><a href="#">3</a></li>
					<li><a href="#">4</a></li>
					<li><a href="#">7</a></li>
					<li><a href="#">8</a></li>
					<li><a href="#">9</a></li>
					<li class="disabled"><span>...</span></li><li>
					<li><a rel="next" href="#">Next</a></li>
				
				</ul>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo Liquid_Outreach::$url . '/assets/js/vandertable.js' ?>"></script>
<script src="<?php echo Liquid_Outreach::$url . '/assets/js/index.js' ?>"></script>