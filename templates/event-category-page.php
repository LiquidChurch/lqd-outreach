<!-- header -->
<?php LO_Template_Loader::output_template('header') ?>

<!-- /.container -->
<!-- Navigation -->
<?php LO_Template_Loader::output_template('nav', [
    'categories' => $this->get('categories'),
    'cities' => $this->get('cities'),
]) ?>

<!--Search field-->
<div class="lo-full">

    <div id="lo-filter-panel" class="lo-filter-panel">

        <!--search-form-->
        <?php LO_Template_Loader::output_template('search-form', [
            'categories' => $this->get('categories'),
            'partners' => $this->get('partners'),
            'cities' => $this->get('cities'),
        ]) ?>

        <!--category-list-->
        <?php LO_Template_Loader::output_template('event-category-list', array(
            'categories' => $this->get('categories'),
        )) ?>

    </div>

</div>