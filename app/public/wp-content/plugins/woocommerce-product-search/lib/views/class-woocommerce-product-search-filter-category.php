<?php
/**
 * class-woocommerce-product-search-filter-category.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 2.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'woocommerce_product_search_filter_category' ) ) {
	/**
	 * Renders a product category filter which is returned as HTML and loads
	 * required resources.
	 *
	 * @param array $atts desired filter options
	 * @return string form HTML
	 */
	function woocommerce_product_search_filter_category( $atts = array() ) {
		return WooCommerce_Product_Search_Filter_Category::render( $atts );
	}
}

/**
 * Filter by category.
 */
class WooCommerce_Product_Search_Filter_Category {

	const MAX_PARENT_SEEK = 100;

	private static $instances = 0;

	/**
	 * Adds the shortcode.
	 */
	public static function init() {
		add_shortcode( 'woocommerce_product_filter_category', array( __CLASS__, 'shortcode' ) );
	}

	/**
	 * Enqueues scripts and styles needed to render our search facility.
	 */
	public static function load_resources() {
		$options = get_option( 'woocommerce-product-search', array() );
		$enable_css = isset( $options[WooCommerce_Product_Search::ENABLE_CSS] ) ? $options[WooCommerce_Product_Search::ENABLE_CSS] : WooCommerce_Product_Search::ENABLE_CSS_DEFAULT;

		wp_enqueue_script( 'product-filter' );
		if ( $enable_css ) {
			wp_enqueue_style( 'product-search' );
		}
	}

	/**
	 * [woocommerce_product_filter_category] shortcode handler.
	 *
	 * @param array $atts
	 * @param string $content not used
	 *
	 * @return mixed
	 */
	public static function shortcode( $atts = array(), $content = '' ) {
		return self::render( $atts );
	}

	/**
	 * Renders the category filter.
	 *
	 * @param array $atts
	 * @param array $results
	 *
	 * @return mixed
	 */
	public static function render( $atts = array(), &$results = null ) {
		self::load_resources();

		$atts = shortcode_atts(
			array(
				'auto_expand'        => 'yes',
				'auto_retract'       => 'yes',
				'child_of'           => '',
				'container_class'    => '',
				'container_id'       => null,
				'depth'              => 0,
				'exclude'            => null,
				'expandable_from_depth' => 0, 
				'expander'           => 'yes',
				'filter'             => 'yes',
				'heading'            => null,
				'heading_class'      => null,
				'heading_element'    => 'div',
				'heading_id'         => null,
				'heading_no_results' => '',
				'hide_empty'         => 'yes',
				'hierarchical'       => 'yes',
				'include'            => null,
				'multiple'           => 'no',
				'number'             => null, 
				'order'              => 'ASC',
				'orderby'            => 'name',
				'show'               => 'set',
				'show_ancestors'     => 'yes',
				'show_count'         => 'no',
				'show_heading'       => 'yes',
				'show_names'         => 'yes',
				'show_parent_names'  => 'yes',
				'show_parent_navigation' => 'no',
				'show_parent_thumbnails' => 'no',
				'show_thumbnails'    => 'no',
				'style'              => 'list',
				'taxonomy'           => 'product_cat',
			),
			$atts
		);

		$n               = self::$instances;
		$container_class = '';
		$container_id    = sprintf( 'product-search-filter-category-%d', $n );
		$heading_class   = 'product-search-filter-terms-heading product-search-filter-category-heading';
		$heading_id      = sprintf( 'product-search-filter-category-heading-%d', $n );

		$taxonomy = get_taxonomy( trim( $atts['taxonomy'] ) );

		if ( $taxonomy === false ) {
			$taxonomy = 'product_cat';
		} else {
			if ( $atts['heading'] === null ) {
				if ( !empty( $taxonomy->labels ) && !empty( $taxonomy->labels->singular_name ) ) {
					$atts['heading'] = _x( $taxonomy->labels->singular_name, 'product category singular name', 'woocommerce-product-search' );
				} else {
					$atts['heading'] = _x( $taxonomy->label, 'product category label', 'woocommerce-product-search' );
				}
			}
			$taxonomy = $taxonomy->name;
		}
		$atts['taxonomy'] = $taxonomy;

		$no_valid_include_terms = false;

		if ( is_tax( $taxonomy ) ) {
			global $wp_query;
			if ( isset( $wp_query ) ) {
				$child_of = $wp_query->get_queried_object_id();
				if ( $child_of ) {
					$include = array( $child_of );
					$children = get_term_children( $child_of, $taxonomy );
					if ( !( $children instanceof WP_Error ) ) {
						$include = array_merge( $include, $children );
					}

					if ( !empty( $atts['include'] ) ) {
						$requested_include = $atts['include']; 
						if ( is_string( $requested_include ) ) {
							$requested_include = explode( ',', $requested_include );
						}
						if ( is_array( $requested_include ) ) {
							$entries = array_map( 'trim', $requested_include );
							$term_ids = array();
							foreach( $entries as $entry ) {
								if ( !( $term = get_term_by( 'id', $entry, $taxonomy ) ) ) {
									if ( !( $term = get_term_by( 'slug', $entry, $taxonomy ) ) ) {
										$term = get_term_by( 'name', $entry, $taxonomy );
									}
								}
								if ( $term ) {
									$term_ids[] = $term->term_id;
								}
							}
							$include = array_intersect( $term_ids, $include );

							if ( count( $include ) === 0 ) {
								$no_valid_include_terms = true;
							}
						}
					}
					$atts['include'] = $include;
				}
			}
		}

		$params = array();
		foreach ( $atts as $key => $value ) {
			$is_param = true;
			if ( $value !== null ) {
				if ( is_string( $value ) ) {
					$value = strip_tags( trim( $value ) );
				}
				switch ( $key ) {
					case 'child_of' :
						if ( $value == '{current}' ) {
							$value = '';
							if ( $queried_object = get_queried_object() ) {
								if ( isset( $queried_object->term_id ) ) {
									$value = intval( $queried_object->term_id );
								}
							}
						} else {
							if ( !( $term = get_term_by( 'id', $value, $taxonomy ) ) ) {
								if ( !( $term = get_term_by( 'slug', $value, $taxonomy ) ) ) {
									$term = get_term_by( 'name', $value, $taxonomy );
								}
							}
							if ( $term ) {
								$value = $term->term_id;
							} else {
								$value = null;
							}
						}
						break;
					case 'exclude' :
					case 'include' :
						if ( is_string( $value ) ) {
							$value = explode( ',', $value );
						}
						if ( is_array( $value ) ) {
							$entries = array_map( 'trim', $value );
							$n_entries = 0;
							$term_ids = array();
							foreach( $entries as $entry ) {
								if ( strlen( $entry ) > 0 ) {
									$n_entries++;
									if ( !( $term = get_term_by( 'id', $entry, $taxonomy ) ) ) {
										if ( !( $term = get_term_by( 'slug', $entry, $taxonomy ) ) ) {
											$term = get_term_by( 'name', $entry, $taxonomy );
										}
									}
									if ( $term ) {
										$term_ids[] = $term->term_id;
									}
								}
							}
							if ( $key === 'include' && $n_entries > 0 ) {

								$hide_empty = in_array( strtolower( $atts['hide_empty'] ), array( 'true', 'yes', '1' ) );
								$processed_term_ids = get_terms( array(
									'taxonomy' => $taxonomy,
									'fields' => 'ids',
									'include' => $term_ids,
									'hide_empty' => $hide_empty
								) );
								if ( is_array( $processed_term_ids ) ) { 
									$term_ids = array_intersect( $term_ids, $processed_term_ids );
								} else {
									$term_ids = array();
								}
							}
							if ( count( $term_ids ) === 0 ) {
								if ( $key === 'include' ) {
									if ( $n_entries !== 0 ) {

										$no_valid_include_terms = true;
									}
								}
								$value = null;
							} else {
								$value = $term_ids;
							}
						} else {
							$value = null;
						}
						break;
					case 'auto_expand' :
					case 'auto_retract' :
					case 'expander' :
					case 'filter' :
					case 'hide_empty' :
					case 'hierarchical' :
					case 'multiple' :
					case 'show_ancestors' :
					case 'show_count' :
					case 'show_heading' :
					case 'show_names' :
					case 'show_parent_names' :
					case 'show_parent_navigation' :
					case 'show_parent_thumbnails' :
					case 'show_thumbnails' :
						$value = strtolower( $value );
						$value = $value == 'true' || $value == 'yes' || $value == '1';
						break;
					case 'orderby' :
						switch ( $value ) {
							case 'count' :
							case 'id' :
							case 'term_order' :
							case 'name' :
							case 'slug' :
								break;
							default :
								$value = 'term_order';
						}
						break;
					case 'order' :
						$value = strtoupper( trim( $value ) );
						switch ( $value ) {
							case 'ASC' :
							case 'DESC' :
								break;
							default :
								$value = 'ASC';
						}
						break;
					case 'depth' :
					case 'expandable_from_depth' :
					case 'number' :
						$value = intval( $value );
						break;
					case 'taxonomy' :

						break;

					case 'container_class' :
					case 'container_id' :
					case 'heading_class' :
					case 'heading_id' :
						$value = preg_replace( '/[^a-zA-Z0-9 _.#-]/', '', $value );
						$value = trim( $value );
						$containers[$key] = $value;
						$is_param = false;
						break;

					case 'heading_element' :
						if ( !in_array( $value, WooCommerce_Product_Search_Filter::get_allowed_filter_heading_elements() ) ) {
							$value = 'div';
						}
						break;

					case 'heading' :
					case 'heading_no_results' :
						$value = esc_html( $value );
						break;

					case 'show' :
						$value = trim( strtolower( $value ) );
						switch ( $value ) {
							case 'all' :
							case 'set' :
								break;
							default :
								$value = 'set';
						}
						break;

					case 'style' :
						$value = trim( strtolower( $value ) );
						switch( $value ) {
							case 'list' :
							case 'inline' :
								break;
							default :
								$value = 'list';
						}
						break;
				}
			}
			if ( $is_param ) {
				$params[$key] = $value;
			}
		}

		if ( !empty( $containers['container_class'] ) ) {
			$container_class = $containers['container_class'];
		}
		if ( !empty( $containers['container_id'] ) ) {
			$container_id = $containers['container_id'];
		}
		if ( !empty( $containers['heading_class'] ) ) {
			$heading_class = $containers['heading_class'];
		}
		if ( !empty( $containers['heading_id'] ) ) {
			$heading_id = $containers['heading_id'];
		}

		$list_classes = array();
		switch( $params['style'] ) {
			case 'list' :
				$list_classes[] = 'style-list';
				break;
			case 'inline' :
				$list_classes[] = 'style-inline';
				break;
		}
		if ( $params['show_thumbnails'] ) {
			$list_classes[] = 'show-thumbnails';
		} else {
			$list_classes[] = 'hide-thumbnails';
		}
		if ( $params['show_names'] ) {
			$list_classes[] = 'show-names';
		} else {
			$list_classes[] = 'hide-names';
		}
		$list_class = implode( ' ', $list_classes );

		$params['echo'] = false;
		$params['title_li'] = ''; 
		$params['show_option_none'] = ''; 

		if ( !empty( $params['exclude'] ) ) {
			$exclude_term_ids = $params['exclude'];
			foreach( $params['exclude'] as $term_id ) {
				$exclude_term_ids = array_merge(
					$exclude_term_ids,
					(array) get_terms( $taxonomy, array( 'child_of' => intval( $term_id ), 'fields' => 'ids', 'hide_empty' => 0 ) )
				);
			}
			$params['exclude'] = $exclude_term_ids;
		}

		if ( !empty( $params['include'] ) ) {
			$include_term_ids = $params['include'];
			foreach( $params['include'] as $term_id ) {
				$include_term_ids = array_merge(
					$include_term_ids,
					(array) get_terms( $taxonomy, array( 'child_of' => intval( $term_id ), 'fields' => 'ids', 'hide_empty' => 0 ) )
				);
			}
			$params['include'] = $include_term_ids;
		}

		$current_term_ids = array();
		$current_term_ancestor_ids = array();
		$parent_term_ids  = array();
		if (
			isset( $_REQUEST['ixwpst'] ) &&
			isset( $_REQUEST['ixwpst'][$taxonomy] ) &&
			is_array( $_REQUEST['ixwpst'][$taxonomy] )
		) {
			$include_term_ids = array();
			foreach ( $_REQUEST['ixwpst'][$taxonomy] as $term_id ) {
				if ( ( $term = get_term( $term_id, $taxonomy ) ) && !( $term instanceof WP_Error ) ) {
					if ( ( $term !== null ) && !( $term instanceof WP_Error) ) {
						$include_term_ids[] = $term->term_id;
						$child_term_ids     = get_terms( array( 'taxonomy' => $term->taxonomy, 'fields' => 'ids', 'child_of' => $term->term_id, 'hierarchical' => true ) );

						$include_term_ids   = array_merge( $include_term_ids, $child_term_ids );
						$include_term_ids   = array_unique( $include_term_ids );

						$current_term_ids[] = $term->term_id;
						$current_term_ids   = array_unique( $current_term_ids );

						$i = 0;
						if ( !empty( $term->parent ) ) {
							$parent = get_term( $term->parent, $taxonomy );
							if ( ( $parent !== null ) && !( $parent instanceof WP_Error) ) {
								while ( $parent && $i < self::MAX_PARENT_SEEK ) {
									$current_term_ancestor_ids[] = $parent->term_id;
									$parent_term_ids[$term->term_id][] = $parent->term_id;
									if ( !empty( $parent->parent ) ) {
										$parent = get_term( $parent->parent, $taxonomy );
										if ( ( $parent === null ) || ( $parent instanceof WP_Error) ) {
											break;
										}
									} else {
										break;
									}
									$i++;
								}
								$parent_term_ids[$term->term_id] = array_reverse( $parent_term_ids[$term->term_id] );
							}
						}
					}
				}
			}
			if ( count( $include_term_ids ) > 0 ) {
				if ( !$params['multiple'] && $params['show'] === 'set' ) {
					if ( !empty( $params['include'] ) && is_array( $params['include'] ) ) {
						$include_term_ids = array_intersect( $params['include'], $include_term_ids );
					}

					$params['include'] = $include_term_ids;
				}
			}
			if ( count( $current_term_ids ) > 0 ) {
				$params['current_terms'] = $current_term_ids;
			}
		}

		if ( $no_valid_include_terms ) {
			$params['include'] = array();
		}

		$has_eligible_terms = true;
		if ( is_array( $params['include'] ) && count( $params['include'] ) === 0 ) {

			$params['include'] = array( PHP_INT_MAX );
			$has_eligible_terms = false;
		}

		if ( $params['show_ancestors'] ) {
			if ( is_array( $params['include'] ) ) {
				$include_parent_term_ids = array();
				foreach( $params['include'] as $term_id ) {
					$term = get_term( $term_id, $taxonomy );
					if ( ( $term !== null ) && !( $term instanceof WP_Error ) ) {
						$i = 0;
						if ( !empty( $term->parent ) ) {
							$parent = get_term( $term->parent, $taxonomy );
							if ( ( $parent !== null ) && !( $parent instanceof WP_Error ) ) {
								while ( $parent && $i < self::MAX_PARENT_SEEK ) {
									$include_parent_term_ids[] = $parent->term_id;
									if ( !empty( $parent->parent ) ) {
										$parent = get_term( $parent->parent, $taxonomy );
										if ( ( $parent === null ) || ( $parent instanceof WP_Error ) ) {
											break;
										}
									} else {
										break;
									}
									$i++;
								}
							}
						}
					}
				}
				if ( count( $include_parent_term_ids ) > 0 ) {
					$params['include'] = array_unique( array_merge( $params['include'],  $include_parent_term_ids ) );
				}
			}
		}

		$query_ixwpst = isset( $_GET['ixwpst'] ) ? $_GET['ixwpst'] : null;
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$current_url = remove_query_arg( array( 'ixwpst' ), $current_url );
		if ( $query_ixwpst !== null ) {
			unset( $query_ixwpst[$taxonomy] );
			if ( count( $query_ixwpst ) > 0 ) {
				$current_url = add_query_arg( array( 'ixwpst' => $query_ixwpst ), $current_url );
			}
		}

		$parent_term_urls      = array();
		$added_parent_term_ids = array();
		foreach ( $parent_term_ids as $tid => $ids ) {
			foreach ( $ids as $id ) {
				if ( !in_array( $id, $added_parent_term_ids ) ) {
					$parent_term_url = add_query_arg( array( 'ixwpst' => array( $taxonomy => array( $id ) ) ), $current_url );
					$parent_term_urls[$id] = $parent_term_url;
				}
			}
		}

		$output_prefix = apply_filters(
			"woocommerce_product_search_filter_{$taxonomy}_prefix",
			sprintf(
				'<div id="%s" class="product-search-filter-terms %s" data-multiple="%s">',
				esc_attr( $container_id ),
				esc_attr( $container_class ),
				$params['multiple'] ? '1' : ''
			)
		);

		$open_inside_output = sprintf( '<ul class="product-categories product-search-filter-items product-search-filter-category product-search-filter-%s %s">', esc_attr( $taxonomy ), esc_attr( $list_class ) );
		$close_inside_output = '</ul>';

		$clear_output = '';
		if ( $has_eligible_terms && isset( $_GET['ixwpst'] ) && isset( $_GET['ixwpst'][$taxonomy] ) ) {
			$clear_output .= sprintf(
				'<li data-term="" class="cat-item-all nav-back product-search-%s-filter-item"><a href="%s">%s</a></li>',
				esc_attr( $taxonomy ), 
				esc_url( $current_url ),
				__( 'Clear', 'woocommerce-product-search' )
			);
		}

		$parent_terms_output = '';
		if ( $params['show_parent_navigation'] ) {
			foreach ( $parent_term_urls as $parent_term_id => $parent_term_url ) {
				if ( $parent_term = get_term( $parent_term_id, $taxonomy ) ) {
					if ( ( $parent !== null ) && !( $parent instanceof WP_Error) ) {
						$parent_terms_output .= sprintf(
							'<li data-term="%s" class="cat-item-parent nav-back product-search-%s-filter-item"><a href="%s">%s</a></li>',
							esc_attr( $parent_term->term_id ), 
							esc_attr( $taxonomy ), 
							esc_url( $parent_term_url ),
							( $params['show_parent_thumbnails'] ? WooCommerce_Product_Search_Thumbnail::term_thumbnail( $parent_term ) : '' ) .
							( $params['show_parent_names'] ? '<span class="term-name">' . esc_html( _x( $parent_term->name, 'product category name', 'woocommerce-product-search' ) ) . '</span>' : '' )
						);
					}
				}
			}
		}

		$elements_displayed = 0;
		$terms_output = '';
		if ( $has_eligible_terms ) {
			$root_class = sprintf( 'product-categories product-search-filter-items product-search-filter-category product-search-filter-%s %s', $taxonomy, $list_class );
			$params['fields'] = 'ids';
			$term_ids = WooCommerce_Product_Search_Service::get_term_ids_for_request( $params, array( $taxonomy ) ); 
			$node = new WooCommerce_Product_Search_Term_Node(
				$term_ids,
				$taxonomy,
				array(
					'hide_empty' => $params['hide_empty'],
					'bubble_down' => false 
				)
			);
			$node->sort( $params['orderby'], $params['order'] );
			$node_renderer = new WooCommerce_Product_Search_Term_Node_Tree_Renderer( array(
				'auto_expand'               => $params['auto_expand'],
				'auto_retract'              => $params['auto_retract'],
				'current_term_ids'          => $current_term_ids,
				'current_term_ancestor_ids' => $current_term_ancestor_ids,
				'expandable_from_depth'     => $params['expandable_from_depth'],
				'expander'                  => $params['expander'],
				'hierarchical'              => $params['hierarchical'],
				'render_root_container'     => false,
				'root_class'                => $root_class,
				'show_count'                => $params['show_count'],
				'show_names'                => $params['show_names'],
				'show_thumbnails'           => $params['show_thumbnails'],
			) );
			$terms_output = apply_filters(
				"woocommerce_product_search_filter_{$taxonomy}_content",
				$node_renderer->render( $node ),
				$atts,
				$params
			);
			$elements_displayed = $node_renderer->get_elements_displayed();
			unset( $node_renderer );
			unset( $node );
		}

		$heading_output = '';
		if ( $params['show_heading'] ) {
			$heading_output .= sprintf(
				'<%s class="%s" id="%s">%s</%s>',
				esc_html( $params['heading_element'] ),
				esc_attr( $heading_class ),
				esc_attr( $heading_id ),
				$elements_displayed > 0 ? esc_html( $params['heading'] ) : esc_html( $params['heading_no_results'] ),
				esc_html( $params['heading_element'] )
			);
		}

		if ( $elements_displayed === 0 ) {
			$clear_output = '';
		}

		$output = $output_prefix;
		$output .= $heading_output;
		$output .= $open_inside_output;
		$output .= $clear_output;
		$output .= $parent_terms_output;
		$output .= $terms_output;
		$output .= $close_inside_output;

		$output .= apply_filters(
			"woocommerce_product_search_filter_{$taxonomy}_suffix",
			'</div>'
		);

		$js_object = sprintf( '{taxonomy:"%s"', esc_attr( $taxonomy ) );
		$js_object .= ',multiple:' . ( $params['multiple'] ? 'true' : 'false' );
		$js_object .= ',filter:' . ( $params['filter'] ? 'true' : 'false' );
		$js_object .= sprintf( ',show:"%s"', esc_attr( $params['show'] ) );
		$js_object .= '}';
		$output .= '<script type="text/javascript">';
		$output .= 'if ( typeof jQuery !== "undefined" ) {';
		$output .= 'jQuery(document).ready(function(){';
		$output .= 'if ( typeof ixwpsf !== "undefined" && typeof ixwpsf.taxonomy !== "undefined" ) {';
		$output .= 'ixwpsf.taxonomy.push(' . $js_object . ');';
		$output .= '}';
		$output .= '});'; 
		$output .= '}'; 
		$output .= '</script>';

		WooCommerce_Product_Search_Filter::filter_added();

		self::$instances++;

		return $output;
	}

}
WooCommerce_Product_Search_Filter_Category::init();
