<?php
	$categories = $this->get( 'categories' );
    $page_link = $this->get( 'page_link' );
?>
<!--Category lists small-->
<div class="row">
    <div class="col-md-1">
    </div>
    <div class="col-md-10 text-center">
		<?php
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $index => $category ) {
                    $link = $category->term_link;
                    if(!empty($page_link['page_query_arr'])) {
                        $link = $category->term_link . '?' . http_build_query($page_link['page_query_arr']);
                    }
					?>
                    <div class="lo-cat-button" style="background-color: <?php echo $category->btn_color ?>;">
                        <a href="<?php echo $link ?>">
                            <span class="lo-icon-cat">
                                <?php echo $category->image ?>
                            </span>
                            <span class="lo-cat-name"><?php echo $category->name ?></span>
                        </a>
                    </div>
					<?php
				}
			}
		?>
    </div>
    <div class="col-md-1">
    </div>
</div>