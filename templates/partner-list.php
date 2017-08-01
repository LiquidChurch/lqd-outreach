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

<!-- Page Content -->
<div class="container">
    <div class="row">
        <h1 style="margin-top: 30px; margin-bottom: 0;">Partner Organizations</h1>
    </div>

    <?php
    $partners = $this->get('partners');
    if (!empty($partners)) {
        $count = 1;
        foreach ($partners as $partner) {
            if ($count % 6 == 1) {
                echo '<div class="row">';
            }
            ?>
            <a href="<?php echo get_permalink($partner->ID) ?>">
                <div class="col-md-2 lo-city-holder-box">
                    <div class="lo-city-name"><?php echo $partner->post_title ?></div>
                </div>
            </a>
            <?php
            if ($count % 6 == 0) {
                echo '</div>';
            }
            $count++;
        }
    }
    ?>
    <!-- /.row -->
</div>
<!-- /.container -->