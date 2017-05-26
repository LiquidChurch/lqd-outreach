<?php
$disable = $this->get('disable');

if (!isset($disable['header']) || !$disable['header']) {
    LO_Template_Loader::output_template( 'header' );
}

if (!isset($disable['nav']) || !$disable['nav']) {
    LO_Template_Loader::output_template( 'nav', [
        'categories' => $this->get( 'categories' ),
        'cities'     => $this->get( 'cities' ),
    ] );
}
?>

<!--Search field-->
<div class="lo-full">

    <?php
    if (!isset($disable['search']) || !$disable['search']) {
        LO_Template_Loader::output_template( 'search-form', [
            'categories' => $this->get( 'categories' ),
            'partners'   => $this->get( 'partners' ),
            'cities'     => $this->get( 'cities' ),
        ] );
    }
    ?>

    <!-- Page Content -->
<!--	--><?php //LO_Template_Loader::output_template( 'breadcrumb', [
//	] ) ?>
    
    <!-- /.container -->
	<?php LO_Template_Loader::output_template( 'event-single', [
		'post'     => $this->get( 'post' ),
	] ) ?>
</div>