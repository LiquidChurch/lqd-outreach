<!-- header -->
<?php LO_Template_Loader::output_template( 'header' ) ?>

<!-- /.container -->
<!-- Navigation -->
<?php LO_Template_Loader::output_template( 'nav' ) ?>

<!--Search field-->
<div class="lo-full">

    <div id="lo-filter-panel" class="lo-filter-panel">

        <!--search-form-->
		<?php LO_Template_Loader::output_template( 'search-form' ) ?>

        <!--event-list-->
	    <?php LO_Template_Loader::output_template( 'event-list' ) ?>

        <!--category-list-->
		<?php LO_Template_Loader::output_template( 'event-category-list' ) ?>

    </div>

</div>