<?php
	$categories = $this->get( 'categories' );
?>
<!--Category lists small-->
<div class="row">
    <div class="col-md-1">
    </div>
    <div class="col-md-10 text-center">
		<?php
			if ( ! empty( $categories ) ) {
				foreach ( $categories as $index => $category ) {
					?>
                    <div class="lo-cat-button" style="background-color: <?php echo $category->btn_color ?>;">
                        <a href="<?php echo $category->term_link ?>">
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