<?php if( false != get_theme_mod( 'dc_enable_custom_top' ) ) { ?>
<div class="dc-custom-top-section">
<?php $id = get_option('dc_custom_top_layout'); $p = get_page($id); echo apply_filters('the_content', $p->post_content); ?>
</div>
<?php } ?> 
<?php $text = get_post_meta( $post->ID,'layout_selector',true );
				 if ($text == "deflyo") {
					 ?>
					 <div class="defaultwoo">
					 <?php
						do_action( 'woocommerce_before_main_content' );
	?>
	
		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
		?>
		</div>
		<?php			} elseif ($text == "deffllyo") {
			?> <div class="defaultfulltabs">
					 <?php

						do_action( 'woocommerce_before_main_content' );
	?>
	
		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		
		do_action( 'woocommerce_sidebar' );
		?>
		</div>
												
<?php	} elseif ($text == "fullyo") { ?> 
	<div id="main-content" class="product dcdivifulllayout">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="entry-content">
			<?php
				the_content();
			?>
			</div>
		</article>
<?php endwhile; ?>
	</div>
<? 
} else {?>
	<div class="defaultwoo">
					 <?php
						do_action( 'woocommerce_before_main_content' );
	?>
	
		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>
<?php if( false != get_theme_mod( 'dc_enable_custom_bottom' ) ) { ?>
<div class="dc-custom-top-section">
<?php $id = get_option('dc_custom_bottom_layout'); $p = get_page($id); echo apply_filters('the_content', $p->post_content); ?>
</div>
<?php } ?> 
	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
		?>
		</div>
		<?php	
					}
					?>
					<?php if( false != get_theme_mod( 'dc_enable_custom_bottom' ) ) { ?>
<div class="dc-custom-top-section">
<?php $id = get_option('dc_custom_bottom_layout'); $p = get_page($id); echo apply_filters('the_content', $p->post_content); ?>
</div>

<?php } ?> 
<?php
/*$post   = get_page( 2 );
$output = echo apply_filters( 'the_content', $post->post_content );*/
?>
</div> 