<!-- header -->
<?php LO_Template_Loader::output_template( 'header' ) ?>

<!-- /.container -->
<!-- Navigation -->
<?php LO_Template_Loader::output_template( 'nav', [
	'categories' => $this->get( 'categories' ),
	'cities'     => $this->get( 'cities' ),
] ) ?>

<!--Search field-->
<div class="lo-full">

    <!--search-form-->
	<?php LO_Template_Loader::output_template( 'search-form', [
		'categories' => $this->get( 'categories' ),
		'partners'   => $this->get( 'partners' ),
		'cities'     => $this->get( 'cities' ),
	] ) ?>


    <!-- Page Content -->
<!--	--><?php //LO_Template_Loader::output_template( 'breadcrumb', [
//	] ) ?>
    
    <!-- /.container -->
	<?php LO_Template_Loader::output_template( 'event-single', [
		'post'     => $this->get( 'post' ),
	] ) ?>
</div>