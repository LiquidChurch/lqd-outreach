<?php
$disable        = $this->get('disable');
$page_link      = $this->get('page_link');
$force_cat_page = $this->get('force_cat_page');

if ( ! isset($disable['header']) || ! $disable['header'])
{
    LO_Template_Loader::output_template('header', [
        'force_cat_page'         => $this->get('force_cat_page'),
    ]);
}

if ( ! isset($disable['nav']) || ! $disable['nav'])
{
    LO_Template_Loader::output_template('nav', [
        'categories'             => $this->get('categories'),
        'cities'                 => $this->get('cities'),
        'page_link'              => $page_link,
        'force_cat_page'         => $force_cat_page,
        'menu_option_index'      => $this->get('menu_option_index'),
        'menu_option_search'     => $this->get('menu_option_search'),
        'menu_option_categories' => $this->get('menu_option_categories'),
        'menu_option_city'       => $this->get('menu_option_city'),
        'menu_option_days'       => $this->get('menu_option_days'),
        'menu_option_partners'   => $this->get('menu_option_partners'),
        'menu_option_campus'     => $this->get('menu_option_campus'),
    ]);
}
?>

<!--Search field-->
<div class="lo-full">

    <?php
    if ( ! isset($disable['search']) || ! $disable['search'])
    {
        LO_Template_Loader::output_template('search-form', [
            'categories'     => $this->get('categories'),
            'partners'       => $this->get('partners'),
            'cities'         => $this->get('cities'),
            'page_link'      => $page_link,
            'force_cat_page' => $force_cat_page,
        ]);
    }
    ?>

    <!-- Page Content -->
    <!--	--><?php //LO_Template_Loader::output_template( 'breadcrumb', [
    //	] ) ?>

    <!-- /.container -->
    <?php LO_Template_Loader::output_template('event-single', [
        'post'           => $this->get('post'),
        'page_link'      => $page_link,
        'force_cat_page' => $force_cat_page,
    ]) ?>
</div>