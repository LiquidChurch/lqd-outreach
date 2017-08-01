<?php
    $categories = $this->get('categories');
    $disable    = $this->get('disable');
    $page_link  = $this->get('page_link');

    if ( ! isset($disable['animation']) || ! $disable['animation'])
    {
        ?>
        <style type="text/css">
            .lo-f1-container:hover .lo-f1-card {
                transform: none !important;
            }
        </style>
        <?php
    }
?>
<!-- Page Content -->
<div class="container">
    <div class="row">
        <h1>Select a Project Category</h1>
    </div>
    <div class="row">

        <?php
            if ( ! empty($categories))
            {
                foreach ($categories as $index => $category)
                {
                    $link = $category->term_link;
                    if ( ! empty($page_link['page_query_arr']))
                    {
                        $link = $category->term_link . '?' . http_build_query($page_link['page_query_arr']);
                    }
                    ?>

                    <div class="col-md-4 col-sm-12">
                        <div class="lo-f1-container" onclick="javascript:window.location = '<?php echo $link ?>'">
                            <div class="lo-f1-card">
                                <div class="lo-front lo-face">
                                    <?php
                                        if ( ! empty($category->image_url))
                                        {
                                            echo '<img src="' . $category->image_url . '" alt="Category Image" width="125" />';
                                        }
                                    ?>
                                    <span class="lo-project-heading"><?php echo $category->name ?></span>
                                </div>
                                <div class="lo-back lo-face center">
                                    <p><span class="lo-project-heading"><?php echo $category->name ?></span></p>
                                    <?php
                                        if ( ! empty($category->image_url))
                                        {
                                            echo '<img src="' . $category->image_url . '" alt="Category Image" width="35" style="margin-bottom:5px;" />';
                                        }
                                    ?>
                                    <p><?php echo substr($category->description, 0, 150) . '...' ?></p>
                                    <a href="<?php echo $link ?>" class="lo-view-proj-num">View Projects</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                }
            }
        ?>

    </div>
    <!-- /.row -->
</div>
<!-- /.container -->