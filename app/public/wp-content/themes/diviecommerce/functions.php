<?php
function AGS_THEME_updater() {
	include(dirname(__FILE__).'/updater/theme-updater.php');
}
add_action('after_setup_theme', 'AGS_THEME_updater');
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'parent-style' )
	); 
	wp_enqueue_style( 'footer-style', get_stylesheet_directory_uri() . '/css/footer.css' );
	wp_enqueue_style( 'header-style', get_stylesheet_directory_uri() . '/css/header.css' );
	wp_enqueue_style( 'blog-style', get_stylesheet_directory_uri() . '/css/blog.css' );
	wp_enqueue_style( 'homepage-style', get_stylesheet_directory_uri() . '/css/home.css' );
	wp_enqueue_style( 'woocommerce-style', get_stylesheet_directory_uri() . '/css/woocommerce.css' ); 
	wp_enqueue_style( 'colors-style', get_stylesheet_directory_uri() . '/css/colors.css' ); 
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function load_wp_admin_style_theme() {
    wp_enqueue_style('theme_wp_admin_css', get_stylesheet_directory_uri() . '/css/admin.css', '', '1.1', '');
}
add_action('admin_enqueue_scripts', 'load_wp_admin_style_theme');

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');

if (is_admin()) {

	require get_stylesheet_directory() . '/aspen-demo-content/admin-menu.php';

}

require_once dirname(__FILE__) . '/aspen-plugin-installer.php';

add_action('tgmpa_register', 'mytheme_require_plugins');

function mytheme_require_plugins() {
	global $AGS_THEME_updater;
	if (!$AGS_THEME_updater->has_license_key()) {
		return;
	}
	
    $plugins = array(

        
        array(

            'name' => 'Caldera Forms',
            'slug' => 'caldera-forms',
            'required' => false,
            'force_activation' => false,
            'force_deactivation' => false

        ),
        array(

            'name' => 'Woocommerce',
            'slug' => 'woocommerce',
            'required' => false,
            'force_activation' => false,
            'force_deactivation' => false

        ),
        array(

            'name' => 'Woocommerce Services',
            'slug' => 'woocommerce-services',
            'required' => false,
            'force_activation' => false,
            'force_deactivation' => false

        ),
        
        array(

            'name' => 'Instagram Feed',
            'slug' => 'instagram-feed',
            'required' => false,
            'force_activation' => false,
            'force_deactivation' => false

        ),
        
        array(

            'name' => 'Breadcrumb NavXT',
            'slug' => 'breadcrumb-navxt',
            'required' => false,
            'force_activation' => false,
            'force_deactivation' => false

        ),
        array(

            'name' => 'WP-PageNavi',
            'slug' => 'wp-pagenavi',
            'required' => false,
            'force_activation' => false,
            'force_deactivation' => false
        )

    );

    tgmpa($plugins);

}

add_action('template_redirect', 'single_result');

function single_result()

{

    if (is_search()) {

        global $wp_query;

        if ($wp_query->post_count == 1) {

            wp_redirect(get_permalink($wp_query->posts['0']->ID));

        }

    }

}


function replace_howdy($wp_admin_bar)

{

    $my_account = $wp_admin_bar->get_node('my-account');

    $newtitle   = str_replace('Howdy,', 'Welcome,', $my_account->title);

    $wp_admin_bar->add_node(array(

        'id' => 'my-account',

        'title' => $newtitle

    ));

}

add_filter('admin_bar_menu', 'replace_howdy', 25);

function footer_inside_dashboard()

{

    echo 'Thank you for using <a href="http://aspengrovestudios.com/" target="_blank">Divi Ecommerce Child Theme from Aspen Grove Studios </a>';

}

add_filter('admin_footer_text', 'footer_inside_dashboard');

add_action('load-index.php', 'ags_welcome_panel');

function ags_welcome_panel()

{

    $user_id = get_current_user_id();

    if (1 != get_user_meta($user_id, 'ags_welcome_panel', true))

        update_user_meta($user_id, 'ags_welcome_panel', 1);

}

function allow_svgimg_types($mimes)

{

    $mimes['svg'] = 'image/svg+xml';

    return $mimes;

}

add_filter('upload_mimes', 'allow_svgimg_types');


function et_add_diviecommerce_menu()

{

    add_menu_page('Divi Ecommerce', 'Divi Ecommerce', 'switch_themes', 'diviecommerce-options', 'ags_diviecommerce_index');

}
add_action('admin_menu', 'et_add_diviecommerce_menu');

add_action('admin_menu', 'ags_diviecommerce_admin');

function ags_diviecommerce_admin()

{

    add_submenu_page('diviecommerce-options', __('Theme Options', 'Divi'), __('Theme Options', 'Divi'), 'manage_options', 'diviecommerce-options', 'ags_diviecommerce_index');

}

function ags_diviecommerce_featured_post_callback()

{

    echo '<a class="button-primary" href="admin.php?page=ags_demo_installer">Import Demo Data</a>';

}

function ags_diviecommerce_index()
{
	global $AGS_THEME_updater;
	if (!$AGS_THEME_updater->has_license_key()) {
		$AGS_THEME_updater->activate_page();
		return;
	}
?>

    <div class="wrap">  

        <div id="icon-themes" class="icon32"></div>  

        <h2>Divi Ecommerce Theme Options</h2>  

        <?php

    settings_errors();

?> 
        <?php

    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'front_page_options';

?>  
        <h2 class="nav-tab-wrapper">  

            <a href="?page=diviecommerce-options&tab=front_page_options" class="nav-tab <?php

    echo $active_tab == 'front_page_options' ? 'nav-tab-active' : '';

?>">Demo Content</a>  

         <?php do_action('agsx_tabs', 'diviecommerce-options', $active_tab); ?>
		 <a href="?page=diviecommerce-options&tab=license-key" class="nav-tab <?php
    echo $active_tab == 'license-key' ? 'nav-tab-active' : '';
?>">License Key</a>

        </h2>
		<?php if ($active_tab == 'license-key') {
			$AGS_THEME_updater->license_key_box();
		} else { ?>
        <form method="post" action="options.php">  

            <?php

    if ($active_tab == 'front_page_options') {

        settings_fields('ags_diviecommerce_front_page_option');

        do_settings_sections('ags_diviecommerce_front_page_option');

    } else {
		do_action('agsx_tab_content', $active_tab);
	}

?>
        </form> 
	<?php } ?>
    </div> 

<?php

}

add_action('admin_init', 'ags_diviecommerce_options');

function ags_diviecommerce_options() {

	register_setting('ags_diviecommerce_front_page_option', 'ags_diviecommerce_front_page_option');
	
    add_settings_section('ags_diviecommerce_front_page', 'Import Demo Data', 'ags_diviecommerce_front_page_callback', 'ags_diviecommerce_front_page_option');

    add_settings_field('featured_post', '', 'ags_diviecommerce_featured_post_callback', 'ags_diviecommerce_front_page_option', 'ags_diviecommerce_front_page');

}

function ags_diviecommerce_front_page_callback()

{

    echo '<div class="demo_content_options">

	

	<p>Use our built-in demo content tool. This will install the content and the design structure as shown in <a href="http://diviecommerce-ct.aspengrovestudios.space/" target="_blank">this demo</a>. </p>

	

	<span>The items that will be imported are:</span> 

	

	<ul>

	<li>Demo text content</li>

	<li>Placeholder media files</li>

	<li>Navigation Menu</li>

	<li>Demo posts, pages and products</li>

	<li>Site widgets (<em>if applicable</em>)</li>

	</ul>

	<h3>Please note</h3>

	<ol>

	<li>

	No WordPress settings will be imported.</li>

	<li>No existing posts, pages, products, images, categories or any data will be modified or deleted.</li>

	<li>The importer will install only placeholder images showing their usage dimension. You can refer to our demo site and replace the placeholder with your own images.</li>

	</ol>

	</div>';

}

// add Caldera affiliate

add_filter( 'caldera_forms_affiliate_id', 'ags_diviecommerce_caldera_forms_affiliate_id');
function ags_diviecommerce_caldera_forms_affiliate_id() {
    return 57;
} 

/*
 *  02 - Creating shorctodes
 */

function breadcrumbs_shortcode() {
	ob_start();
	if(function_exists('woocommerce_breadcrumb'))  { 
		woocommerce_breadcrumb( $args );
	}
	return ob_get_clean(); 
}
add_shortcode( 'woo-breadcrumbs', 'breadcrumbs_shortcode' );

/*
 * 03 - Register Woocommerce Sidebar
 */

function divi_ecommerce_register_sidebars() { 
    register_sidebar(
        array(
			'id' => 'woocomerce-sidebar',
			'name' => __( 'Woocommerce Sidebar', 'textdomain' ),
			'description' => __( 'This is the WooCommerce shop sidebar', 'divi_ecommerce' ),
			'before_widget' => '<div id="%1$s" class="et_pb_widget %2$s">',
			'after_widget' => '</div>',  
			'before_title'  => '<h4 class="widgettitle">',
			'after_title'   => '</h4>',
        )
    ); 
}
add_action( 'widgets_init', 'divi_ecommerce_register_sidebars' );

/*
 *  04 - Display custom sidebar on Woocommerce pages
 */ 

function divi_ecommerce_output_content_wrapper_end() {
	echo '</div> <!-- #left-area -->';
	if (
		( is_product() && 'et_full_width_page' !== get_post_meta( get_the_ID(), '_et_pb_page_layout', true ) )
		||
		( ( is_shop() || is_product_category() || is_product_tag() ) && 'et_full_width_page' !== et_get_option( 'divi_shop_page_sidebar', 'et_right_sidebar' ) )
	) {
		echo '<div id="sidebar">';
		dynamic_sidebar( 'woocomerce-sidebar' );
		echo '</div>';
	}
	echo '
				</div> <!-- #content-area -->
			</div> <!-- .container -->
		</div> <!-- #main-content -->';
}

function divi_ecommerce_woocommerce_custom_sidebar() {
	remove_action( 'woocommerce_after_main_content', 'et_divi_output_content_wrapper_end', 10 );
	add_action( 'woocommerce_after_main_content', 'divi_ecommerce_output_content_wrapper_end', 10 );
}
add_action( 'after_setup_theme', 'divi_ecommerce_woocommerce_custom_sidebar', 50 );

/*
 *  05 - Other Woocommerce functions
 */ 

/* Custom "Empty Cart" message */
function brobasket_empty_cart_message() {
	echo '<div class="empty-cart"><h1>Your cart is <span>empty</span> :(</h1><p>Looks like you have not made your choice yet...</p></div>';
}
add_action( 'wc_empty_cart_message', 'brobasket_empty_cart_message' );

/* Display Add to cart button on archives */
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_add_to_cart', 10 );

/* Custom page header on Woo pages */
function divi_ecommerce_page_title() { 
	?>
	<div class="et_pb_section" id="ecommerce-custom-header">
		<div class="et_pb_row">
			<div class="et_pb_column">
				<?php if (  is_single() )  { ?>
					<h1 class="woocommerce-products-header__title page-title"><?php the_title( ); ?></h1>
				<?php ; } else  { ?>
					<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
				<?php ; }  ?>
				<?php woocommerce_breadcrumb(); ?>
			</div>
		</div>
	</div>
	<?php  
}
add_action('woocommerce_before_main_content', 'divi_ecommerce_page_title', 5); 

/* Remove breadcrumbs from woo_before_main_content */
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20); 

/* Remove duplicate page-title from WooCommerce archive pages */
function  divi_ecommerce_hide_page_titles() {
    if ( is_shop() )
    	return false;
}
add_filter( 'woocommerce_show_page_title', 'divi_ecommerce_hide_page_titles' );

 /* Display category thumbnail on taxonomy archives */
function woocommerce_category_image() {
    if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_woocommerce_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
		    echo '<img src="' . $image . '" alt="' . $cat->name . '" class="term-img" />';
		}
	}
}
add_action( 'woocommerce_archive_description', 'woocommerce_category_image', 2 );
