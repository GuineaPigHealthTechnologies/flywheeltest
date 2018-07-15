<?php
get_header();
$show_default_title = get_post_meta( get_the_ID(), '_et_pb_show_title', true );
$is_page_builder_used = et_pb_is_pagebuilder_used( get_the_ID() );
?>

<div id="main-content">
	<?php
		if ( et_builder_is_product_tour_enabled() ) {
			// load fullwidth page in Product Tour mode
			while ( have_posts() ) { the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<div class="entry-content">
					<?php
						the_content();
					?>
					</div> <!-- .entry-content -->

				</article> <!-- .et_pb_post -->

		<?php }
		} else {
	?>

	<div class="container">
		<div id="content-area" class="clearfix">
			<div id="left-area" <?php if (! $is_page_builder_used) echo 'class="pb_disabled"'; ?>>
			<?php while ( have_posts() ) : the_post(); ?>
				<?php if (et_get_option('divi_integration_single_top') <> '' && et_get_option('divi_integrate_singletop_enable') == 'on') echo(et_get_option('divi_integration_single_top')); ?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post' ); ?>>
					<?php if ( ( 'off' !== $show_default_title && $is_page_builder_used ) || ! $is_page_builder_used ) { ?>
						
						<?php				
						$thumb = '';
						$width = (int) apply_filters( 'et_pb_index_blog_image_width', 1080 );
						$height = (int) apply_filters( 'et_pb_index_blog_image_height', 675 );
						$classtext = 'et_featured_image';
						$titletext = get_the_title();
						$thumbnail = get_thumbnail( $width, $height, $classtext, $titletext, $titletext, false, 'Blogimage' );
						$thumb = $thumbnail["thumb"];
						$post_format = et_pb_post_format();

						if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) {
							printf(
								'<div class="et_main_video_container">
									%1$s
								</div>',
								$first_video
							);
						} else if ( ! in_array( $post_format, array( 'gallery', 'link', 'quote' ) ) && 'on' === et_get_option( 'divi_thumbnails', 'on' ) && '' !== $thumb ) {
							echo '<div class="et_main_thumbnail_container">';
								print_thumbnail( $thumb, $thumbnail["use_timthumb"], $titletext, $width, $height );
							echo '</div>';
						} else if ( 'gallery' === $post_format ) {
							et_pb_gallery_images();
						}
					?>

					<?php
						$text_color_class = et_divi_get_post_text_color();
						$inline_style = et_divi_get_post_bg_inline_style();
						switch ( $post_format ) {
							case 'audio' :
								$audio_player = et_pb_get_audio_player();
								if ( $audio_player ) {
									printf(
										'<div class="et_audio_content%1$s"%2$s>%3$s</div>',
										esc_attr( $text_color_class ),
										$inline_style,
										$audio_player
									);
								}

								break;
							case 'quote' :
								printf(
									'<div class="et_quote_content%2$s"%3$s>%1$s</div> <!-- .et_quote_content -->',
									et_get_blockquote_in_content(),
									esc_attr( $text_color_class ),
									$inline_style
								);
								break;
							case 'link' :
								printf(
									'<div class="et_link_content%3$s"%4$s><a href="%1$s" class="et_link_main_url">%2$s</a></div> <!-- .et_link_content -->',
									esc_url( et_get_link_url() ),
									esc_html( et_get_link_url() ),
									esc_attr( $text_color_class ),
									$inline_style
								);
								break;
						} 
						?>
					 
					<div class="post_title_wrapper"> 
						<h2 class="entry-title"><?php the_title(); ?></h2>
						<?php et_divi_post_meta();?> 
					</div> <!-- .et_post_meta_wrapper -->
					<?php  } ?>

					<div class="entry-content">
					<?php
						do_action( 'et_before_content' );
						the_content();
						wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
					?>
					</div> <!-- .entry-content -->

					<!-- Show ads -->
					<div class="et_post_meta_wrapper">
					<?php
					if ( et_get_option('divi_468_enable') == 'on' ){
						echo '<div class="et-single-post-ad">';
						if ( et_get_option('divi_468_adsense') <> '' ) echo( et_get_option('divi_468_adsense') );
						else { ?>
							<a href="<?php echo esc_url(et_get_option('divi_468_url')); ?>"><img src="<?php echo esc_attr(et_get_option('divi_468_image')); ?>" alt="468" class="foursixeight" /></a>
				<?php 	}
						echo '</div> <!-- .et-single-post-ad -->';
					}
				?>

					<?php if (et_get_option('divi_integration_single_bottom') <> '' && et_get_option('divi_integrate_singlebottom_enable') == 'on') echo(et_get_option('divi_integration_single_bottom')); ?>

					</div> <!-- .et_post_meta_wrapper -->
				</article> <!-- .et_pb_post -->

				<!-- POST NAVIGATION -->
				<div class="post-navigation">  
					<div class="navi-content">
						<?php previous_post_link('<div class="post-navigation-previous">%link', '%title</div>'); ?> 
						<?php next_post_link('<div class="post-navigation-next">%link', '%title</div>'); ?>
					</div> 
				</div>

				<!-- COMMENT AREA -->
				<?php
					if ( ( comments_open() || get_comments_number() ) && 'on' == et_get_option( 'divi_show_postcomments', 'on' ) ) { 
						comments_template( '', true );  }
				?>

			<?php endwhile; ?>
			</div> <!-- #left-area -->

			<?php get_sidebar(); ?> 
		</div> <!-- #content-area -->
	</div> <!-- .container -->

	<!-- RELATED POSTS BY CATEGORY --> 
	<?php 
	$orig_post = $post;
	global $post; 
	$categories = get_the_category($post->ID);
	if ($categories) {
	$category_ids = array();
	foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
	$args=array(
	'category__in' => $category_ids,
	'post__not_in' => array($post->ID),
	'posts_per_page'=> 3, // Number of related posts that will be shown.
	'ignore_sticky_posts' => true
	);
	$my_query = new wp_query( $args );
	if( $my_query->have_posts() ) {
	echo '<div class="related-posts"><div class="et_pb_row" style="padding: 0 !important;"><h1>Related Posts</h1>';
	while( $my_query->have_posts() ) {
	$my_query->the_post();?>
		<div class="related-thumb">
			<div class="related-thumb-wrapper" >
				<div class="thumb-container" style="background-image: url(<?php echo the_post_thumbnail_url();?>)">  
					<a rel="external" href="<?php the_permalink(); ?>"></a>
				</div>
				<div class="related-post-content">
					<a rel="external" href="<?php the_permalink(); ?>">
						<h3 class="post-title">
							<?php
							$thetitle = $post->post_title; 
							$getlength = strlen($thetitle);
							$thelength = 45;
							echo  substr($thetitle, 0, $thelength)  ;
							if ($getlength > $thelength) echo "...";
							?>
						</h3>
					</a>
					<a rel="external" href="<?php the_permalink(); ?>" class="more-link">read more</a>
				</div>
			</div>
		</div>
	<?php
	}
	echo '</div></div>';
	}
	}
	$post = $orig_post;
	wp_reset_query(); ?> 

	<?php } ?>
</div> <!-- #main-content -->

<?php get_footer(); ?>