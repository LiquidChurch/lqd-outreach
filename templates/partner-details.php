<?php
$disable = $this->get('disable');

if (!isset($disable['header']) || !$disable['header']) {
    LO_Template_Loader::output_template('header');
}

if (!isset($disable['nav']) || !$disable['nav']) {
    LO_Template_Loader::output_template('nav', [
        'categories' => $this->get('categories'),
        'cities' => $this->get('cities'),
        'page_link' => $this->get('page_link'),
    ]);
}
?>

<!--Search field-->
<div class="lo-full">

    <?php
    if (!isset($disable['search']) || !$disable['search']) {
        LO_Template_Loader::output_template('search-form', [
            'categories' => $this->get('categories'),
            'partners' => $this->get('partners'),
            'cities' => $this->get('cities'),
        ]);
    }
    ?>

    <!-- Page Content -->
    <!--	--><?php //LO_Template_Loader::output_template( 'breadcrumb', [
    //	] ) ?>

    <!-- /.container -->
    <?php
    LO_Template_Loader::output_template('partner-single', [
        'post' => $this->get('post'),
    ]);
    ?>
</div>

<div class="lo-full panel lo-panel-custom" style="margin-top: 30px;">
        <!--event-list-->
        <?php LO_Template_Loader::output_template('event-list', 'event-list', [
            'events' => $this->get('events'),
            'event_empty_msg' => $this->get('event_empty_msg'),
            'pagination' => $this->get('pagination')
        ]) ?>
</div>
