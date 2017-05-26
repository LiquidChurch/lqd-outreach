<?php
	/**
	 * The template for displaying pages
	 *
	 * This is the template that displays all pages by default.
	 * Please note that this is the WordPress construct of pages and that
	 * other "pages" on your WordPress site will use a different template.
	 *
	 * @package    WordPress
	 * @subpackage Liquid_Church
	 * @since      Liquid Church 1.0
	 */
	
	get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">

        <article id="" class="">
            <div class="entry-content">
				<?php
					$category_slug = get_queried_object()->slug;;
					echo do_shortcode( '[lo_event_category_single event_cat_slug=' . $category_slug . ']' );
				?>
            </div>
        </article>

    </main><!-- .site-main -->
	<?php if ( ! is_front_page() ) { ?>
		<?php //get_sidebar( 'content-bottom' ); ?>
	<?php } ?>


</div><!-- .content-area -->

<?php if ( ! is_front_page() ) { ?>
	<?php //get_sidebar(); ?>
<?php } ?>
<?php get_footer(); ?>
