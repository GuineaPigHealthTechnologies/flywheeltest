<?php
/*
Plugin Name: Divi Commerce
Plugin URI: https://www.divistride.com/
Description: Make WooCommerce look and work the way you want it to.
Author: Divi Stride
Version: 2.0.6 
Author URI: https://www.divistride.com/
WC tested up to: 3.3.1
*/
require plugin_dir_path( __FILE__ ) .  'includes/update/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	'https://www.boltthemes.com/dc/details.json',
	__FILE__,
	'divicommerce'
);
/*Including files*/
/*CSS and JavaScript enque*/
//frontend
function dc_custom_css() {
	wp_register_style( 'divi_commerce_css', plugins_url('includes/css/frontend.css', __FILE__ ),'','1.1', '' );
	 wp_enqueue_style( 'divi_commerce_css' );
}
add_action( 'wp_enqueue_scripts', 'dc_custom_css', '30' );
//backend
function ds_dc_admin_css() {
    wp_enqueue_style('back-end-css', plugin_dir_url( __FILE__ ) . 'includes/css/backend.css');
}
add_action('admin_enqueue_scripts', 'ds_dc_admin_css'); // Admin Assets  
//clear cache
define( 'DC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
function dc_clear_local_storage () {
	wp_enqueue_script( 'dc_clear_local_storage', DC_PLUGIN_URL . 'includes/js/clear_local_storage.js' );
    wp_enqueue_script( 'dc_fullscreen_class', DC_PLUGIN_URL . 'includes/js/dc_class.js' );
}
add_action( 'admin_enqueue_scripts', 'dc_clear_local_storage', 9999 );
/*Enable Pagebuilder*/	
require plugin_dir_path( __FILE__ ) . '/includes/enable-pgb.php';
require plugin_dir_path( __FILE__ ) . 'includes/extensions/meta-box/meta-box.php'; // Path to the plugin's main file

/*Plugin option files*/
include("includes/single-product-meta.php");
include("includes/customizer.php"); 
include("includes/checkout-fields.php");
include("includes/inject-layouts.php");
include("includes/assistance.php");
/*Working on feature*/
/**require_once 'includes/category-layout.php';**/

/*Divi Commerce modules*/
function DC_Custom_Modules(){
 if(class_exists("ET_Builder_Module")){
 include("includes/modules/addtocart_module.php");
 include("includes/modules/info_tab_module.php");
 include("includes/modules/reviews_module.php");
 include("includes/modules/price_module.php");
 include("includes/modules/title_module.php");
 include("includes/modules/myaccount.php");
 include("includes/modules/cart.php");
 include("includes/modules/checkout.php");
 include("includes/modules/relatedproduct.php");
 include("includes/modules/breadcrumbs.php");
 include("includes/modules/gallery.php");
 include("includes/modules/thumbnails.php");
 }
}
function Prep_DC_Custom_Modules(){
 global $pagenow;

$is_admin = is_admin();
 $action_hook = $is_admin ? 'wp_loaded' : 'wp';
 $required_admin_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php' ); // list of admin pages where we need to load builder files
 $specific_filter_pages = array( 'edit.php', 'admin.php', 'edit-tags.php' );
 $is_edit_library_page = 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'];
 $is_role_editor_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'et_divi_role_editor' === $_GET['page'];
 $is_import_page = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import']; 
 $is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && 'layout_category' === $_GET['taxonomy'];

if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages ) && ( ! in_array( $pagenow, $specific_filter_pages ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page ) ) ) {
 add_action($action_hook, 'DC_Custom_Modules', 9789);
 }
}
Prep_DC_Custom_Modules(); 

/*template overide*/

//Replace the default Single Product template with ours
if( true != get_theme_mod( 'dc_no_template' ) ) {
function dc_single_product_file_replace() {
   $plugin_dir = plugin_dir_path( __FILE__ ) . '/single-product.php';
   $theme_dir = get_stylesheet_directory() . '/single-product.php';

    if (!copy($plugin_dir, $theme_dir)) { 
      echo "failed to copy $plugin_dir to $theme_dir...\n";
    }

}
add_action('wp_head', 'dc_single_product_file_replace');
}

//Add to cart on shop pages and shop module
if( false != get_theme_mod( 'dc_add_act1_loop' ) ) {
add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 20 );
}
if( false != get_theme_mod( 'dc_add_vdb_loop' ) ) {
add_action( 'woocommerce_after_shop_loop_item', 'bbloomer_view_product_button', 30);
function bbloomer_view_product_button() {
global $product;
$link = $product->get_permalink();
echo do_shortcode('<a href="'.$link.'" class="button addtocartbutton">View Product</a>');
}
}

function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="customize.php?autofocus[panel]=divi_commerce_panel">' . __( 'Settings' ) . '</a>';
    array_push( $links, $settings_link );
  	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'plugin_add_settings_link' );
add_filter('plugin_row_meta',  'Register_Plugins_Links', 10, 2);
function Register_Plugins_Links ($links, $file) {
               $base = plugin_basename(__FILE__);
               if ($file == $base) {
                       $links[] = '<a href="http://divistride.helpscoutdocs.com/category/5-divi-commerce">' . __('Documentation') . '</a>';
                       $links[] = '<a href="http://divistride.helpscoutdocs.com/article/14-changelog">' . __('Changelog') . '</a>';
               }
               return $links;
       }