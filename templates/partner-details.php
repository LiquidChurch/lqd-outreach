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
	<div class="row no-margin">
		<!--Details column-->
		<div class="col-md-8 panel panel-custom">
			<span class="search-heading">Aegis Living at Shadowridge - Aegis Senior Living Home</span>
			<hr/>
			<p class="description">
			<div class="col-md-4">
				<img src="<?php echo Liquid_Outreach::$url . '/assets/css/img/AegisLiving-250x250.jpg' ?>" width="100%"/>
			</div>
			<div class="col-md-8">
				<p>Aegis at Shadowridge is a cozy home-like community known for extraordinary care with services tailored to meet their residents changing physical and cognitive needs.  They offer a delightful outdoor courtyard with water fountains and well-manicured gardens that serve as a relaxing retreat and a venue for a wide variety of day and evening activities for their residents.</p>
				<p>Take a few hours out of your day and share it with these seniors.  They LOVE to interact with individuals of all ages, especially young families with children.  Love on them by listening to their unique life experiences, sit and play games with them, flip through photo albums or sign up for one of our projects below.</p>
			</div>
			</p>
		
		</div>
		<!--addtnal info column-->
		<div class="col-md-4">
			<hr/>
			<strong>Organization Information</strong>
			<hr/>
			<div class="row">
				<div class="col-md-5 add-info-label">Address</div>
				<div class="col-md-1">&#8594 </div>
				<div class="col-md-6">some address</div>
			</div>
			<div class="row">
				<div class="col-md-5 add-info-label">Website</div>
				<div class="col-md-1">&#8594 </div>
				<div class="col-md-6"><a href="#">www.xyz.com</a></div>
			</div>
			<!--team leader block-->
			<div class="row">
				<div class="col-md-5 add-info-label">Team Leader</div>
				<div class="col-md-7 add-info-label"><br/></div>
				
				<div class="col-md-5 add-info-label-2">Name</div>
				<div class="col-md-1">&#8594 </div>
				<div class="col-md-6">John Doe</div>
				
				<div class="col-md-5 add-info-label-2">Email</div>
				<div class="col-md-1">&#8594 </div>
				<div class="col-md-6">JohnDoe@gmail.co.uk</div>
				
				<div class="col-md-5 add-info-label-2">Phone</div>
				<div class="col-md-1">&#8594 </div>
				<div class="col-md-6">+44-225-566</div>
			</div>
			<!--/team leader block-->
		</div>
	</div><!--/.row-->
	
	<div class="row">
		<div class="col-md-12">
			<h4>Upcoming Projects</h4>
			<!--Tables result for search-->
			<table class="VanderTable">
				<thead>
				<tr>
					<th>Date</th>
					<th class="project-table-head">Project</th>
					<th>Category</th>
					<th>Day(s)</th>
					<th>Location</th>
					<th>Openings</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td>24/12/2017</td>
					<td>Get help for my kids tuition,Get help for my kids tuition,Get help for my kids tuition</td>
					<td>hands on</td>
					<td>12</td>
					<td>carolina</td>
					<td>Closed</td>
				</tr>
				<tr>
					<td>23/12/2017</td>
					<td>Get help for my kids tuition</td>
					<td>hands on</td>
					<td>8</td>
					<td>carolina</td>
					<td>open</td>
				</tr>
				<tr>
					<td>21/12/2017</td>
					<td>Get help for my kids tuition</td>
					<td>hands on</td>
					<td>5</td>
					<td>carolina</td>
					<td>open</td>
				</tr>
				<tr>
					<td>26/12/2017</td>
					<td>Get help for my kids tuition</td>
					<td>hands on</td>
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