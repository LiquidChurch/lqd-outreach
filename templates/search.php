<!-- header -->
<?php LO_Template_Loader::output_template( 'header' ) ?>

<!-- /.container -->
<!-- Navigation -->
<?php LO_Template_Loader::output_template( 'nav', [
	'categories' => $this->get( 'categories' ),
	'cities' => $this->get( 'cities' ),
] ) ?>

<!--Search field-->
<div class="lo-full">

    <div id="lo-filter-panel" class="lo-filter-panel">

        <!--search-form-->
		<?php LO_Template_Loader::output_template( 'search-form', [
			'categories' => $this->get( 'categories' ),
			'partners'   => $this->get( 'partners' ),
			'cities' => $this->get( 'cities' ),
		] ) ?>

        <!--event-list-->
		<?php LO_Template_Loader::output_template( 'event-list', 'event-list', [
			'events'     => $this->get( 'events' ),
			'pagination' => $this->get( 'pagination' )
		] ) ?>

        <!--category-list-->
		<?php LO_Template_Loader::output_template( 'event-category-list' ) ?>

    </div>

</div>