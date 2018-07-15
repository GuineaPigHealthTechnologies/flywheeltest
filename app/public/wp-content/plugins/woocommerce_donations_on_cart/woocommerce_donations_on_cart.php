<?php
/*
Plugin Name: WooCommerce Donations Plugin
Plugin URI: http://www.pidhasome.com/albdesign/plugins/
Description: This plugin adds a donation field on the cart page.
Version: 1.7
Author: Albdesign
Author URI: http://www.pidhasome.com/albdesign/plugins
*/



//load translatable files
add_action('plugins_loaded', 'albdesign_wc_donations_language');
function albdesign_wc_donations_language() {
	load_plugin_textdomain( 'albdesign-wc-donations', false, dirname( plugin_basename( __FILE__ ) ) . '/language/' );
}

add_action('admin_menu', 'register_woocommerce_donation_submenu');

function register_woocommerce_donation_submenu() {
	add_submenu_page( 'woocommerce', 'Donations', 'Donations', 'manage_options', 'donnation-settings-page', 'woocommerce_donation_submenu_callback' ); 
}

function woocommerce_donation_submenu_callback() {
	
	echo '<h3>Donation settings page</h3>';

	//Create new product STARTS
	if(isset($_POST['woocommerce_donations_add_new_product_form'])){

			if($_POST['woocommerce_donations_new_product_title']!=""){
				$new_product_title = $_POST['woocommerce_donations_new_product_title'];
			}
		
						$add_new_donation_product_array = array(

								  'post_title'     => $new_product_title ,
								  'post_status'    => 'publish' , 
								  'post_type'      => 'product'  

								);  
						$id_of_new_donation_product = wp_insert_post($add_new_donation_product_array);

						
						
						//update_post_meta($id_of_new_donation_product , '_visibility','hidden');		
						update_post_meta($id_of_new_donation_product , '_sku','checkout-donation-product');		
						update_post_meta($id_of_new_donation_product , '_tax_class','zero-rate');		
						update_post_meta($id_of_new_donation_product , '_tax_status','none');		
						update_post_meta($id_of_new_donation_product , '_sold_individually','yes');		
						update_post_meta($id_of_new_donation_product , '_virtual','yes');		

	}
	//Create new product ENDS


	if(isset($_POST['woocommerce_donations_select_product_form'])){

		if ( !isset($_POST['woocommerce_donations_select_product_nonce_field']) || !wp_verify_nonce($_POST['woocommerce_donations_select_product_nonce_field'],'woocommerce_donations_select_product_nonce') )
		{
		   print 'Sorry, your nonce did not verify.';
		   exit;
		}
		else
		{
			//PROCESS FORM DATA 
			
			//selected an existing product
			if($_POST['woocommerce_donations_select_product_id']!=""){
				
				//save selected product ID
				$donation_product_new_option_value = $_POST['woocommerce_donations_select_product_id'] ;

				if ( get_option( 'woocommerce_donations_product_id' ) !== false ) {
					update_option( 'woocommerce_donations_product_id', $donation_product_new_option_value );
				} else {
					// there is still no options on the database
					add_option( 'woocommerce_donations_product_id', $donation_product_new_option_value, null, 'no' );
				}

			}
		}
		
		
	}

	
	//save string translations 
	if(isset($_POST['woocommerce_donations_save_string_translation'])){
		if($_POST['woocommerce_donations_translations']['use_custom_translation']){
			update_option('woocommerce_donations_translations',$_POST['woocommerce_donations_translations']);
		}
	}
	
	
	
	$get_saved_translation = get_option('woocommerce_donations_translations');
	
	
	
	?>

	<form  action="" method="post">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row" class="titledesc"><label for="woocommerce_donations_select_product_id">Donation product</label></th>
					<td class="forminp">
						Select existing product 
						<select name="woocommerce_donations_select_product_id" id="woocommerce_donations_select_product_id" style="" class="select email_type">
							<option value=""></option>
							
							<?php
							
							//read existing products that fullfill our needs
							$query_existing_hidden_products = new WP_Query( array( 
							'posts_per_page' => -1,
							'post_type'      => array( 'product' ),
							'meta_query' => array(
											array(
												//'key' => '_visibility',
												//'value' => array( 'catalog', 'visible' ),
												//'compare' => 'NOT IN'
							))));	



							while ( $query_existing_hidden_products->have_posts() ) {

								$query_existing_hidden_products->the_post(); ?>
								
								<option value="<?php echo get_the_ID(); ?>" <?php if ( get_option('woocommerce_donations_product_id' )  == get_the_ID() ){ echo 'selected="selected"'; } ?>> <?php echo get_the_title() ;?> </option>
								
							<?php
							}
							wp_reset_postdata();
							?>
						</select>

						<p class="description">A non taxable,not shippable product  needs to exists in woocommerce before using donations</p>
					</td>
				</tr>			
			</tbody>
		</table>		
		<p class="submit">
			<input type="hidden" name="woocommerce_donations_select_product_form">
				<?php wp_nonce_field('woocommerce_donations_select_product_nonce','woocommerce_donations_select_product_nonce_field'); ?>
			<input name="save" class="button-primary" type="submit" value="Save changes">        			        
		</p>
	</form>	

	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc"><label for="woocommerce_donations_select_product_id">New Donation product</label></th>
				<td class="forminp">
					<form  action="" method="post">
						  New product title :   <input name="woocommerce_donations_new_product_title" class="text" type="text" >
						<input name="woocommerce_donations_add_new_product_form" class="button" type="submit" value="Create Product">
						<p class="description">A non taxable,not shippable product will be created and you can select it on the "Donation Product" above afterward. <br> Keep in mind that the new product title will be visible on the cart, the checkout page and invoice so name it something like "DONATIONS" . </p>
					</form>
				</td>
			</tr>			
		</tbody>
	</table>		

	
	<h3>String translations</h3>

	<form  action="" method="post">
		<table class="form-table">
			
				<tbody>
				
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_use_custom_translation">Use custom text</label></th>
						<td class="forminp">
							<select name="woocommerce_donations_translations[use_custom_translation]" class="text" type="text" style="width:80%">
								<option value="no" <?php selected($get_saved_translation['use_custom_translation'],'no');?>>No , use default strings </option>
								<option value="yes" <?php selected($get_saved_translation['use_custom_translation'],'yes');?>>Yes ,I want to use the texts below instead of default text of plugin</option>
							</select>
							<p class="description">Select "Yes" if you want to use the strings below instead of default plugin strings . Supports HTML </p>
						</td>
					</tr>				
				
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_single_product_text">Single product</label></th>
						<td class="forminp">
							<input name="woocommerce_donations_translations[single_product_text]" class="text" type="text" style="width:80%" value="<?php echo woocommerce_donations_get_saved_strings_admin('single_product_text');?>">
							<p class="description">Text "Enter the amount you wish to donate" located on single product page . Supports HTML </p>
						</td>
					</tr>			
				
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_single_product_text">Single product confirmation</label></th>
						<td class="forminp">
							<input name="woocommerce_donations_translations[donation_added_single_product_text]" class="text" type="text" style="width:80%" value="<?php echo woocommerce_donations_get_saved_strings_admin('donation_added_single_product_text');?>">
							<p class="description">Text "Donation added" located on single product page . Shown once the customer adds the donation . Supports HTML </p>
						</td>
					</tr>				
				
				
				
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_cart_header_text">Cart Text</label></th>
						<td class="forminp">
							<input name="woocommerce_donations_translations[cart_header_text]" class="text" type="text"  style="width:80%" value="<?php echo woocommerce_donations_get_saved_strings_admin('cart_header_text');?>">
							<p class="description">Text "Add a donation to your order" located on cart before "Add Donation" . Supports HTML </p>
						</td>
					</tr>
					
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_cart_button_text">Cart Button</label></th>
						<td class="forminp">
							<input name="woocommerce_donations_translations[cart_button_text]" class="text" type="text"  style="width:80%" value="<?php echo woocommerce_donations_get_saved_strings_admin('cart_button_text');?>">
							<p class="description">Text for button "Add Donation" located on cart . Supports HTML </p>
						</td>
					</tr>			
					
					
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_checkout_title_text">Checkout Title Text</label></th>
						<td class="forminp">
							<input name="woocommerce_donations_translations[checkout_title_text]" class="text" type="text"  style="width:80%" value="<?php echo woocommerce_donations_get_saved_strings_admin('checkout_title_text');?>">
							<p class="description">"Add a donation to your order" header text located on checkout . Shown on checkout when user has not added a donation . Supports HTML </p>
						</td>
					</tr>				
					
					
					<tr valign="top">
						<th scope="row" class="titledesc"><label for="woocommerce_donations_checkout_text">Checkout  Text</label></th>
						<td class="forminp">
							<input name="woocommerce_donations_translations[checkout_text]" class="text" type="text"  style="width:80%" value="<?php echo woocommerce_donations_get_saved_strings_admin('checkout_text');?>">
							<p class="description">"If you wish to add a donation you can do so on the " text located on checkout . Shown on checkout when user has not added a donation . Supports HTML </p>
						</td>
					</tr>				
					
				</tbody>
			
		</table>		
		
		<p class="submit">
			<input name="woocommerce_donations_save_string_translation" class="button-primary" type="submit" value="Save translations">        			        
		</p>
	
	</form>
	
<?php

} //woocommerce_donation_submenu_callback




//current product ID 
if ( get_option('woocommerce_donations_product_id' ) !== false ) {

	//defines the ID of the product to be used as donation
	define('DONATE_PRODUCT_ID', get_option( 'woocommerce_donations_product_id' )); 
}


if ( ! function_exists( 'ok_donation_exists' ) ){
	function ok_donation_exists(){
	 
		global $woocommerce;
	 
		if( sizeof($woocommerce->cart->get_cart()) > 0){
	 
			foreach($woocommerce->cart->get_cart() as $cart_item_key => $values){
	 
				$_product = $values['data'];
	 
				if($_product->id == DONATE_PRODUCT_ID)
					return true;
			}
		}
		return false;
	}
}



// Avada and themes that uses avada as parent Fix
if(strtolower(wp_get_theme()->Template) == 'avada' || strtolower(wp_get_theme()->Name) == 'avada'){
	add_action('woocommerce_after_cart','ok_woocommerce_after_cart_table' , 1 );
}else{
	
	//All other themes 
	add_action('woocommerce_cart_contents','ok_woocommerce_after_cart_table');
}


if ( ! function_exists( 'ok_woocommerce_after_cart_table' ) ){
	
	function ok_woocommerce_after_cart_table(){
	 
		global $woocommerce;
		$donate = isset($woocommerce->session->ok_donation) ? floatval($woocommerce->session->ok_donation) : 0;
	 
		if(!ok_donation_exists()){
			unset($woocommerce->session->ok_donation);
		}
	  
		if(!ok_donation_exists()){
			?>
			<tr class="donation-block">
				<td colspan="6">
					<div class="donation">
						
						<p class="message"><strong>
						
						<?php 
						if(woocommerce_donations_get_saved_strings('cart_header_text')){
							echo woocommerce_donations_get_saved_strings('cart_header_text');
						}else{
							_e('Add a donation to your order','albdesign-wc-donations'); 
						}
						
						?>
						
						</strong></p>
						
						<form action=""method="post">
							<div class="input text">
								<input type="text" name="ok-donation" class="input-text" value="<?php echo $donate;?>"/>
								
								<?php if( woocommerce_donations_get_saved_strings( 'cart_button_text' ) ) { ?>
									<input type="submit" name="donate-btn" class="button" value="<?php echo woocommerce_donations_get_saved_strings('cart_button_text');?>"/>
								<?php }else{ ?>
									<input type="submit" name="donate-btn" class="button" value="<?php _e('Add Donation','albdesign-wc-donations');?>"/>
								<?php } ?>
								
								
							</div>
						</form>
					</div>
				</td>
			</tr>
			<?php
		}
	}
}



add_action('template_redirect','ok_process_donation');

if ( ! function_exists( 'ok_process_donation' ) ){
	
	function ok_process_donation(){
	 
		global $woocommerce;
	 
		$donation = isset($_POST['ok-donation']) && !empty($_POST['ok-donation']) ? floatval($_POST['ok-donation']) : false;
	 
		if($donation && isset($_POST['donate-btn'])){
	 
			// add item to basket
			$found = false;
	 
			// add to session
			if($donation > 0){
				$woocommerce->session->ok_donation = $donation;
	 
				//check if product already in cart
				
				if( sizeof($woocommerce->cart->get_cart()) > 0){
	 
					
					foreach($woocommerce->cart->get_cart() as $cart_item_key=>$values){
	 
						$_product = $values['data'];
	 
						if($_product->id == DONATE_PRODUCT_ID){
							$found = true;
						}
						
					}
		
					// if product not found, add it
					if(!$found){
						$woocommerce->cart->add_to_cart(DONATE_PRODUCT_ID);
					}
				}else{
					// if no products in cart, add it
					$woocommerce->cart->add_to_cart(DONATE_PRODUCT_ID);
				}

			}
		}
	}

}


add_filter('woocommerce_get_price', 'ok_get_price',10,2);

if ( ! function_exists( 'ok_get_price' ) ){
	function ok_get_price($price, $product){
	 
		global $woocommerce;
		
		if($product->id == DONATE_PRODUCT_ID){
			
			if(isset($_POST['ok-donation'])){
				return isset($woocommerce->session->ok_donation) ? floatval($woocommerce->session->ok_donation) : 0;
			}
			
			if(isset($_POST['albdesign_wc_donation_from_single_page'])){
				
				return ($_POST['albdesign_wc_donation_from_single_page']>0) ? floatval($_POST['albdesign_wc_donation_from_single_page']) : 0 ;
				
			}
			
			return isset($woocommerce->session->ok_donation) ? floatval($woocommerce->session->ok_donation) : 0;
			
		}
		
		return $price;
	}
}

//Change free text 
add_filter('woocommerce_free_price_html','albdesign_change_free_text' , 12,2);

if ( ! function_exists( 'albdesign_change_free_text' ) ){
	
	function albdesign_change_free_text($price,$product_object){
		
		global $woocommerce;
		
		if( !is_admin() && !( defined( 'WC_API_REQUEST' ) && WC_API_REQUEST == true)){
			
			if(isset($product_object->id)){
				
				if(defined('DONATE_PRODUCT_ID')){
				
					if ($product_object->id == DONATE_PRODUCT_ID ){
						
						if(isset($woocommerce->session->ok_donation )){
							if($woocommerce->session->ok_donation ){
								return 'Donation  added';
							}
						}
						//return 'Enter the amount you wish to donate' . print_r($woocommerce->session);
						
						if( woocommerce_donations_get_saved_strings( 'single_product_text' ) ) { 
							return '<span class="enter_donation_amount_single_page">'. woocommerce_donations_get_saved_strings( 'single_product_text' )  .'</span>' ;
						}

						return '<span class="enter_donation_amount_single_page">'. _e('Enter the amount you wish to donate','albdesign-wc-donations') .'</span>' ;
						
					}
				
				}
			}
		}
		
		return $price;
	}

}


add_action('woocommerce_before_add_to_cart_button','albdesign_add_input_on_single_product_page');

if ( ! function_exists( 'albdesign_add_input_on_single_product_page' ) ){
	function albdesign_add_input_on_single_product_page(){
		
		global $woocommerce,$post;
		
		$current_donation_value = 0;
		
		if(defined('DONATE_PRODUCT_ID')){
		
			if($post->ID == DONATE_PRODUCT_ID){
				
				
				if(!ok_donation_exists()){ 
					unset($woocommerce->session->ok_donation); 
				}
				
				if( ! isset($woocommerce->session->ok_donation)){
				 ?>
					<input name="albdesign_wc_donation_from_single_page" value="<?php echo $current_donation_value;?>">
				
				 <?php
				}else{
					?>
					 <p class="albdesign_wc_donation_from_single_page_added">
						
						<?php 
						
							if( woocommerce_donations_get_saved_strings( 'single_product_text' ) ) { 
						
								echo woocommerce_donations_get_saved_strings( 'donation_added_single_product_text' );
							
							}else {
								
								printf( __( 'Donation added . Check it on the  <a href="%s"> cart page </a>', 'albdesign-wc-donations' ), $woocommerce->cart->get_cart_url()); 
								
							} 
						?>
						
					</p>
					<?php
				}
			} 
		
		} // if defined
	}
}


/*
* Change "add to cart" on single page
*/

add_filter( 'woocommerce_product_single_add_to_cart_text', 'albdesign_custom_cart_button_text_single_page' );  
 
if ( ! function_exists( 'albdesign_custom_cart_button_text_single_page' ) ){
	
	function albdesign_custom_cart_button_text_single_page($text) {
	 
		global $post,$woocommerce;

		if(defined('DONATE_PRODUCT_ID')){
		
			if($post->ID == DONATE_PRODUCT_ID) {
				if(isset($woocommerce->session->ok_donation )){
					$text =  _e('Donation added','albdesign-wc-donations') ;
				}
			}
		
		}
	 
		return $text;
	}

}

/*
* Hide  the "ADD TO CART" on single page if donations already added
*/
add_action('wp_head','albdesign_wc_donation_hide_add_to_cart_on_single_product');

if ( ! function_exists( 'albdesign_wc_donation_hide_add_to_cart_on_single_product' ) ){
	
	function albdesign_wc_donation_hide_add_to_cart_on_single_product(){
		
		global $woocommerce;
		
		if(defined('DONATE_PRODUCT_ID')){
		
			if(isset($woocommerce->session->ok_donation )){
				echo '<style>
						.woocommerce div.product.post-'.DONATE_PRODUCT_ID.' form.cart .button {
							display:none;
						}
					 </style>';
			}

		}
		
	}
}


add_filter('woocommerce_add_cart_item', 'albdesign_wc_donation_add_cart_item_data', 14, 2);

if ( ! function_exists( 'albdesign_wc_donation_add_cart_item_data' ) ){
	
	function albdesign_wc_donation_add_cart_item_data($cart_item) {
		global $woocommerce;

		if(defined('DONATE_PRODUCT_ID')){
		
			if($cart_item['product_id'] == DONATE_PRODUCT_ID){

				//if the user is adding from single product page 
				if(isset($_POST['albdesign_wc_donation_from_single_page'])){
					
					$woocommerce->session->ok_donation =  floatval($_POST['albdesign_wc_donation_from_single_page']);
				}
			}
		
		}
		
		return $cart_item;
	}

}

add_action('woocommerce_review_order_before_payment','albdesign_donations_add_link_on_checkout');

if ( ! function_exists( 'albdesign_donations_add_link_on_checkout' ) ){
	
	function albdesign_donations_add_link_on_checkout(){ 

		global $woocommerce;

		$products_ids_in_cart=false;
		
		//check if donation is already in cart 
		foreach($woocommerce->cart->get_cart() as $cart_item_key => $values ) {
			
			$_product = $values['data'];
		
			$products_ids_in_cart[$_product->id]= $_product->id;

		}

		//if no donation found on cart ... show a link on checkout page
		if( is_array( $products_ids_in_cart ) ) {
			
			if( !in_array(DONATE_PRODUCT_ID,$products_ids_in_cart )){
				?>
					<div style="margin: 0 -1px 24px 0;">
					<h3>  
					
					<?php 
						if( woocommerce_donations_get_saved_strings( 'checkout_title_text' ) ) { 
							echo woocommerce_donations_get_saved_strings( 'checkout_title_text' );
						}else {
							_e('Add a donation to your order','albdesign-wc-donations');
						} 
					?>
					
					</h3> 
					
					
					<?php 
						if( woocommerce_donations_get_saved_strings( 'checkout_text' ) ) { 
							echo woocommerce_donations_get_saved_strings( 'checkout_text' );
						}else {
							 printf( __( 'If you wish to add a donation you can do so on the <a href="%s"> cart page </a>', 'albdesign-wc-donations' ), $woocommerce->cart->get_cart_url() ); 
						} 
					?>					
					
					
					</div>
				<?php 
				
			} //end if "no donation found on cart"
		
		} //end if is array $products_ids_in_cart
		
	}
	
}




/*
* Get translated texts for backend plugin options
*/
function woocommerce_donations_get_saved_strings_admin($key){
	$saved_strings_array = get_option('woocommerce_donations_translations');
	if(isset($saved_strings_array[$key])){
		return stripcslashes(esc_html($saved_strings_array[$key]));
	}
	
	return false;
}

/*
* Get translated texts for frontend
*/
function woocommerce_donations_get_saved_strings($key){
	
	$saved_strings_frontend_array = get_option('woocommerce_donations_translations');
	
	if($saved_strings_frontend_array['use_custom_translation']=='yes'){
		
		if($saved_strings_frontend_array[$key]){
			return stripcslashes($saved_strings_frontend_array[$key]);
		}
	
	}
	
	return false;
}