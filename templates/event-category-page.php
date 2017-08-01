<?php
$disable = $this->get('disable');
$page_link = $this->get('page_link');

if(!isset($disable['header']) || !$disable['header']) {
    LO_Template_Loader::output_template('header');
}

if(!isset($disable['nav']) || !$disable['nav']) {
    LO_Template_Loader::output_template('nav', [
        'categories' => $this->get('categories'),
        'cities' => $this->get('cities'),
        'page_link' => $page_link,
    ]);
}
?>

<!--Search field-->
<div class="lo-full">

    <div id="lo-filter-panel" class="lo-filter-panel">

        <!--search-form-->
        <?php
        if(!isset($disable['search']) || !$disable['search']) {
            LO_Template_Loader::output_template('search-form', [
                'categories' => $this->get('categories'),
                'partners' => $this->get('partners'),
                'cities' => $this->get('cities'),
                'page_link' => $page_link,
            ]);
        }
        ?>

        <!--category-list-->
        <?php LO_Template_Loader::output_template('event-category-list', array(
            'categories' => $this->get('categories'),
            'disable' => $disable,
            'page_link' => $page_link,
        )) ?>

    </div>

</div>