<?php
/**
 * Class for generating the actual feeds
 */
class WooSEA_Get_Products {

	private $feedback;
	public $feed_config;
	private $products = array();
	private $utm = array();
	public $utm_part;
	public $project_config;
	private $upload_dir;
	private $base;
	private $path;
	private $file;

        public function __construct() {
                $this->get_products = array();
        }

	/**
 	 * Get all product cats for a product by ID, including hierarchy
 	 * @param  int $product_id
 	 * @return array
 	 */
	public function wc_get_product_cat_ids( $product_id ) {
		$product_cats = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );

	        foreach ( $product_cats as $product_cat ) {
        	        $product_cats = array_merge( $product_cats, get_ancestors( $product_cat, 'product_cat' ) );
        	}
        	return $product_cats;
	}

	/**
         * Function that will create an append with Google Analytics UTM parameters
         * Removes UTM paramaters that are left blank
	 */
	public function woosea_append_utm_code ( $feed_config, $productId, $parentId ) {

		// AdTribes conversionId
		if(array_key_exists('adtribes_conversion', $feed_config)){
			$adtribesConvId = "$feed_config[project_hash]|adtribes|$productId";
		} else {
			$adtribesConvId = "";
		}	
		
		# Create Array of Google Analytics UTM codes				
		$utm = array (
			'adTribesID' => $adtribesConvId,
			'utm_source' => $feed_config['utm_source'],
			'utm_campaign' => $feed_config['utm_campaign'],
			'utm_medium' => $feed_config['utm_medium'],
			'utm_term' => $productId,
			'utm_content' => $feed_config['utm_content']
		);

		// GA tracking is disabled, so remove from array
		if(!array_key_exists('utm_on', $feed_config)){
			unset($utm['utm_source']);
			unset($utm['utm_campaign']);
			unset($utm['utm_medium']);
			unset($utm['utm_term']);
			unset($utm['utm_content']);
		}
		$utm = array_filter($utm); // Filter out empty or NULL values from UTM array		
                
		$utm_part = "";	
		foreach ($utm as $key => $value ) {
			$value = str_replace(" ", "%20", $value);
			$utm_part .= "&$key=$value";
		}

		# Strip first & from utm 
		if($parentId > 0){
			$utm_part = "&".ltrim($utm_part, '&');
		} else {
			$utm_part = "?".ltrim($utm_part, '&');
		}
		return $utm_part;
	}

       	/**
         * Converts an ordinary xml string into a CDATA string
         */
    	public function woosea_convert_to_cdata( $string ) { 
		return "<![CDATA[ $string ]]>"; 
	}

	/**
	 * Get custom attribute names for a product
	 */
	private function get_custom_attributes( $productId ) {
        	global $wpdb;
        	$list = array();

        	$sql = "SELECT meta_key as name, meta_value as type FROM " . $wpdb->prefix . "postmeta" . " WHERE post_id=".$productId." group by meta_key";
        	$data = $wpdb->get_results($sql);

        	if (count($data)) {
                	foreach ($data as $key => $value) {
                        	if (substr($value->name, 0, 1) !== "_") {
                                	$value_display = str_replace("_", " ",$value->name);
			                if (!preg_match("/pyre|sbg/i",$value->name)){
                                		$list[$value->name] = ucfirst($value_display);
                        		}
				}
                	}
                	return $list;
        	}
        	return false;
	}

	/**
	 * Get orders for given time period used in filters
	 */
	public function woosea_get_orders( $project_config ){
    		$query_args = array(
        		'post_type'      => wc_get_order_types(),
        		'post_status'    => array_keys( wc_get_order_statuses() ),
        		'posts_per_page' => 999999999999,
    		);
    		$all_orders      = get_posts( $query_args );

    		foreach ( $all_orders as $orders ) {		
			$order = wc_get_order( $orders-> ID);
			$order_data = $order->get_data();
			$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');

			//error_log("ORDER ID:" . $orders->ID);
			//error_log("ORDER DATE:" . $order_date_created);

			foreach ($order->get_items() as $item_key => $item_values){
				$product_id = $item_values->get_product_id();
				//error_log("PRODUCT ID:" . $product_id);
			}		
		}
		//return $orders_timeframe;
	}

	/**
	 * Get category path (needed for Prisjakt)
	 */
	public function woosea_get_term_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false, $visited = array() ) {
		$chain = '';
		$parent = get_term( $id, $taxonomy );
		
		if ( is_wp_error( $parent ) )
			return $parent;
		 
		if ($nicename){
			$name = $parent->slug;
		} else {
			$name = $parent->name;
		}

		if ($parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited )){
			$visited[] = $parent->parent;
			$chain .= WooSEA_Get_Products::woosea_get_term_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
		}
 
		if ($link){
			$chain .= '<a href="' . get_term_link( $parent, $taxonomy ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$parent->name.'</a>' . $separator;
		} else {
			$chain .= $name.$separator;
		}
		return $chain;
	} // End woo_get_term_parents()	

	/**
	 * Get all configured shipping zones
	 */
	public function woosea_get_shipping_zones () {
		if( class_exists( 'WC_Shipping_Zones' ) ) {
			$all_zones = WC_Shipping_Zones::get_zones();
			return $all_zones;
		}
		return false;
	}

	/**
	 * Get shipping cost for product
	 */
	public function woosea_get_shipping_cost ($class_cost_id, $project_config, $price, $tax_rates, $shipping_zones) {
        	$shipping_cost = 0;
		$shipping_arr = array();
		$zone_count = 0;
		$nr_shipping_zones = count($shipping_zones);

		$base_location = wc_get_base_location();
		$base_country = $base_location['country'];

              	foreach ( $shipping_zones as $zone){
			$zone_details = array();
			$zone_code = $zone['zone_locations'][0]->code;
			$zone_details['country'] = $zone_code;
			$zone_details['service'] = "";
			$zone_details['region'] = "";
			if(isset($zone['zone_locations'][1]->code)){
				$zone_region = $zone['zone_locations'][1]->code;
				$zn_short = explode (":", $zone_region);
				$zone_details['region'] = $zn_short[1];
			}	
	
			// When no shipping zone has been configured in project take the shops country zone id as default
			if (!array_key_exists('zone', $project_config) AND ($zone_code == $base_country)){
				$project_config['zone'] = $zone['zone_id'];
			}

                     	$shipping_methods     = $zone['shipping_methods'];
                    	$arr_shipping_methods = json_decode( json_encode( $shipping_methods ), true );

			foreach ( $arr_shipping_methods as $shipping ) {


				// FLAT RATE SHIPPING COSTS
				if($shipping['instance_settings']['title'] == "Flat rate"){
					if(isset($shipping['instance_settings']['cost'])){
						$zone_details['service'] = $shipping['instance_settings']['title'];
						$shipping_cost = $shipping['instance_settings']['cost'];
					}

					// CLASS SHIPPING COSTS
                           		if(isset($shipping['instance_settings'][$class_cost_id])){
						$zone_details['service'] = $shipping['instance_settings']['title'];
						$shipping_cost = ($shipping['instance_settings'][$class_cost_id]+$shipping_cost);
                              		}
				}
        
				// FREE SHIPPING COSTS
				if($shipping['instance_settings']['title'] == "Free shipping"){
					if ($price >= $shipping['instance_settings']['min_amount']){
						$zone_details['service'] = $shipping['instance_settings']['title'];
						$shipping_cost = 0;
					}
				}
                   	}

			foreach ($tax_rates as $k => $v){
				if($v['shipping'] == "yes"){
					$rate = (($v['rate']+100)/100);
					$shipping_cost = $shipping_cost*$rate;
					$shipping_cost = round($shipping_cost, 2);
				}
			}

			$zone_details['price'] = $shipping_cost;
			$shipping_arr[$zone_count] = $zone_details; 		
			$zone_count++;	
		}
		return $shipping_arr;
		//return $shipping_cost;
	}

	/**
	 * Log queries, used for debugging errors
	 */
	public function woosea_create_query_log ( $query, $filename ) {
                $upload_dir = wp_upload_dir();

                $base = $upload_dir['basedir'];
                $path = $base . "/woo-product-feed-pro/logs";
                $file = $path . "/". $filename ."." ."log";

                // External location for downloading the file   
                $external_base = $upload_dir['baseurl'];
                $external_path = $external_base . "/woo-product-feed-pro/logs";
                $external_file = $external_path . "/" . $filename ."." ."log";

                // Check if directory in uploads exists, if not create one      
                if ( ! file_exists( $path ) ) {
                        wp_mkdir_p( $path );
                }

		// Log timestamp
		$today = "\n";
		$today .= date("F j, Y, g:i a");                 // March 10, 2001, 5:16 pm
		$today .= "\n";

                $fp = fopen($file, 'a+');
                fwrite($fp, $today);
		fwrite($fp, print_r($query, TRUE));
		fclose($fp);
	}


	/**
         * Creates XML root and header for productfeed
	 */	
	public function woosea_create_xml_feed ( $products, $feed_config, $header ) {
		$upload_dir = wp_upload_dir();

		$base = $upload_dir['basedir'];
 		$path = $base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$file = $path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];
	
		// External location for downloading the file	
		$external_base = $upload_dir['baseurl'];
 		$external_path = $external_base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$external_file = $external_path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];

		// Check if directory in uploads exists, if not create one	
		if ( ! file_exists( $path ) ) {
    			wp_mkdir_p( $path );
		}

		// Check if file exists, if it does: delete it first so we can create a new updated one
		if ( (file_exists( $file )) AND ($header == "true") AND ($feed_config['nr_products_processed'] == 0) ) {
			unlink ( $file );
		}	

		// Check if there is a channel feed class that we need to use
		if ($feed_config['fields'] != 'standard'){
			if (!class_exists('WooSEA_'.$feed_config['fields'])){
				require plugin_dir_path(__FILE__) . '/channels/class-'.$feed_config['fields'].'.php';
				$channel_class = "WooSEA_".$feed_config['fields'];
				$channel_attributes = $channel_class::get_channel_attributes();
				update_option ('channel_attributes', $channel_attributes, 'yes');	
			} else {
				$channel_attributes = get_option('channel_attributes');
			}
		}	

		// Some channels need their own feed config and XML namespace declarations (such as Google shopping)
		if ($feed_config['taxonomy'] == 'google_shopping'){
			$namespace = array(
				'g' => 'http://base.google.com/ns/1.0'
			);
			if ( ($header == "true") AND ($feed_config['nr_products_processed'] == 0) ) {
			   	$xml = new SimpleXMLElement('<rss xmlns:g="http://base.google.com/ns/1.0"></rss>');
			   	$xml->addAttribute('version', '2.0');
				$xml->addChild('channel');
				$xml->channel->addChild('title', $feed_config['projectname'] );
				$xml->channel->addChild('link', site_url());
				$xml->channel->addChild('description', 'WooCommerce Product Feed PRO - This product feed is created with the free Advanced WooCommerce Product Feed PRO plugin from AdTribes.io. For all your support questions check out our FAQ on https://www.adtribes.io or e-mail to: support@adtribes.io ');
				$xml->asXML($file);	
			} else {
				$xml = simplexml_load_file($file, 'SimpleXMLElement', LIBXML_NOCDATA);
				$aantal = count($products);

				if ($aantal > 0){
					foreach ($products as $key => $value){
						if (is_array ( $value ) ) {
							$product = $xml->channel->addChild('item');
							foreach ($value as $k => $v){
								if ($k == "g:shipping"){
									$ship = explode("||", $v);
									foreach ($ship as $kk => $vv){
										$country_ship = explode(":", $vv);
										
										$shipping = $product->addChild($k, '', $namespace['g']);
                                                       				$shipping_country = $shipping->addChild('g:country', $country_ship[0], $namespace['g']);
                                                       				$shipping_region = $shipping->addChild('g:region', $country_ship[2], $namespace['g']);
                                                       				$shipping_service = $shipping->addChild('g:service', $country_ship[1], $namespace['g']);
										$shipping_price = $shipping->addChild('g:price', $country_ship[3], $namespace['g']);
									}
								// Fix issue with additional images for Google Shopping
								} elseif (preg_match("/g:additional_image_link/i",$k)){
                                                       			$link = $product->addChild('g:additional_image_link', $v, $namespace['g']);
									//$product->$k = $v;
								} else {
									$product->$k = $v;
								}
							}
						}	
					}
					$xml->asXML($file);

					unset($product);	
				}
				unset($products);
			}
			unset($xml);
		} else {
			if ( ($header == "true") AND ($feed_config['nr_products_processed'] == 0) ) {
				if ($feed_config['name'] == "Yandex") {
					$main_currency = get_woocommerce_currency();

					$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><yml_catalog></yml_catalog>');	
					$xml->addAttribute('date', date('Y-m-d H:i'));
					$shop = $xml->addChild('shop');
					$shop->addChild('name', $feed_config['projectname']);
					$shop->addChild('company', get_bloginfo());
					$shop->addChild('url', site_url());
					$shop->addChild('platform', 'WooCommerce');
					$currencies = $shop->addChild('currencies');
					$currency = $currencies->addChild('currency');
					$currency->addAttribute('id', $main_currency);
					$currency->addAttribute('rate', '1');

					$args = array(
    						'taxonomy'   => "product_cat",
					);
					$product_categories = get_terms( 'product_cat', $args );
					$count = count($product_categories);
					if ($count > 0){
						$categories = $shop->addChild('categories');

        					foreach ($product_categories as $product_category){
							$category = $categories->addChild('category', $product_category->name);
							$category->addAttribute('id', $product_category->term_id);
							if ($product_category->parent > 0){
								$category->addAttribute('parentId', $product_category->parent);

							}
        					}
					}

					$shop->addChild('agency', 'AdTribes.io');
					$shop->addChild('email', 'support@adtribes.io');
					$xml->asXML($file);
				} elseif ($feed_config['name'] == "Heureka.cz") {
					$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><SHOP></SHOP>');	
					$xml->addAttribute('xmlns', 'https://www.zbozi.cz/ns/offer/1.0/');
					$xml->asXML($file);
				} else {
					$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><products></products>');	
					$xml->addAttribute('version', '1.0');
					$xml->addChild('datetime', date('Y-m-d H:i:s'));
					$xml->addChild('title', $feed_config['projectname']);
					$xml->addChild('link', site_url());
					$xml->addChild('description', 'WooCommerce Product Feed PRO - This product feed is created with the free Advanced WooCommerce Product Feed PRO plugin from AdTribes.io. For all your support questions check out our FAQ on https://www.adtribes.io or e-mail to: support@adtribes.io ');
					$xml->asXML($file);
				}
			} else {
				$xml = simplexml_load_file($file);
				$aantal = count($products);

				if ($aantal > 0){
					if ($feed_config['name'] == "Yandex") {
						$offers = $xml->shop[0]->addChild('offers');
					}

					foreach ($products as $key => $value){
						if (is_array ( $value ) ) {
							if ($feed_config['name'] == "Yandex") {
								$product = $offers->addChild('offer');
							} elseif ($feed_config['name'] == "Heureka.cz") {
								$product = $xml->addChild('SHOPITEM');
							} else {
								$product = $xml->addChild('product');
							}
							foreach ($value as $k => $v){
					
								$v = trim($v);
								$k = trim($k);	

								if(($k == "id") AND ($feed_config['name'] == "Yandex")){
									$product->addAttribute('id', trim($v));
								}
								if(($k == "available") AND ($feed_config['name'] == "Yandex")){
									if($v == "in stock"){
										$v = "true";
									} else {
										$v = "false";
									}
									$product->addAttribute('available', $v);
								}

								/**
								 * Check if a product resides in multiple categories
								 * id so, create multiple category child nodes
								 */			
								if ($k == "categories"){
									$category = $product->addChild('categories');
									$cat = explode("||",$v);							

									if (is_array ( $cat ) ) {
										foreach ($cat as $kk => $vv){
											$child = "category";
											$category->addChild("$child", "$vv");
										}
									}
								} elseif ($k == "shipping"){
									$ships = explode("||",$v);							
									if (is_array ( $ships ) ) {
										foreach ($ships as $kk => $vv){
											$ship_zone = $product->addChild('shipping');
											$country_ship = explode(":", $vv);

                                                                                	$shipping_country = $ship_zone->addChild('country', $country_ship[0]);
                                                                                	$shipping_region = $ship_zone->addChild('region', $country_ship[2]);
                                                                                	$shipping_service = $ship_zone->addChild('service', $country_ship[1]);
                                                                                	$shipping_price = $ship_zone->addChild('price', $country_ship[3]);
										}
									}
								} elseif ($k == "category_link"){
									$category = $product->addChild('category_links');
									$cat_links = explode("||",$v);							
									if (is_array ( $cat_links ) ) {
										foreach ($cat_links as $kk => $vv){
											$child = "category_link";
											$category->addChild("$child", "$vv");
										}
									}
								} elseif ($k == "categoryId"){

									if($feed_config['name'] == "Yandex"){
										$args = array(
    											'taxonomy'   => "product_cat",
										);
		
										//$category = $product->addChild('categories');
										$product_categories = get_terms( 'product_cat', $args );
										$count = count($product_categories);
										$cat = explode("||",$v);							

										if (is_array ( $cat ) ) {
											foreach ($cat as $kk => $vv){
												if ($count > 0){
        												foreach ($product_categories as $product_category){
														if($vv == $product_category->name){
															$product->addChild("$k", "$product_category->term_id");
														}
													}
												}
											}
										}
									}
								} elseif (($k == "id" || $k == "available") AND ($feed_config['name'] == "Yandex")){
									// Do not add these nodes to Yandex product feeds
								} else {
									if ($feed_config['fields'] != 'standard'){
	          	                                           		$k = WooSEA_Get_Products::get_alternative_key ($channel_attributes, $k);
									}
								
									if(!empty($k)){	
										$product->addChild("$k");
										$product->$k = $v;
									}
								}
							}
						}
					}	
					$xml->asXML($file);
					unset($product);
				}
				unset($products);
			}
			unset($xml);
		}
	}

	/**
         * Actual creation of CSV/TXT file
         * Returns relative and absolute file path
	 */	
	public function woosea_create_csvtxt_feed ( $products, $feed_config, $header ) {

		$upload_dir = wp_upload_dir();
		$base = $upload_dir['basedir'];
 		$path = $base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$file = $path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];
	
		// External location for downloading the file	
		$external_base = $upload_dir['baseurl'];
 		$external_path = $external_base . "/woo-product-feed-pro/" . $feed_config['fileformat'];
        	$external_file = $external_path . "/" . sanitize_file_name($feed_config['filename']) . "." . $feed_config['fileformat'];

		// Check if directory in uploads exists, if not create one	
		if ( ! file_exists( $path ) ) {
    			wp_mkdir_p( $path );
		}

		// Check if file exists, if it does: delete it first so we can create a new updated one
		if ( (file_exists( $file )) AND ($feed_config['nr_products_processed'] == 0) AND ($header == "true") ) {
			unlink ( $file );
		}	

		// Check if there is a channel feed class that we need to use
		if ($feed_config['fields'] != 'standard'){
			if (!class_exists('WooSEA_'.$feed_config['fields'])){
				require plugin_dir_path(__FILE__) . '/channels/class-'.$feed_config['fields'].'.php';
				$channel_class = "WooSEA_".$feed_config['fields'];
				$channel_attributes = $channel_class::get_channel_attributes();
				update_option ('channel_attributes', $channel_attributes, 'yes');	
			} else {
				$channel_attributes = get_option('channel_attributes');
			}
		}	
		
		// Append or write to file
		$fp = fopen($file, 'a+');

		// Write each row of the products array
		foreach ($products as $row) {

			foreach ($row as $k => $v){
				$pieces = explode ("','", $v);
				$pieces = str_replace("'", "", $pieces);

				foreach ($pieces as $k => $v){
                                        if ($feed_config['fields'] != 'standard'){
						$v = WooSEA_Get_Products::get_alternative_key ($channel_attributes, $v);
					}			            

					// For CSV fileformat the keys need to get stripped of the g:
                                      	if($feed_config['fileformat'] == "csv"){
                                        	$v = str_replace("g:", "", $v);
                                     	}	

					$pieces[$k] = $v;
				}
				$blaat = fputcsv($fp, $pieces, $feed_config['delimiter'], '"');
			}
		}
		// Close the file
		fclose($fp);

		// Return external location of feed
		return $external_file;
	}

	/**
         * Get products that are eligable for adding to the file
	 */
	public function woosea_get_products ( $project_config ) {
		$nr_products_processed = $project_config['nr_products_processed'];
		$count_products = wp_count_posts('product', 'product_variation');

 		if(isset($project_config['product_variations'])){
			$count_single = wp_count_posts('product');
			$count_variation = wp_count_posts('product_variation');
			$published_single = $count_single->publish;
			$published_variation = $count_variation->publish;
			$published_products = $published_single+$published_variation;		
		} else {
			$count_single = wp_count_posts('product');
			$published_products = $count_single->publish;
		}

		$versions = array (
        		"PHP" => (float)phpversion(),
        		"Wordpress" => get_bloginfo('version'),
        		"WooCommerce" => WC()->version,
			"Plugin" => WOOCOMMERCESEA_PLUGIN_VERSION
		);

		/**
		 * Do not change these settings, they are here to prevent running into memory issues
		 */
		if ($versions['PHP'] < 5.6){
			// Old version, process a maximum of 50 products per batch
			$nr_batches = ceil($published_products/50);
		} elseif ($versions['PHP'] == 5.6){
			// Old version, process a maximum of 100 products per batch
			$nr_batches = ceil($published_products/200);
		} else {
			// Fast PHP version, process a 250 products per batch
			$nr_batches = ceil($published_products/500);
		}
		$offset_step_size = ceil($published_products/$nr_batches);

		/**
		 * Check if the [attributes] array in the project_config is of expected format.
		 * For channels that have mandatory attribute fields (such as Google shopping) we need to rebuild the [attributes] array
		 * Only add fields to the file that the user selected
		 * Construct header line for CSV ans TXT files, for XML create the XML root and header
		 */
		if($project_config['fileformat'] != 'xml'){
			if($project_config['fields'] != 'standard'){
				foreach ($project_config['attributes'] as $key => $value){
					foreach($value as $k => $v){
						if(($k == "attribute") AND (strlen($v) > 0)){
                     	       				if(!isset($attr)){
								$attr = "'$v'";
							} else {
								$attr .= ",'$v'";
							}
						}
					}
				}
			} else {
				foreach( array_keys($project_config['attributes']) as $attribute_key ){
					if (!isset($attr)){
						if(strlen($attribute_key) > 0){
							$attr = "'$attribute_key'";
						}
					} else {
						if(strlen($attribute_key) > 0){
							$attr .= ",'$attribute_key'";
						}
					}			
				}
			}
			$attr = trim($attr, "'");
			$products[] = array ( $attr );
			if($nr_products_processed == 0){
				$file = WooSEA_Get_Products::woosea_create_csvtxt_feed ( $products, $project_config, 'true' );
			}
		} else {
			$products[] = array ();
			$file = WooSEA_Get_Products::woosea_create_xml_feed ( $products, $project_config, 'true' );
		}
		$xml_piece = "";

		// Check if we need to get just products or also product variations
		if(isset($project_config['product_variations'])){
			$post_type = array('product', 'product_variation');
		} else {
			$post_type = array('product');
		}

		// Get Orders
		// $order_timeframe = WooSEA_Get_Products::woosea_get_orders ( $project_config );

		// Construct WP query
		$wp_query = array(
				'posts_per_page' => $offset_step_size,
                                'offset' => $nr_products_processed,
				'post_type' => $post_type,
				'post_status' => 'publish',
                                'fields' => 'ids',
                                'no_found_rows' => true,
                );
		$prods = new WP_Query($wp_query);

		// Logging the WP query and WP query output has been disabled as there are no structural issues with the plugin

		// Log query for debugging purposes
		// $log_query = WooSEA_Get_Products::woosea_create_query_log($wp_query, "query");

		// Log the query output for debugging purposes
		// $log_query = WooSEA_Get_Products::woosea_create_query_log($prods, "query_output");

		$shipping_zones = WooSEA_GET_Products::woosea_get_shipping_zones();

	        while ($prods->have_posts()) : $prods->the_post(); 
			global $product;
			$attr_line = "";
			$catname = "";	
			$catlink = "";
			$xml_product = array();

			$this->childID = get_the_ID();
            		$this->parentID = wp_get_post_parent_id($this->childID);
			$post = get_post($this->parentID);

			// When Wordpress user is an admin and runs the process of creating product feeds also products are put in the feed
			// with a status other than published. This is unwanted behaviour so we skip all products that are not on publish.
			$status = get_post_status($this->parentID);
			if($status != "publish") { continue; }

			$product_data['id'] = get_the_ID();
			$product_data['title'] = $product->get_title();
			$product_data['sku'] = $product->get_sku();
			$product_data['sku_id'] = $product_data['id'];
			
			if (!empty($product_data['sku'])){
				$product_data['sku_id'] = $product_data['sku']."_".$product_data['id'];
			}

                       	$product_data['item_group_id'] = $this->parentID;
			$categories = array_unique(wc_get_product_cat_ids( $product_data['id'] ));

			// Check if the Yoast plugin is installed and active
			if ( class_exists('WPSEO_Primary_Term') ){

				$product_id = $product_data['id'];
				$primary_cat_id=get_post_meta($product_id ,'_yoast_wpseo_primary_product_cat',true);
 				$category_path = WooSEA_Get_Products::woosea_get_term_parents( $primary_cat_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
				if(!is_object($category_path)){
					$product_data['category_path'] = $category_path;	
				}	

				if(($primary_cat_id) AND ($primary_cat_id > 0)){
   					$product_cat = get_term($primary_cat_id, 'product_cat');
			
					if(isset($product_cat->name)) {
						$catname = $product_cat->name;
						$catlink = get_category_link($product_cat->term_id);
					}
				} else {
					foreach ($categories as $key => $value){
						if (!$catname){
		                                        $product_cat = get_term($value, 'product_cat');

                                        		if(isset($product_cat->name)) {
                                                		$catname = $product_cat->name;
								$catlink = get_term_link($value,'product_cat');
                                        		}
						} else {
		                                        $product_cat = get_term($value, 'product_cat');
	                                   	     	if(isset($product_cat->name)) {
                                                		$catname_concat = $product_cat->name;
								$catlink_concat = get_term_link($value,'product_cat');
                                        		}
							$catname .= "||".$catname_concat;
							$catlink .= "||".$catlink_concat;
						}
					}
				}
			} else {
				foreach ($categories as $key => $value){

					if (!$catname){
	                                        $product_cat = get_term($value, 'product_cat');

						// Check if there are mother categories
						$parent_categories = get_ancestors($product_cat->term_id, 'product_cat');
					
						$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
                                		if(!is_object($category_path)){
							$product_data['category_path'] = $category_path;
						}

						foreach ($parent_categories as $category_id){
							$parent = get_term_by('id', $category_id, 'product_cat');
							$parent_name = $parent->name;
						}

                                        	if(isset($product_cat->name)) {
							$catname = $product_cat->name;
							$catlink = get_term_link($value,'product_cat');
                                        	}
					} else {
	                                        $product_cat = get_term($value, 'product_cat');
					
						$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
                                               	if(!is_object($category_path)){
							$product_data['category_path'] = $category_path;
						}	

						// Check if there are mother categories
						$parent_categories = get_ancestors($product_cat->term_id, 'product_cat');
						foreach ($parent_categories as $category_id){
							$parent = get_term_by('id', $category_id, 'product_cat');
							$parent_name .= "||".$parent->name;
						}

	                                        if(isset($product_cat->name)) {
                                                	$catname_concat = $product_cat->name;
							$catlink_concat = get_term_link($value,'product_cat');
                                        	}
						$catname .= "||".$catname_concat;
						$catlink .= "||".$catlink_concat;
					}
				}
			}

			$product_data['category_link'] = $catlink;
			$product_data['categories'] = $catname;
			$product_data['description'] = str_replace("\r", "", $post->post_content);
			$product_data['short_description'] = str_replace("\r", "", $post->post_excerpt);

			// Strip HTML from (short) description
			$product_data['description'] = strip_shortcodes(strip_tags($product_data['description']));
			$product_data['short_description'] = strip_shortcodes(strip_tags($product_data['short_description']));

			/**
		 	* Check of we need to add Google Analytics UTM parameters
		 	*/
			if(isset($project_config['utm_on'])){
				$utm_part = WooSEA_Get_Products::woosea_append_utm_code ( $project_config, get_the_ID(), $this->parentID);
			} else {
				$utm_part = "";
			}

			$product_data['link'] = get_permalink()."$utm_part";
			$product_data['condition'] = "New";
			$product_data['availability'] = $this->get_stock( $this->childID );
			$product_data['quantity'] = $this->clean_quantity( $this->childID, "_stock" );
			$product_data['visibility'] = $this->get_attribute_value( $this->childID,"_visibility" );
			$product_data['currency'] = get_woocommerce_currency();
                        $product_data['sale_price_start_date'] = $this->get_sale_date($this->childID, "_sale_price_dates_from");
                        $product_data['sale_price_end_date'] = $this->get_sale_date($this->childID, "_sale_price_dates_to");
			$product_data['image'] = wp_get_attachment_url($product->get_image_id());

			$gallery_ids = $product->get_gallery_image_ids();
			foreach ($gallery_ids as $gallery_key => $gallery_value){
				$gal_id = $gallery_key+1;
				$product_data["image_" . $gal_id] = wp_get_attachment_url($gallery_value);
			}
			$product_data['product_type'] = $product->get_type();
                        $product_data['rating_total'] = $product->get_rating_count();
                        $product_data['rating_average'] = $product->get_average_rating();
	                $product_data['shipping'] = 0;
			$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
			$shipping_class_id = $product->get_shipping_class_id();
                	$shipping_class= $product->get_shipping_class();
			$class_cost_id = "class_cost_".$shipping_class_id;

			// Get prices including taxes
			$product_data['price'] = wc_format_localized_price(wc_get_price_including_tax($product,array('price'=> $product->get_price())));
			$product_data['regular_price'] = wc_format_localized_price(wc_get_price_including_tax($product, array('price'=> $product->get_regular_price())));
			$product_data['sale_price'] = wc_format_localized_price(wc_get_price_including_tax($product, array('price'=> $product->get_sale_price())));

			// Get net prices
			$product_data['net_price'] = wc_format_localized_price($product->get_price());
			$product_data['net_regular_price'] = wc_format_localized_price($product->get_regular_price());
			$product_data['net_sale_price'] = wc_format_localized_price($product->get_sale_price());

			$price = wc_format_localized_price(wc_get_price_including_tax($product,array('price'=> $product->get_price())));
			if($product_data['sale_price'] > 0){
				$price = $product_data['sale_price'];
			}
			$product_data['shipping'] =  WooSEA_Get_Products::woosea_get_shipping_cost($class_cost_id, $project_config, $price, $tax_rates, $shipping_zones);

			$shipping_str = "";
			foreach ($product_data['shipping'] as $key => $value){
				$shipping_str .= "||";
				foreach($value as $k => $v){
					$shipping_str .= "$v>";
				}
			}
			ltrim($shipping_str, "||");

			$product_data['weight'] = ($product->get_weight()) ? $product->get_weight() : false;
                        $product_data['height'] = ($product->get_height()) ? $product->get_height() : false;
                        $product_data['length'] = ($product->get_length()) ? $product->get_length() : false;
			$product_data['width'] = ($product->get_width()) ? $product->get_width() : false;

                        // Featured Image
                        if (has_post_thumbnail($post->ID)){
                         	$image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'single-post-thumbnail');
                            	$product_data['feature_image'] = $this->get_image_url($image[0]);
                        } else {
                           	$product_data['feature_image'] = $this->get_image_url($product_data['image']);
                        }

			/**
			 * Do we need to add Dynamic Attributes?
			 */
			$project_config['attributes_original'] = $project_config['attributes'];

			if($project_config['fields'] != 'standard'){
				//$project_config['attributes_original'] = array();
				foreach($project_config['attributes'] as $stand_key => $stand_val){
					if((isset($stand_val['mapfrom'])) AND (strlen($stand_val['mapfrom']) > 0)){
						$project_config['attributes_original'][$stand_val['mapfrom']] = "true";
					}
				}				
			}

        		$no_taxonomies = array("element_category","template_category","portfolio_category","portfolio_skills","portfolio_tags","faq_category","slide-page","yst_prominent_words","category","post_tag","nav_menu","link_category","post_format","product_type","product_visibility","product_cat","product_shipping_class","product_tag");
        		$taxonomies = get_taxonomies();
  
	      		$diff_taxonomies = array_diff($taxonomies, $no_taxonomies);

			foreach($diff_taxonomies as $taxo){
				$term_value = get_the_terms($product_data['id'], $taxo);
				
				if(is_array($term_value)){
					foreach($term_value as $term){
						$product_data[$taxo] = $term->name;
					}
				}
			}

			/**
			 * Get Custom Attributes
			 */
			$custom_attributes = WooSEA_Get_Products::get_custom_attributes( $product_data['id'] );

			foreach($custom_attributes as $custom_kk => $custom_value){

    				$custom_value = get_post_meta( $product_data['id'], $custom_kk, true );
				$new_key ="custom_attributes_" . $custom_kk;
				$product_data[$new_key] = $custom_value;
			}

			/**
			 * Get Product Attributes for Single products 
			 */
			if ($product->is_type('simple')){
				$single_attributes = $product->get_attributes();

				foreach ($single_attributes as $attribute){
					$attr_name = strtolower($attribute->get_name());
					$attr_value = $product->get_attribute($attr_name);
					$product_data[$attr_name] = $attr_value;
				}
			}
		
			/**
			 * Versioned products need a seperate approach
			 * Get data for these products based on the mother products item group id 
			 */
			if( ($product_data['item_group_id'] > 0) ){

				$product_variations = new WC_Product_Variation( $product_data['id'] );
    				$variations = $product_variations->get_variation_attributes();
				$append = "";
	
				/**
				 * Although this is a product variation we also need to grap the Product attributes belonging to the simple mother product
				 */
				$mother_attributes = get_post_meta($product_data['item_group_id'], '_product_attributes');
	                      	foreach ($mother_attributes as $attribute){
					foreach($attribute as $attr){
						$attr_name = $attr['name'];
						$terms = get_the_terms($product_data['item_group_id'], $attr_name);
						foreach($terms as $term){
							$attr_value = $term->name;
						}
						$product_data[$attr_name] = $attr_value;		
					}
				}

				/**
				 * Although this is a product variation we also need to grap the Dynamic attributes belonging to the simple mother prodict
				 */
                        	foreach($diff_taxonomies as $taxo){
                                	$term_value = get_the_terms($product_data['item_group_id'], $taxo);

                                	if(is_array($term_value)){
                                        	foreach($term_value as $term){
                                                	$product_data[$taxo] = $term->name;
                                       		}
                                	}
                        	}

        	                /**
                	         * Get Custom Attributes
                       	  	 */
                        	$custom_attributes = WooSEA_Get_Products::get_custom_attributes( $product_data['id'] );

                        	foreach($custom_attributes as $custom_kk => $custom_value){
                                	$custom_value = get_post_meta( $product_data['id'], $custom_kk, true );
                                	$new_key ="custom_attributes_" . $custom_kk;
					// In order to make the mapping work again, replace var by product
                                        $new_key = str_replace("var","product",$new_key);
					$product_data[$new_key] = $custom_value;
                        	}

				// User does need to also add the attributes to the feed otherwise they cannot be appended to the productname
				foreach($variations as $kk => $vv){
					$custom_key = $kk; 

					if (isset($project_config['product_variations']) AND ($project_config['product_variations'] == "on")){
						$taxonomy = str_replace("attribute_","",$kk);
						$term = get_term_by('slug', $vv, $taxonomy); 
						
						if($term){
							$append = ucfirst($term->name);
						}

						$product_data['title'] = $product_data['title']." ".$append;
					}
						
					$custom_key = str_replace("attribute_","",$custom_key);
					$product_data[$custom_key] = $vv;
				}
                        	
				// Get versioned product categories	
				$categories = wc_get_product_cat_ids( $product_data['item_group_id'] );
                       
				// Check if the Yoast plugin is installed and active
				if ( class_exists('WPSEO_Primary_Term') ){
					$product_id = $product_data['item_group_id'];

					$primary_cat_id=get_post_meta($product_id ,'_yoast_wpseo_primary_product_cat',true);
					if($primary_cat_id){
 		  				$product_cat = get_term($primary_cat_id, 'product_cat');
   						$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
 	                                      	if(!is_object($category_path)){
							$product_data['category_path'] = $category_path;
						}							

						if(isset($product_cat->name)) {
							$catname = $product_cat->name;
							$catlink = get_category_link($product_cat->term_id);
						}
					} else {
						foreach ($categories as $key => $value){
                                                	if (!$catname){
                                                        	$product_cat = get_term($value, 'product_cat');
                                                		$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
                                                			
					                     	if(!is_object($category_path)){
									$product_data['category_path'] = $category_path;
								}

                                                        	if(isset($product_cat->name)) {
                                                                	$catname = $product_cat->name;
                                                                	$catlink = get_term_link($value,'product_cat');
                                                        	}
                                                	} else {
                                                        	$product_cat = get_term($value, 'product_cat');
                                                        	$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
                                               			if(!is_object($category_path)){
									$product_data['category_path'] = $category_path;
								}

								if(isset($product_cat->name)) {
                                                                	$catname_concat = $product_cat->name;
                                                                	$catlink_concat = get_term_link($value,'product_cat');
                                                        	}
                                                        	$catname .= "||".$catname_concat;
                                                        	$catlink .= "||".$catlink_concat;
                                               		}
						}
					}
				} else {
					foreach ($categories as $key => $value){
	                                        if (!$catname){
                                                        $product_cat = get_term($value, 'product_cat');
                                                	$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
                                                	if(!is_object($category_path)){
								$product_data['category_path'] = $category_path;
                                                    	}
							 if(isset($product_cat->name)) {
                                                               	$catname = $product_cat->name;
                                                               	$catlink = get_term_link($value,'product_cat');
                                                       	}
                                                } else {
                                                        $product_cat = get_term($value, 'product_cat');
							$category_path = WooSEA_Get_Products::woosea_get_term_parents( $product_cat->term_id, 'product_cat', $link = false, $separator = ' / ', $nicename = false, $visited = array() );
                                                	if(!is_object($category_path)){
								$product_data['category_path'] = $category_path;                                                        	
							}
							if(isset($product_cat->name)) {
                                                              	$catname_concat = $product_cat->name;
                                                               	$catlink_concat = get_term_link($value,'product_cat');
                                                       	}
                                                       	$catname .= "||".$catname_concat;
                                                       	$catlink .= "||".$catlink_concat;
                                               	}
					}
				}
				$product_data['category_link'] = $catlink;
				$product_data['categories'] = $catname;
                     	} 

			/**
			 * Filter execution
			 */
			if (array_key_exists('rules', $project_config)){
				$product_data = $this->woocommerce_sea_rules( $project_config['rules'], $product_data ); 
				$cnta = count($product_data);
				// Product needs to be removed
				if($cnta == 0){
					break;
				}
			}

			/**
			 * Check if we need to add category taxonomy mappings (Google Shopping)
			 */
			if ((array_key_exists('mappings', $project_config)) AND ($project_config['taxonomy'] == 'google_shopping')){
				$product_data = $this->woocommerce_sea_mappings( $project_config['mappings'], $product_data ); 
			}

			/**
			 * Rules execution
			 */
			if (array_key_exists('rules2', $project_config)){
				$product_data = $this->woocommerce_sea_rules2( $project_config['rules2'], $product_data ); 
			}

			/**
			 * When a product is a variable product we need to delete the original product from the feed, only the originals are allowed
			 */
			if(($product->is_type('variable')) AND ($product_data['item_group_id'] == 0)){
		        	$product_data = array();
                        	$product_data = null;	
			}

			/**
			 * When product has passed the filter rules it can continue with the rest
			 */
			if(!empty($product_data)){
				/**
				 * Determine what fields are allowed to make it to the csv and txt productfeed
				 */
			        if (($project_config['fields'] != "standard") AND (!isset($tmp_attributes))){
					$old_attributes_config = $project_config['attributes'];
                      			$tmp_attributes = array();
					foreach ($project_config['attributes'] as $key => $value){
						if(strlen($value['mapfrom']) > 0){
							$tmp_attributes[$value['mapfrom']] = "true";
						}
					}
	                      		$project_config['attributes'] = $tmp_attributes;
				}

				if(isset($old_attributes_config)){
					$identifier_positions = array();
					$loop_count = 0;

					foreach($old_attributes_config as $attr_key => $attr_value){
				
						if(!$attr_line){
							if(array_key_exists('static_value', $attr_value)){
								if(strlen($attr_value['mapfrom'])){
									$attr_line = "'".$attr_value['prefix']. "".$attr_value['mapfrom']."" .$attr_value['suffix']."'";
								} else {
									$attr_line = "''";
								}
							} else {
								if((strlen($attr_value['mapfrom'])) AND (array_key_exists($attr_value['mapfrom'], $product_data))){
									$attr_line = "'".$attr_value['prefix']. "".$product_data[$attr_value['mapfrom']]."" .$attr_value['suffix']."'";
								} else {
									$attr_line = "''";
								}
							}
						} else {
							if(array_key_exists('static_value', $attr_value)){
								$attr_line .= ",'".$attr_value['prefix']. "".$attr_value['mapfrom']."" .$attr_value['suffix']."'";
							} else {
								// Determine position of identifiers in CSV row
								if($attr_value['attribute'] == "g:brand" || $attr_value['attribute'] == "g:gtin" || $attr_value['attribute'] == "g:mpn" || $attr_value['attribute'] == "g:identifier_exists"){
									$arr_pos = array($attr_value['attribute'] => $loop_count);
									$identifier_positions = array_merge($identifier_positions, $arr_pos);	
								}

 								if (array_key_exists($attr_value['mapfrom'], $product_data)){
									if(is_array($product_data[$attr_value['mapfrom']])){
                                        	                               	$shipping_str = "";
                                                                               	foreach ($product_data[$attr_value['mapfrom']] as $key => $value){
                                                       	                        	$shipping_str .= "||";
                                                               	                     	foreach($value as $k => $v){
												if(preg_match('/[0-9]/', $v)){
                                                                              	           		$shipping_str .= ":$attr_value[prefix] $v $attr_value[suffix]";
                                                                                  	         } else {
                                                                                        		$shipping_str .= ":$v";
                                                                     	                 	}
											}
                                                                       	     	}
                                                                  	        $shipping_str = ltrim($shipping_str, "||");
                                                                        	$shipping_str = rtrim($shipping_str, ":");
                                                                     	      	$shipping_str = ltrim($shipping_str, ":");
                                                                             	$shipping_str = str_replace("||:", "||", $shipping_str);

										$attr_line .= ",'".$shipping_str."'";
                                                             	 	} else {
										$attr_line .= ",'".$attr_value['prefix']. "".$product_data[$attr_value['mapfrom']]."" .$attr_value['suffix']."'";
									}
								} else {
									$attr_line .= ",''";
								}
							}
						}
						$loop_count++;
					}
					$pieces_row = explode ("','", $attr_line);
					$pieces_row = array_map('trim', $pieces_row);

					foreach($identifier_positions as $id_key => $id_value){
						if($id_key != "g:identifier_exists"){
							if ($pieces_row[$id_value]){
								$identifier_exists = "Yes";
							}
						} else {
							$identifier_position = $id_value;
						}
					}
	
					if((isset($identifier_exists)) AND ($identifier_exists == "Yes")){
						$pieces_row[$id_value] = $identifier_exists;
					} else {
						if(isset($id_value)){
							$pieces_row[$id_value] = "No";
						}
					}
					$attr_line = implode("','", $pieces_row);
			
					$products[] = array ( $attr_line );
				} else {
					foreach( array_keys($project_config['attributes']) as $attribute_key ){
                                        	if (array_key_exists($attribute_key, $product_data)){
                                                        if(!$attr_line){
                                                                $attr_line = "'".$product_data[$attribute_key]."'";
                                                        } else {
                                                                $attr_line .= ",'".$product_data[$attribute_key]."'";
                                                        }
                                                }
					}
					$attr_line = trim($attr_line, "'");
					$products[] = array ( $attr_line );
				}

				/**
				 * Build an array needed for the adding Childs in the XML productfeed
				 */
				foreach( array_keys($project_config['attributes']) as $attribute_key ){
					if(!isset($old_attributes_config)){
						if(!$xml_product){
							$xml_product = array (
								$attribute_key => $product_data[$attribute_key]
							);
						} else {
							if(isset($product_data[$attribute_key])){
								$xml_product = array_merge($xml_product, array($attribute_key => $product_data[$attribute_key]));
							}
						}
					} else {
						foreach($old_attributes_config as $attr_key => $attr_value){

							$ca = 0;
							// Static attribute value was set by user
							if(array_key_exists('static_value', $attr_value)){
								if(!isset($xml_product)){
									$xml_product = array (
										$attr_value['attribute'] => "$attr_value[prefix] ". $attr_value['mapfrom'] ." $attr_value[suffix]"
									);
								} else {
									$xml_product[$attr_value['attribute']] = "$attr_value[prefix] ". $attr_value['mapfrom'] ." $attr_value[suffix]";	
								}
							} elseif ($attr_value['mapfrom'] == $attribute_key){
								if(!isset($xml_product)){
									$xml_product = array (
										$attr_value['attribute'] => "$attr_value[prefix] ". $product_data[$attr_value['mapfrom']] ." $attr_value[suffix]"
									);
								} else {
									if(key_exists($attr_value['mapfrom'],$product_data)){
										if(is_array($product_data[$attr_value['mapfrom']])){
											$shipping_str = "";
                        								foreach ($product_data[$attr_value['mapfrom']] as $key => $value){
                                								$shipping_str .= "||";
                                								foreach($value as $k => $v){
													if(preg_match('/[0-9]/', $v)){
                                        									$shipping_str .= ":$attr_value[prefix] $v $attr_value[suffix]";
													} else {
                                        									$shipping_str .= ":$v";
                                									}
												}
                        								}
                        								$shipping_str = ltrim($shipping_str, "||");
                        								$shipping_str = rtrim($shipping_str, ":");
                        								$shipping_str = ltrim($shipping_str, ":");
											$shipping_str = str_replace("||:", "||", $shipping_str);

											$xml_product[$attr_value['attribute']] = "$shipping_str";	
										} else {
											if(array_key_exists($attr_value['attribute'], $xml_product)){
												$ca++;
												$xml_product[$attr_value['attribute']."_$ca"] = "$attr_value[prefix] ". $product_data[$attr_value['mapfrom']] ." $attr_value[suffix]";	
											} else {
												$xml_product[$attr_value['attribute']] = "$attr_value[prefix] ". $product_data[$attr_value['mapfrom']] ." $attr_value[suffix]";	
											}
										}
									}
								}
							}
						}
					}
				}

				// Do we need to do some calculation on attributes for Google Shopping
				$xml_product = WooSEA_Get_Products::woosea_calculate_value ( $project_config, $xml_product ); 

				foreach($xml_product as $key_product => $value_product){
					if (preg_match("/custom_attributes_attribute_/", $key_product)){
						$pieces = explode("custom_attributes_attribute_",$key_product);
						unset($xml_product[$key_product]);
						$xml_product[$pieces[1]] = $value_product;
					} elseif (preg_match("/product_attributes_/", $key_product)){
						$pieces = explode("product_attributes_",$key_product);
						unset($xml_product[$key_product]);
						$xml_product[$pieces[1]] = $value_product;
					}
				}

				if(!$xml_piece){
					$xml_piece = array ($xml_product);
					unset($xml_product);
				} else {
					array_push ($xml_piece, $xml_product);
					unset($xml_product);
				}
				unset($product_data);	
			}
		endwhile;
		wp_reset_query();

		/**
		 * Update processing status of project
		 */
		$project_updated = WooSEA_Get_Products::woosea_project_update($project_config['project_hash'], $offset_step_size, $xml_piece);

		/**
		 * Write row to CSV/TXT or XML file
		 */
		if($project_config['fileformat'] != 'xml'){
			unset($products[0]);
			$file = WooSEA_Get_Products::woosea_create_csvtxt_feed ( array_filter($products), $project_config, 'false' );
		} else {
			if(is_array($xml_piece)){
				$file = WooSEA_Get_Products::woosea_create_xml_feed ( array_filter($xml_piece), $project_config, 'false' );
				unset($xml_piece);
			}
			unset($products);
		}

		/**
	  	 * Ready creating file, clean up our feed configuration mess now
		 */
		 delete_option('attributes_dropdown');
		 delete_option('channel_attributes');
	}

	/**
 	 * Update processing statistics of batched projects 
 	 */
	public function woosea_project_update($project_hash, $offset_step_size, $xml_piece){
        	$feed_config = get_option( 'cron_projects' );
		$nr_projects = count ($feed_config);

		// Information for debug log
		$count_variation = wp_count_posts('product_variation');
		$count_single = wp_count_posts('product');
		$published_single = $count_single->publish;
		$published_variation = $count_variation->publish;
		$published_products = $published_single+$published_variation;

		$product_numbers = array (
      			"Single products" => $published_single,
        		"Variation products" => $published_variation,
        		"Total products" => $published_products,
			"Number projects" => count($feed_config)
		);

                $versions = array (
                        "PHP" => (float)phpversion(),
                        "Wordpress" => get_bloginfo('version'),
                        "WooCommerce" => WC()->version,
                        "Plugin" => WOOCOMMERCESEA_PLUGIN_VERSION
                );

                // Get the sales from created product feeds
		global $wpdb;
                $charset_collate = $wpdb->get_charset_collate();
                $table_name = $wpdb->prefix . 'adtribes_my_conversions';
                $order_rows = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

              	$notifications_obj = new WooSEA_Get_Admin_Notifications;
                $external_debug_file = $notifications_obj->woosea_debug_informations ($versions, $product_numbers, $order_rows, $feed_config);
		// End information for debug log

		foreach ( $feed_config as $key => $val ) {
                
			if(isset($val['product_variations'])){
		                $count_variation = wp_count_posts('product_variation');
                		$count_single = wp_count_posts('product');
				$published_single = $count_single->publish;
                		$published_variation = $count_variation->publish;
                		$published_products = $published_single+$published_variation; 
			} else {
                		$count_single = wp_count_posts('product');
				$published_products = $count_single->publish;
			}

			if ($val['project_hash'] == $project_hash){
				$nrpr = $feed_config[$key]['nr_products_processed'];
				$nr_prods_processed = $nrpr+$offset_step_size;

				if(is_array($xml_piece)){
					// End of processing batched feed
					if($nrpr >= $feed_config[$key]['nr_products']){
						// Set counters back to 0
						$feed_config[$key]['nr_products_processed'] = 0;
					
						// Set processing status on ready
						$feed_config[$key]['running'] = "ready";
						$project_data['last_updated'] = date("d M Y H:i");

						$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
						delete_option( $batch_project );

						// In 2 minutes from now check the amount of products in the feed and update the history count
						wp_schedule_single_event( time() + 120, 'woosea_update_project_stats', array($val['project_hash']) );
					} else {
						$feed_config[$key]['nr_products_processed'] = $nr_prods_processed;
						$feed_config[$key]['running'] = "processing";
			
						// Set new scheduled event for next batch in 3 seconds
						if($offset_step_size < $published_products){
        						if (! wp_next_scheduled ( 'woosea_create_batch_event', array($feed_config[$key]['project_hash']) ) ) {
								wp_schedule_single_event( time() + 2, 'woosea_create_batch_event', array($feed_config[$key]['project_hash']) );
								$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
								update_option( $batch_project, $val);
							}
						} else {
							// No batch is needed, already done processing all products
					
							// Set counters back to 0
							$feed_config[$key]['nr_products_processed'] = 0;
					
							// Set processing status on ready
							$feed_config[$key]['running'] = "ready";
							$project_data['last_updated'] = date("d M Y H:i");

							$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
							delete_option( $batch_project );

							// In 2 minutes from now check the amount of products in the feed and update the history count
							wp_schedule_single_event( time() + 120, 'woosea_update_project_stats', array($val['project_hash']) );
						}
					}
				} else {
					// Fall-back: no more products in xml_piece, stop batching process
					// Set counters back to 0
					$feed_config[$key]['nr_products_processed'] = 0;
					
					// Set processing status on ready
					$feed_config[$key]['running'] = "ready";
					$project_data['last_updated'] = date("d M Y H:i");

					$batch_project = "batch_project_".$feed_config[$key]['project_hash'];
					delete_option( $batch_project );
				}
                	}
        	}
		$nr_projects_cron = count ( get_option ( 'cron_projects' ) );

		/**
		 * Only update the cron_project when no new project was created during the batched run otherwise the new project will be overwritten and deleted
		 */
		if ($nr_projects == $nr_projects_cron){
        		update_option( 'cron_projects', $feed_config);
		}
	}

	/**
	 * Make product availability readable
	 */
    	public function clean_availability( $available ) {
    		if ($available) {
        		if ($available == 'instock') {
                		return "in stock";
            		} elseif ($available == 'outofstock') {
                		return "out of stock";
            		}
        	}	
        	return "out of stock";
    	}

	/**
	 * Calculate the value of an attribute
	 */
	public function woosea_calculate_value ( $project_config, $xml_product ) {
		// trim whitespaces from attribute values
		$xml_product = array_map('trim', $xml_product);

		// Check for new products in the Google Shopping feed if we need to 'calculate' the identifier_exists attribute value
	    	if(($project_config['taxonomy'] == "google_shopping") AND (isset($xml_product['g:condition']))){
			$identifier_exists = "no"; // default value is no

			if (array_key_exists("g:brand", $xml_product) AND ($xml_product['g:brand'] != "")){
				// g:gtin exists and has a value
				if ((array_key_exists("g:gtin", $xml_product)) AND ($xml_product['g:gtin'] != "")){
					$identifier_exists = "yes";
				// g:mpn exists and has a value
				} elseif ((array_key_exists("g:mpn", $xml_product)) AND ($xml_product['g:mpn'] != "")){
					$identifier_exists = "yes";
				// g:brand is empty and so are g:gtin and g:mpn, so no identifier exists
				} else {
					$identifier_exists = "no";
				}
			} else {
				// g:gtin exists and has a value but brand is empty
				if ((array_key_exists("g:gtin", $xml_product)) AND ($xml_product['g:gtin'] != "")){
					$identifier_exists = "no";
				// g:mpn exists and has a value but brand is empty
				} elseif ((array_key_exists("g:mpn", $xml_product)) AND ($xml_product['g:mpn'] != "")){
					$identifier_exists = "no";
				// g:brand is empty and so are g:gtin and g:mpn, so no identifier exists
				} else {
					$identifier_exists = "no";
				}
			}
			$xml_product['g:identifier_exists'] = $identifier_exists;
		}
		return $xml_product;
	}

	/**
	 * Check if the channel requires unique key/field names and change when needed
	 */
	private function get_alternative_key ($channel_attributes, $original_key) {
		$alternative_key = $original_key;

		if(!empty($channel_attributes)){
			foreach ($channel_attributes as $k => $v){
				foreach ($v as $key => $value){
					if(array_key_exists("woo_suggest", $value)){				
						if ($original_key == $value['woo_suggest']){
							$alternative_key = $value['feed_name'];
						}
					}
				} 
			}
		}
		return $alternative_key;
	}	

	/**
	 * Make product quantity readable
	 */
    	public function clean_quantity( $id, $name ) {
        	$quantity = $this->get_attribute_value( $id, $name );
        	if ($quantity) {
            		return $quantity + 0;
        	}
        	return "0";
    	}

	/**
	 * Make start and end sale date readable
	 */
    	public function get_sale_date($id, $name) {
        	$date = $this->get_attribute_value($id, $name);
        	if ($date) {
            		return date("Y-m-d", $date);
        	}
        	return false;
    	}

	/**
	 * Get product stock
	 */
    	public function get_stock( $id ){
        	$status=$this->get_attribute_value($id,"_stock_status");
        	if ($status) {
            		if ($status == 'instock') {
                		return "in stock";
            		} elseif ($status == 'outofstock') {
                		return "out of stock";
            		}
        	}
        	return "out of stock";
    	}

	/**
	 * Create proper format image URL's
	 */
	public function get_image_url($image_url = ""){
        	if (!empty($image_url)) {
            		if (substr(trim($image_url), 0, 4) === "http" || substr(trim($image_url), 0,5) === "https" || substr(trim($image_url), 0, 3) === "ftp" || substr(trim($image_url), 0, 4) === "sftp") {
                		return rtrim($image_url, "/");
            		} else {
                		$base = get_site_url();
                		$image_url = $base . $image_url;
                		return rtrim($image_url, "/");
            		}
		}
        	return $image_url;
	}

	/**
     	 * Get attribute value
     	 */
    	public function get_attribute_value( $id, $name ){
        	if (strpos($name, 'attribute_pa') !== false) {
        		$taxonomy = str_replace("attribute_","",$name);
            		$meta = get_post_meta($id,$name, true);
            		$term = get_term_by('slug', $meta, $taxonomy);
            		return $term->name;
        	} else {
            		return get_post_meta($id, $name, true);
        	}
    	}
	/**
	 * Execute category taxonomy mappings
	 */
        private function woocommerce_sea_mappings( $project_mappings, $product_data ){
		$original_cat = $product_data['categories'];
		$original_cat = preg_replace('/&amp;/','&',$original_cat);

		$tmp_cat = "";
		$match = "false";

		foreach ($project_mappings as $pm_key => $pm_array){
	
			// Strip slashes
			$pm_array['criteria'] = str_replace("\\","",$pm_array['criteria']);

			// Category mapping based on productname
			if($pm_array['attribute'] == "title"){
				if($pm_array['condition'] == "="){
					if($product_data['title'] == $pm_array['criteria']){
						$category_pieces = explode("-", $pm_array['map_to_category']);
						$tmp_cat = $category_pieces[0];
						$match = "true";
					} else {
						$product_data['categories'] = "";
					}
				} elseif ($pm_array['condition'] == "contains"){
					if (@preg_match("/$pm_array[criteria]/i","$product_data[title]")){
						$category_pieces = explode("-", $pm_array['map_to_category']);
						$tmp_cat = $category_pieces[0];
						$match = "true";
					} else {
						$product_data['categories'] = "";
					}
				}
			// Category mapping based on "all products"
			} elseif($pm_array['attribute'] == "all_products") {
				$category_pieces = explode("-", $pm_array['map_to_category']);
				$tmp_cat = $category_pieces[0];
				$match = "true";
			// Category mapping based on category
			} else {
				if($pm_array['condition'] == "="){
					$cat_pieces = explode ("||",$original_cat);
					
					if (count($cat_pieces) > 1){
						foreach ($cat_pieces as $k_piece => $v_piece){
							if($v_piece == $pm_array['criteria']){
								$category_pieces = explode("-", $pm_array['map_to_category']);
								$tmp_cat = $category_pieces[0];
								$match = "true";
							} else {
								$product_data['categories'] = "";
							}
						}
					} else {
						if($original_cat == $pm_array['criteria']){
							$category_pieces = explode("-", $pm_array['map_to_category']);
							$tmp_cat = $category_pieces[0];
							$match = "true";
						}
					}
				} elseif ($pm_array['condition'] == "contains"){
					if (preg_match("/$pm_array[criteria]/i","$original_cat")){
						$category_pieces = explode("-", $pm_array['map_to_category']);
						$tmp_cat = $category_pieces[0];
						$match = "true";
					} else {
						$product_data['categories'] = "";
					}
				}
			}
		}
	
		if($match == "true"){
			$product_data['categories'] = $tmp_cat;
		}

		return $product_data;
	}

	/**
	 * Execute project rules 
	 */
        private function woocommerce_sea_rules2( $project_rules2, $product_data ){

		$aantal_prods = count($product_data);
		if($aantal_prods > 0){

			foreach ($project_rules2 as $pr_key => $pr_array){
				foreach ($product_data as $pd_key => $pd_value){

					// Check is there is a rule on specific attributes
					if($pd_key == $pr_array['attribute']){

						// Check if a rule has been set for Google categories
						if ($product_data[$pr_array['than_attribute']] == "google_category"){
							$pr_array['than_attribute'] = "categories";
							$category_id = explode("-", $pr_array['newvalue']);
							$pr_array['newvalue'] = $category_id[0];
						}

						if ((is_numeric($pd_value)) AND ($pr_array['than_attribute'] != "shipping")){
							// Rules for numeric values	
							switch ($pr_array['condition']) {
								case($pr_array['condition'] = "contains"):
									if ((preg_match('/'.$pr_array['criteria'].'/', $pd_value))){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = "containsnot"):
									if ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value))){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = "="):
									if (($pd_value == $pr_array['criteria'])){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = "!="):
									if (($pd_value != $pr_array['criteria'])){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = ">"):
									if (($pd_value > $pr_array['criteria'])){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
    									break;
								case($pr_array['condition'] = ">="):
									if (($pd_value >= $pr_array['criteria'])){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = "<"):
									if (($pd_value < $pr_array['criteria'])){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = "=<"):
									if (($pd_value <= $pr_array['criteria'])){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								case($pr_array['condition'] = "empty"):
									if ((strlen($pd_value) < 1)){
										$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									}
									break;
								default:
									break;
							}
						} elseif (is_array($pd_value)) {
							// For now only shipping details are in an array
							foreach ($pd_value as $k => $v){
								foreach ($v as $kk => $vv){
									// Only shipping detail rule can be on price for now
									if($kk == "price"){
										switch ($pr_array['condition']) {
											case($pr_array['condition'] = "contains"):
												if ((preg_match('/'.$pr_array['criteria'].'/', $vv))){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = "containsnot"):
												if ((!preg_match('/'.$pr_array['criteria'].'/', $vv))){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = "="):
												if (($vv == $pr_array['criteria'])){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = "!="):
												if (($vv != $pr_array['criteria'])){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = ">"):
												if (($vv > $pr_array['criteria'])){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
    												break;
											case($pr_array['condition'] = ">="):
												if (($vv >= $pr_array['criteria'])){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = "<"):
												if (($vv < $pr_array['criteria'])){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = "=<"):
												if (($vv <= $pr_array['criteria'])){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											case($pr_array['condition'] = "empty"):
												if ((strlen($vv) < 1)){
													$pd_value[$k]['price'] = $pr_array['newvalue'];
													$product_data[$pr_array['than_attribute']] = $pd_value;
												}
												break;
											default:
												break;
										}
									}
								}
							}
						} else {
							// Rules for string values
							if (!array_key_exists('cs', $pr_array)){
								$pd_value = strtolower($pd_value);
								$pr_array['criteria'] = strtolower($pr_array['criteria']);
							}				
							switch ($pr_array['condition']) {
								case($pr_array['condition'] = "contains"):
									if ((preg_match('/'.$pr_array['criteria'].'/', $pd_value))){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;
								case($pr_array['condition'] = "containsnot"):
									if ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value))){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;
								case($pr_array['condition'] = "="):
									if (($pr_array['criteria'] == "$pd_value")){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									$ship = $product_data['shipping'];
									break;
								case($pr_array['condition'] = "!="):
									if (($pr_array['criteria'] != "$pd_value")){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;
								case($pr_array['condition'] = ">"):
									// Use a lexical order on relational string operators
									if (($pd_value > $pr_array['criteria'])){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;
								case($pr_array['condition'] = ">="):
									// Use a lexical order on relational string operators
									if (($pd_value >= $pr_array['criteria'])){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;
								case($pr_array['condition'] = "<"):
									// Use a lexical order on relational string operators
									if (($pd_value < $pr_array['criteria'])){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;
								case($pr_array['condition'] = "=<"):
									// Use a lexical order on relational string operators
									if (($pd_value <= $pr_array['criteria'])){
										// Specifically for shipping price rules
										if(is_array($product_data[$pr_array['than_attribute']])){
											$arr_size = (count($product_data[$pr_array['than_attribute']])-1);
											for ($x = 0; $x <= $arr_size; $x++) {
												$product_data[$pr_array['than_attribute']][$x]['price'] = $pr_array['newvalue'];	
											}	
										} else {
											$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
										}
									}
									break;

								case($pr_array['condition'] = "empty"):
									$product_data[$pr_array['than_attribute']] = $pr_array['newvalue'];
									break;
								default:
									break;
							}
						}
					}
				}
			}
		}
		return $product_data;
	}

	/**
	 * Execute project filters (include / exclude) 
	 */
        private function woocommerce_sea_rules( $project_rules, $product_data ){
		$allowed = "1";

		foreach ($project_rules as $pr_key => $pr_array){
			if(array_key_exists($pr_array['attribute'], $product_data)){

				foreach ($product_data as $pd_key => $pd_value){

					// Check is there is a rule on specific attributes
					if(in_array($pd_key, $pr_array)){

						if (is_numeric($pd_value)){
					
							// Rules for numeric values	
							switch ($pr_array['condition']) {
								case($pr_array['condition'] = "contains"):
									if ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "containsnot"):
									if ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "="):
									if (($pd_value == $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value != $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "!="):
									if (($pd_value != $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value == $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = ">"):
									if (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value <= $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
    									break;
								case($pr_array['condition'] = ">="):
									if (($pd_value >= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "<"):
									if (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "=<"):
									if (($pd_value <= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
								case($pr_array['condition'] = "empty"):
									if ((strlen($pd_value) < 1) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif ((strlen($pd_value > 0)) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								default:
									break;
							}
						} elseif (is_array($pd_value)){

							// For now only shipping details are in an array
							foreach ($pd_value as $k => $v){
								foreach ($v as $kk => $vv){
									// Only shipping detail rule can be on price for now
									if($kk == "price"){
										switch ($pr_array['condition']) {
											case($pr_array['condition'] = "contains"):
												if ((preg_match('/'.$pr_array['criteria'].'/', $vv))){
													$allowed = 0;	
												}
												break;
											case($pr_array['condition'] = "containsnot"):
												if ((!preg_match('/'.$pr_array['criteria'].'/', $vv))){
													$allowed = 0;
												}
												break;
											case($pr_array['condition'] = "="):
												if (($vv == $pr_array['criteria'])){
													$allowed = 0;
												}
												break;
											case($pr_array['condition'] = "!="):
												if (($vv != $pr_array['criteria'])){
													$allowed = 0;
												}
												break;
											case($pr_array['condition'] = ">"):
												if (($vv > $pr_array['criteria'])){
													$allowed = 0;
												}
    												break;
											case($pr_array['condition'] = ">="):
												if (($vv >= $pr_array['criteria'])){
													$allowed = 0;
												}
												break;
											case($pr_array['condition'] = "<"):
												if (($vv < $pr_array['criteria'])){
													$allowed = 0;
												}
												break;
											case($pr_array['condition'] = "=<"):
												if (($vv <= $pr_array['criteria'])){
													$allowed = 0;
												}
												break;
											case($pr_array['condition'] = "empty"):
												if (strlen($vv) < 1){
													$allowed = 0;
												}
												break;
											default:
												break;
										}
									}
								}
							}
						} else {
							// Rules for string values
			
							// If case-sensitve is off than lowercase both the criteria and attribute value
							if (array_key_exists('cs', $pr_array)){
								if ($pr_array['cs'] != "on"){
									$pd_value = strtolower($pd_value);
									$pr_array['criteria'] = strtolower($pr_array['criteria']);
								}
							}				

							switch ($pr_array['condition']) {
								case($pr_array['condition'] = "contains"):
									if ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "containsnot"):
									if ((!preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif ((preg_match('/'.$pr_array['criteria'].'/', $pd_value)) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "="):
									if (($pr_array['criteria'] == "$pd_value") && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pr_array['criteria'] != "$pd_value") && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "!="):
									if (($pr_array['criteria'] != "$pd_value") && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pr_array['criteria'] == "$pd_value") && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = ">"):
									// Use a lexical order on relational string operators
									if (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = ">="):
									// Use a lexical order on relational string operators
									if (($pd_value >= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "<"):
									// Use a lexical order on relational string operators
									if (($pd_value < $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "=<"):
									// Use a lexical order on relational string operators
									if (($pd_value <= $pr_array['criteria']) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif (($pd_value > $pr_array['criteria']) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								case($pr_array['condition'] = "empty"):
									if ((strlen($pd_value) < 1) && ($pr_array['than'] == "exclude")){
										$allowed = 0;
									} elseif ((strlen($pd_value > 0)) && ($pr_array['than'] == "include_only")){
										$allowed = 0;
									}
									break;
								default:
									break;
							}
						}
					}
				}
			} else {
				// A empty rule has been set on an attribute that is not in a product anyhow. Still, remove this product from the feed
				if($pr_array['condition'] = "empty"){
					$allowed = 0;
				}
			}
		}
		if ($allowed < 1){
			$product_data = array();
			$product_data = null;
		}
		return $product_data;
	}
}
