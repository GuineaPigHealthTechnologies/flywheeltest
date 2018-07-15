<?php
function ds_dc_woo_customizer($wp_customize) {
$wp_customize->add_panel( 'divi_commerce_panel', array(
'priority' => 30,
'capability' => 'edit_theme_options',
'title' => __('Divi Commerce' ),
'description' => __('Customize WooCommerce with these Divi Commerce theme options' ),
));
$wp_customize->add_section('dc_layouts', array(    
    'priority' => 1,
    'title' => __('Global Layouts' ),
    'panel' => 'divi_commerce_panel',
    ));

/*Header add to cart*/
  $wp_customize->add_section('dc_header_atc', array(    
    'priority' => 1,
    'title' => __('Header | Header add to cart' ),
    'panel' => 'divi_commerce_panel',
    ));
    /*Add view details button to loop*/
    $wp_customize->add_setting('dc_add_header_atc', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_atc', array(
    'label' => __('Add add to cart button', 'divi_commerce_options'),
    'section' => 'dc_header_atc',
    'type' => 'checkbox',
    'settings' => 'dc_add_header_atc'
    ));
    /*header add to cart background*/
   /*ADD TO CART BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_add_header_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_add_header_bg_colour', array(
    'label' => __('Add to Cart background colour'),
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_add_header_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_add_header_border_colour', array(
    'label' => __('Add to Cart border colour'),
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_add_header_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_add_header_text_colour', array(
    'label' => __('Add to Cart text colour'),
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_add_header_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_font_size', array(
    'label' => __('Add to cart Font Size'),
    'section' => 'dc_header_atc',
    'type' => 'option',
    'settings' => 'dc_add_header_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_add_header_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_fw', array(
    'type' => 'select',
    'label' => 'Add to cart font weight',
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_add_header_act_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_act_tt', array(
    'type' => 'select',
    'label' => 'Add to Cart Text Transform',
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_act_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_add_header_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_border_width', array(
    'label' => __('add to cart border width'),
    'section' => 'dc_header_atc',
    'type' => 'option',
    'settings' => 'dc_add_header_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_add_header_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_add_header_bg_colour_h', array(
    'label' => __('Add to Cart Hover background colour'),
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_add_header_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_add_header_border_colour_h', array(
    'label' => __('Add to Cart Hover border colour'),
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_add_header_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_add_header_text_colour_h', array(
    'label' => __('Add to Cart text Hover colour'),
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_add_header_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_font_size_h', array(
    'label' => __('Add to cart Hover Font Size'),
    'section' => 'dc_header_atc',
    'type' => 'option',
    'settings' => 'dc_add_header_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_add_header_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_fw_h', array(
    'type' => 'select',
    'label' => 'Add to cart font weight',
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_add_header_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_tt_h', array(
    'type' => 'select',
    'label' => 'Add to Cart Hover Text Transform',
    'section' => 'dc_header_atc',
    'settings' => 'dc_add_header_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_add_header_border_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_header_border_width_h', array(
    'label' => __('add to cart Hover border width'),
    'section' => 'dc_header_atc',
    'type' => 'option',
    'settings' => 'dc_add_header_border_width_h'
    ));
/*Header hover cart*/
  $wp_customize->add_section('dc_header_cart', array(    
    'priority' => 1,
    'title' => __('Header | Header cart' ),
    'panel' => 'divi_commerce_panel',
    ));
    /*Add view details button to loop*/
    $wp_customize->add_setting('dc_add_header_cart', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    /**/
    $wp_customize->add_control('dc_add_header_cart', array(
    'label' => __('Add mini cart to header', 'divi_commerce_options'),
    'section' => 'dc_header_cart',
    'type' => 'checkbox',
    'settings' => 'dc_add_header_cart'
    ));
    /*header add to cart background*/
    $wp_customize->add_setting('dc_header_cart_icon_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_icon_colour', array(
    'label' => __('Cart icon Background Colour'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_cart_icon_colour',
    ) ) );
    $wp_customize->add_setting('dc_header_mini_cart_background', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_mini_cart_background', array(
    'label' => __('container Background Colour'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_mini_cart_background',
    ) ) );
    $wp_customize->add_setting('dc_header_mini_cart_heading_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_mini_cart_heading_colour', array(
    'label' => __('heading Colour'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_mini_cart_heading_colour',
    ) ) );
    $wp_customize->add_setting('dc_header_mini_cart_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_mini_cart_text_colour', array(
    'label' => __('Text Colour'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_mini_cart_text_colour',
    ) ) );
    $wp_customize->add_setting('dc_header_mini_cart_remove_icon_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_mini_cart_remove_icon_colour', array(
    'label' => __('Remove icon colour'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_mini_cart_remove_icon_colour',
    ) ) );
    $wp_customize->add_setting('dc_header_mini_cart_remove_icon_bg', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_mini_cart_remove_icon_bg', array(
    'label' => __('remove icon background'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_mini_cart_remove_icon_bg',
    ) ) );
    $wp_customize->add_setting('dc_header_mini_cart_seperator', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_mini_cart_seperator', array(
    'label' => __('Seperator'),
    'section' => 'dc_header_cart',
    'settings' => 'dc_header_mini_cart_seperator',
    ) ) );
    /**/
   /*header add to cart background*/
   $wp_customize->add_setting('dc_loop_atc_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_bg_colour', array(
    'label' => __('background colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_border_colour', array(
    'label' => __('border colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_text_colour', array(
    'label' => __('text colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_loop_atc_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_font_size', array(
    'label' => __('Font Size'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_loop_atc_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_fw', array(
    'type' => 'select',
    'label' => 'font weight',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_loop_atc_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_tt', array(
    'type' => 'select',
    'label' => 'Text Transform',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_loop_atc_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_border_width', array(
    'label' => __('border width'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_bg_colour_h', array(
    'label' => __('Hover background colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_border_colour_h', array(
    'label' => __('Hover border colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_text_colour_h', array(
    'label' => __('text Hover colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_loop_atc_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_font_size_h', array(
    'label' => __('Hover Font Size'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_loop_atc_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_fw_h', array(
    'type' => 'select',
    'label' => 'hover font weight',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_loop_atc_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_tt_h', array(
    'type' => 'select',
    'label' => 'Hover Text Transform',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_loop_atc_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_border_width_h', array(
    'label' => __('Hover border width'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_border_width_h'
    ));
/*button 1*/
   $wp_customize->add_section('dc_header_cart_but_1', array(    
    'priority' => 1,
    'title' => __('Header | Header cart | Button 1' ),
    'panel' => 'divi_commerce_panel',
    ));
      $wp_customize->add_setting('dc_header_cart_but_1_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_1_bg_colour', array(
    'label' => __('background colour'),
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_1_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_1_border_colour', array(
    'label' => __('border colour'),
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_1_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_1_text_colour', array(
    'label' => __('text colour'),
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_header_cart_but_1_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_font_size', array(
    'label' => __('Font Size'),
    'section' => 'dc_header_cart_but_1',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_1_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_header_cart_but_1_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_fw', array(
    'type' => 'select',
    'label' => 'font weight',
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_header_cart_but_1_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_tt', array(
    'type' => 'select',
    'label' => 'Text Transform',
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_header_cart_but_1_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_border_width', array(
    'label' => __('border width'),
    'section' => 'dc_header_cart_but_1',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_1_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_1_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_1_bg_colour_h', array(
    'label' => __('Hover background colour'),
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_1_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_1_border_colour_h', array(
    'label' => __('Hover border colour'),
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_1_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_1_text_colour_h', array(
    'label' => __('text Hover colour'),
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_header_cart_but_1_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_font_size_h', array(
    'label' => __('Hover Font Size'),
    'section' => 'dc_header_cart_but_1',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_1_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_header_cart_but_1_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_fw_h', array(
    'type' => 'select',
    'label' => 'hover font weight',
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_header_cart_but_1_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_tt_h', array(
    'type' => 'select',
    'label' => 'Hover Text Transform',
    'section' => 'dc_header_cart_but_1',
    'settings' => 'dc_header_cart_but_1_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_header_cart_but_1_border_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_1_border_width_h', array(
    'label' => __('Hover border width'),
    'section' => 'dc_header_cart_but_1',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_1_border_width_h'
    ));
/*button 2*/
   $wp_customize->add_section('dc_header_cart_but_2', array(    
    'priority' => 1,
    'title' => __('Header | Header cart | Button 2' ),
    'panel' => 'divi_commerce_panel',
    ));
     $wp_customize->add_setting('dc_header_cart_but_2_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_2_bg_colour', array(
    'label' => __('background colour'),
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_2_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_2_border_colour', array(
    'label' => __('border colour'),
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_2_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_2_text_colour', array(
    'label' => __('text colour'),
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_header_cart_but_2_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_font_size', array(
    'label' => __('Font Size'),
    'section' => 'dc_header_cart_but_2',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_2_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_header_cart_but_2_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_fw', array(
    'type' => 'select',
    'label' => 'font weight',
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_header_cart_but_2_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_tt', array(
    'type' => 'select',
    'label' => 'Text Transform',
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_header_cart_but_2_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_border_width', array(
    'label' => __('border width'),
    'section' => 'dc_header_cart_but_2',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_2_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_2_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_2_bg_colour_h', array(
    'label' => __('Hover background colour'),
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_2_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_2_border_colour_h', array(
    'label' => __('Hover border colour'),
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_header_cart_but_2_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_header_cart_but_2_text_colour_h', array(
    'label' => __('text Hover colour'),
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_header_cart_but_2_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_font_size_h', array(
    'label' => __('Hover Font Size'),
    'section' => 'dc_header_cart_but_2',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_2_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_header_cart_but_2_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_fw_h', array(
    'type' => 'select',
    'label' => 'hover font weight',
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_header_cart_but_2_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_tt_h', array(
    'type' => 'select',
    'label' => 'Hover Text Transform',
    'section' => 'dc_header_cart_but_2',
    'settings' => 'dc_header_cart_but_2_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_header_cart_but_2_border_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_header_cart_but_2_border_width_h', array(
    'label' => __('Hover border width'),
    'section' => 'dc_header_cart_but_2',
    'type' => 'option',
    'settings' => 'dc_header_cart_but_2_border_width_h'
    ));
/*product page sections*/
   $wp_customize->add_section('dc_product_sections', array(    
    'priority' => 1,
    'title' => __('Single Product | Section Display' ),
    'panel' => 'divi_commerce_panel',
    ));
    $wp_customize->add_setting('dc_enable_custom_top', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_enable_custom_top', array(
    'type' => 'checkbox',
    'label' => 'Enable Custom top',
    'section' => 'dc_product_sections',
    'settings' => 'dc_enable_custom_top',
    ));
    $wp_customize->add_setting('dc_custom_top_layout', array(
    'capability' => 'edit_theme_options',
    'type' => 'option',
    ));
    $wp_customize->add_control('dc_custom_top_layout', array(
    'label' => __('Set your custom top page layout'),
    'section' => 'dc_product_sections',
    'type' => 'dropdown-pages',
    'settings' => 'dc_custom_top_layout',
    ));
    $wp_customize->add_setting('dc_enable_custom_bottom', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_enable_custom_bottom', array(
    'type' => 'checkbox',
    'label' => 'Enable custom bottom',
    'section' => 'dc_product_sections',
    'settings' => 'dc_enable_custom_bottom',
    ));
    $wp_customize->add_setting('dc_custom_bottom_layout', array(
    'capability' => 'edit_theme_options',
    'type' => 'option',
    ));
    $wp_customize->add_control('dc_custom_bottom_layout', array(
    'label' => __('Set your custom bottom page layout'),
    'section' => 'dc_product_sections',
    'type' => 'dropdown-pages',
    'settings' => 'dc_custom_bottom_layout',
    ));
    /*Hide breadcrumbs*/
    $wp_customize->add_setting('dc_hide_pro_bread', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_bread', array(
    'type' => 'checkbox',
    'label' => 'Remove breadcrumbs',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_bread',
    ));
    /*Hide image*/
    $wp_customize->add_setting('dc_hide_default_img', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_default_img', array(
    'type' => 'checkbox',
    'label' => 'Remove image',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_default_img',
    ));
    /*Hide title*/
    $wp_customize->add_setting('dc_hide_pro_title', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_title', array(
    'type' => 'checkbox',
    'label' => 'Remove title',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_title',
    ));
    /*Hide price*/
    $wp_customize->add_setting('dc_hide_pro_price', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_price', array(
    'type' => 'checkbox',
    'label' => 'Remove price',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_price',
    ));
    /*Hide summary*/
    $wp_customize->add_setting('dc_hide_pro_info', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_info', array(
    'type' => 'checkbox',
    'label' => 'Remove summary',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_info',
    ));
    /*Hide quantity*/
    $wp_customize->add_setting('dc_hide_pro_qty', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_qty', array(
    'type' => 'checkbox',
    'label' => 'Remove quantity',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_qty',
    ));
    /*Hide add to cart*/
    $wp_customize->add_setting('dc_hide_pro_atc_but', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_atc_but', array(
    'type' => 'checkbox',
    'label' => 'Remove add to cart',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_atc_but',
    ));
    /*Hide meta*/
    $wp_customize->add_setting('dc_hide_pro_meta', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_meta', array(
    'type' => 'checkbox',
    'label' => 'Remove Meta',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_meta',
    ));
    $wp_customize->add_setting('dc_hide_whole_summary', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_whole_summary', array(
    'type' => 'checkbox',
    'label' => 'Remove entire summary',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_whole_summary',
    ));
    /*Hide tabs*/
    $wp_customize->add_setting('dc_hide_pro_tab', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_pro_tab', array(
    'type' => 'checkbox',
    'label' => 'Remove tabs',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_pro_tab',
    ));
    /*Hide related products*/
    $wp_customize->add_setting('dc_hide_related_pro', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_related_pro', array(
    'type' => 'checkbox',
    'label' => 'Remove Related Products',
    'section' => 'dc_product_sections',
    'settings' => 'dc_hide_related_pro',
    ));

/*Single Product Basic Styling*/
  $wp_customize->add_section('dc_product_basics', array(    
    'priority' => 1,
    'title' => __('Single Product | Product basics' ),
    'panel' => 'divi_commerce_panel',
    ));
    /*SINGLE PRODUCT PAGE BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_base_page_background', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_base_page_background', array(
    'label' => __('Single Product Page background colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_page_background',
    ) ) );
    /*BREADCRUMB TEXT COLOUR*/
    $wp_customize->add_setting('dc_base_breadcrumbs_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_base_breadcrumbs_colour', array(
    'label' => __('Breadcrumb text colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_breadcrumbs_colour',
    ) ) );
    /*SUMMARY CONTAINER BACKGROUND*/
    $wp_customize->add_setting('dc__base_info_con', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_base_info_con', array(
    'label' => __('summary container background'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_info_con',
    ) ) );
    /*SUMMARY CONTAINER BORDER WIDTH*/
    $wp_customize->add_setting('dc_base_info_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_info_border_width', array(
    'label' => __('summary container border width'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_base_info_border_width'
    ));
    /*INFO BORDER COLOUR*/
    $wp_customize->add_setting('dc__base_info_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_info_border_colour', array(
    'label' => __('info border colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_info_border_colour',
    ) ) );
    /*INFO CONTAINER PADDING*/
    $wp_customize->add_setting('dc_base_info_padding', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_info_padding', array(
    'label' => __('info container padding'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_base_info_padding'
    ));
    /*IMAGE BORDER COLOUR*/
    $wp_customize->add_setting('dc__base_img_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_img_border_colour', array(
    'label' => __('image border colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_img_border_colour',
    ) ) );
    /*MAIN IMAGE BORDER WIDTH*/
    $wp_customize->add_setting('dc_base_main_img_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_main_img_border_width', array(
    'label' => __('main image border width'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_base_main_img_border_width'
    ));
    /*TITLE FONT SIZE*/
    $wp_customize->add_setting('dc_base_title_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_title_font_size', array(
    'label' => __('title font size'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_base_title_font_size'
    ));
    /*TITLE COLOUR*/
    $wp_customize->add_setting('dc__base_title_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_title_colour', array(
    'label' => __('title colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_title_colour',
    ) ) );
    /*TITLE FONT WEIGHT*/
    $wp_customize->add_setting('dc_base_title_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_title_fw', array(
    'type' => 'select',
    'label' => 'Title font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_title_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*TITLE TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_base_title_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_title_tt', array(
    'type' => 'select',
    'label' => 'Title text transform',
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_title_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*PRICE COLOUR*/
    $wp_customize->add_setting('dc__base_price_colour', array(
    'transport' => 'refresh',                                            )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_price_colour', array(
    'label' => __('Price colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_price_colour',
    ) ) );
    /*PRICE FONT SIZE*/
    $wp_customize->add_setting('dc_base_price_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_price_font_size', array(
    'label' => __('Price font size'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_base_price_font_size'
    ));
    /*PRICE FONT WEIGHT*/
    $wp_customize->add_setting('dc_price_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_price_fw', array(
    'type' => 'select',
    'label' => 'Price font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_price_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*SHORT DESCRIPTION TEXT FONT SIZE*/
    $wp_customize->add_setting('dc_main_text_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_main_text_font_size', array(
    'label' => __('Short Description Text Font Size'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_main_text_font_size'
    ));
    /*SHORT DESCRIPTION TEXT COLOUR*/
    $wp_customize->add_setting('dc_main_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_main_text_colour', array(
    'label' => __('Short Description Text Colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_main_text_colour',
    ) ) );
    /*SHORT DESCRIPTION FONT WEIGHT*/
    $wp_customize->add_setting('dc_main_text_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_main_text_fw', array(
    'type' => 'select',
    'label' => 'Short Description font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_main_text_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*QUANTITY BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_quantity_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_quantity_bg_colour', array(
    'label' => __('Quantity background colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_quantity_bg_colour',
    ) ) );
    /*QUANTITY TEXT COLOURe*/
    $wp_customize->add_setting('dc_quantity_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_quantity_text_colour', array(
    'label' => __('Quantity text colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_quantity_text_colour',
    ) ) );
    /*ADD TO CART BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_atc_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_atc_bg_colour', array(
    'label' => __('Add to Cart background colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_atc_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_atc_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_atc_border_colour', array(
    'label' => __('Add to Cart border colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_atc_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_atc_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_atc_text_colour', array(
    'label' => __('Add to Cart text colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_atc_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_atc_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_atc_font_size', array(
    'label' => __('Add to cart Font Size'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_atc_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_act_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_act_fw', array(
    'type' => 'select',
    'label' => 'Add to cart font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_act_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_act_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_act_tt', array(
    'type' => 'select',
    'label' => 'Add to Cart Text Transform',
    'section' => 'dc_product_basics',
    'settings' => 'dc_act_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_act_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_act_border_width', array(
    'label' => __('add to cart border width'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_act_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_atc_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_atc_bg_colour_h', array(
    'label' => __('Add to Cart Hover background colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_atc_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_atc_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_atc_border_colour_h', array(
    'label' => __('Add to Cart Hover border colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_atc_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_atc_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_atc_text_colour_h', array(
    'label' => __('Add to Cart text Hover colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc_atc_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_atc_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_atc_font_size_h', array(
    'label' => __('Add to cart Hover Font Size'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_atc_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_act_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_act_fw_h', array(
    'type' => 'select',
    'label' => 'Add to cart font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_act_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_act_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_act_tt_h', array(
    'type' => 'select',
    'label' => 'Add to Cart Hover Text Transform',
    'section' => 'dc_product_basics',
    'settings' => 'dc_act_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_act_border_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_act_border_width_h', array(
    'label' => __('add to cart Hover border width'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_act_border_width_h'
    ));
    /*META FONT SIZE*/
    $wp_customize->add_setting('dc_base_meta_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_meta_font_size', array(
    'label' => __('Meta font size'),
    'section' => 'dc_product_basics',
    'type' => 'option',
    'settings' => 'dc_base_meta_font_size'
    ));
    /*META COLOUR*/
    $wp_customize->add_setting('dc__base_meta_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_meta_colour', array(
    'label' => __('Meta colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_meta_colour',
    ) ) );
    /*META FONT WEIGHT*/
    $wp_customize->add_setting('dc_base_meta_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_meta_fw', array(
    'type' => 'select',
    'label' => 'Meta font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_meta_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*META text transform*/
    $wp_customize->add_setting('dc_base_meta_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_meta_tt', array(
    'type' => 'select',
    'label' => 'meta text transform',
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_meta_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*META LINK COLOUR*/
    $wp_customize->add_setting('dc__base_metalink_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_metalink_colour', array(
    'label' => __('Meta link colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_metalink_colour',
    ) ) );
    /*META FONT WEIGHT*/
    $wp_customize->add_setting('dc_base_metalink_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_base_metalink_fw', array(
    'type' => 'select',
    'label' => 'Meta font weight',
    'section' => 'dc_product_basics',
    'settings' => 'dc_base_metalink_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    /*META LINK HOVER COLOUR*/
    $wp_customize->add_setting('dc__base_metalink_colour_h', array(
    'transport' => 'refresh',                                            )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc__base_metalink_colour_h', array(
    'label' => __('Meta link hover colour'),
    'section' => 'dc_product_basics',
    'settings' => 'dc__base_metalink_colour_h',
    ) ) );
//tabs customizer
  $wp_customize->add_section('dc_tabs_custom', array(
    'priority' => 5,
    'title' => __('Single Product | Tabs customizer' ),
    'panel' => 'divi_commerce_panel',
    ));
    $wp_customize->add_setting('dc_hide_tabs_bar', array(
    'default' => false,'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_hide_tabs_bar', array(
    'label' => __('Hide tabs', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'type' => 'checkbox',
    'settings' => 'dc_hide_tabs_bar'
    ));
    $wp_customize->add_setting('dc_tabs_main_bg', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_setting('dc_tab_main_header', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tab_main_header', array(
    'label' => __('Hide main header', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'type' => 'checkbox',
    'settings' => 'dc_tab_main_header'
    ));
     $wp_customize->add_setting('dc_tab_main_header_padding', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tab_main_header_padding', array(
    'label' => __('Remove tab padding', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'type' => 'checkbox',
    'settings' => 'dc_tab_main_header_padding'
    ));
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tabs_main_bg', array(
    'label' => __('Select tabs content area background', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tabs_main_bg',   
    ) ) );
    $wp_customize->add_setting('dc_tabs_bg', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tabs_bg_color_control', array(
    'label' => __('Select tabs background colour'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tabs_bg',   
    ) ) );
    $wp_customize->add_setting('dc_tabs_margin_top', array(
    'default' => '0px',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tabs_margin_top', array(
    'label' => __('tab area margin top' ),
    'section' => 'dc_tabs_custom',
    'type' => 'option',
    'settings' => 'dc_tabs_margin_top'
    ));
    $wp_customize->add_setting('dc_tabs_margin_bottom', array(
    'default' => '0px',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tabs_margin_bottom', array(
    'label' => __('tab area margin bottom' ),
    'section' => 'dc_tabs_custom',
    'type' => 'option',
    'settings' => 'dc_tabs_margin_bottom'
    ));
    $wp_customize->add_setting('dc_tabs_border_colour', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tabs_border_colour', array(
    'label' => __('Select tabs area border colour', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tabs_border_colour',   
    ) ) );
    $wp_customize->add_setting('dc_tabs_position', array(
    'default' => 'left',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tabs_position', array(
    'type' => 'select',
    'label' => 'Tabs position',
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tabs_position',
    'choices' => array(
    'left' => 'left',
    'center' => 'center',
    'right' => 'right',
    ),
    ));
    $wp_customize->add_setting('dc_tab_n_bg_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tab_n_bg_color', array(
    'label' => __('tabs background colour', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tab_n_bg_color',   
    ) ) );
    $wp_customize->add_setting('dc_tab_a_bg_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tab_a_bg_color', array(
    'label' => __('Tabs active background colour', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tab_a_bg_color',   
    ) ) );
    $wp_customize->add_setting('dc_tab_n_text_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tab_n_text_color', array(
    'label' => __('title colour', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tab_n_text_color',   
    ) ) );
    $wp_customize->add_setting('dc_tab_a_text_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_tab_a_text_color', array(
    'label' => __('Title active colour', 'divi_commerce_options'),
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tab_a_text_color',   
    ) ) );
    $wp_customize->add_setting('dc_tab_title_tt', array(
    'default' => 'uppercase',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tab_title_tt', array(
    'type' => 'select',
    'label' => 'Title Text Transform',
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tab_title_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    $wp_customize->add_setting('dc_tab_title_fw', array(
    'default' => 'bold',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_tab_title_fw', array(
    'type' => 'select',
    'label' => 'Title font weight',
    'section' => 'dc_tabs_custom',
    'settings' => 'dc_tab_title_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
/*Related products*/
  $wp_customize->add_section('dc_related_products', array(    
    'title' => __('Single Product | Related Products' ),
    'panel' => 'divi_commerce_panel',
    ));
     /*header add to cart background*/
    $wp_customize->add_setting('dc_related_products_bg', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_related_products_bg', array(
    'label' => __('Background Colour'),
    'section' => 'dc_related_products',
    'settings' => 'dc_related_products_bg',
    ) 
    ));
    $wp_customize->add_setting('dc_related_products_title_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_related_products_title_font_size', array(
    'label' => __('Title font Size'),
    'section' => 'dc_related_products',
    'type' => 'option',
    'settings' => 'dc_related_products_title_font_size'
    ));
    $wp_customize->add_setting('dc_related_products_title_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_related_products_title_color', array(
    'label' => __('title colour', 'divi_commerce_options'),
    'section' => 'dc_related_products',
    'settings' => 'dc_related_products_title_color',   
    ) ) );
    $wp_customize->add_setting('dc_related_products_title_tt', array(
    'default' => 'uppercase',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_related_products_title_tt', array(
    'type' => 'select',
    'label' => 'Title Text Transform',
    'section' => 'dc_related_products',
    'settings' => 'dc_related_products_title_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    $wp_customize->add_setting('dc_related_products_title_fw', array(
    'default' => 'bold',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_related_products_title_fw', array(
    'type' => 'select',
    'label' => 'Title font weight',
    'section' => 'dc_related_products',
    'settings' => 'dc_related_products_title_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    $wp_customize->add_setting('dc_related_products_price_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_related_products_price_font_size', array(
    'label' => __('price font Size'),
    'section' => 'dc_related_products',
    'type' => 'option',
    'settings' => 'dc_related_products_price_font_size'
    ));
    $wp_customize->add_setting('dc_related_products_price_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_related_products_price_color', array(
    'label' => __('price colour', 'divi_commerce_options'),
    'section' => 'dc_related_products',
    'settings' => 'dc_loop_basic_price_color',   
    ) ) );
    $wp_customize->add_setting('dc_related_products_price_color', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_related_products_price_color', array(
    'label' => __('Price Colour'),
    'section' => 'dc_related_products',
    'settings' => 'dc_related_products_price_color',
    ) 
    ));
    $wp_customize->add_setting('dc_related_products_price_fw', array(
    'default' => 'bold',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_related_products_price_fw', array(
    'type' => 'select',
    'label' => 'price font weight',
    'section' => 'dc_related_products',
    'settings' => 'dc_related_products_price_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
/*Loop basics*/
  $wp_customize->add_section('dc_loop_basic', array(    
    'title' => __('Product Loop | Basics' ),
    'panel' => 'divi_commerce_panel',
    ));
    /*header add to cart background*/
   $wp_customize->add_setting('dc_loop_basic_bg', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_basic_bg', array(
    'label' => __('Background Colour'),
    'section' => 'dc_loop_basic',
    'settings' => 'dc_loop_basic_bg',
    ) 
    ));
    $wp_customize->add_setting('dc_loop_basic_title_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_basic_title_font_size', array(
    'label' => __('Title font Size'),
    'section' => 'dc_loop_basic',
    'type' => 'option',
    'settings' => 'dc_loop_basic_title_font_size'
    ));
    $wp_customize->add_setting('dc_loop_basic_title_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_basic_title_color', array(
    'label' => __('title colour', 'divi_commerce_options'),
    'section' => 'dc_loop_basic',
    'settings' => 'dc_loop_basic_title_color',   
    ) ) );
    $wp_customize->add_setting('dc_loop_basic_title_tt', array(
    'default' => 'uppercase',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_basic_title_tt', array(
    'type' => 'select',
    'label' => 'Title Text Transform',
    'section' => 'dc_loop_basic',
    'settings' => 'dc_loop_basic_title_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    $wp_customize->add_setting('dc_loop_basic_title_fw', array(
    'default' => 'bold',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_basic_title_fw', array(
    'type' => 'select',
    'label' => 'Title font weight',
    'section' => 'dc_loop_basic',
    'settings' => 'dc_loop_basic_title_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    $wp_customize->add_setting('dc_loop_basic_price_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_basic_price_font_size', array(
    'label' => __('price font Size'),
    'section' => 'dc_loop_basic',
    'type' => 'option',
    'settings' => 'dc_loop_basic_price_font_size'
    ));
    $wp_customize->add_setting('dc_loop_basic_price_color', array(
    'transport' => 'refresh',
    )) ;   
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_basic_price_color', array(
    'label' => __('price colour', 'divi_commerce_options'),
    'section' => 'dc_loop_basic',
    'settings' => 'dc_loop_basic_price_color',   
    ) ) );
    $wp_customize->add_setting('dc_loop_basic_price_fw', array(
    'default' => 'bold',
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_basic_price_fw', array(
    'type' => 'select',
    'label' => 'price font weight',
    'section' => 'dc_loop_basic',
    'settings' => 'dc_loop_basic_price_fw',
    'choices' => array(
    'bold' => 'bold',
    'bolder' => 'bolder',
    'normal' => 'normal',
    ),
    ));
    $wp_customize->add_setting('dc_loop_button_full', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_button_full', array(
    'label' => __('Add add to cart button', 'divi_commerce_options'),
    'section' => 'dc_loop_basic',
    'type' => 'checkbox',
    'settings' => 'dc_loop_button_full'
    ));
/*loop description*/
  //$wp_customize->add_section('dc_loop_description', array(    
    //'title' => __('Product Loop | description' ),
    //'panel' => 'divi_commerce_panel',
    //));
    /*Add view details button to loop*/
    //$wp_customize->add_setting('dc_add_desc_loop', array(
    //'default' => false,
    //'type'        => 'theme_mod',
    //'capability'  => 'edit_theme_options',
    //));
    //$wp_customize->add_control('dc_add_desc_loop', array(
    //'label' => __('Add description', 'divi_commerce_options'),
    //'section' => 'dc_loop_description',
    //'type' => 'checkbox',
    //'settings' => 'dc_add_desc_loop'
    //));
    /*header add to cart background*/
    //$wp_customize->add_setting('dc_loop_description_colour', array(
    //'transport' => 'refresh',
    //)) ;
    //$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_description_colour', array(
    //'label' => __('Add to Cart Background Colour'),
    //'section' => 'dc_loop_description',
    //'settings' => 'dc_loop_description_colour',
    //) 
    //));
/*loop add to cart*/
  $wp_customize->add_section('dc_loop_atc', array(    
    'title' => __('Product Loop | Add to cart' ),
    'panel' => 'divi_commerce_panel',
    ));
    /*enable add to cart*/
    $wp_customize->add_setting('dc_add_act1_loop', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_act1_loop', array(
    'label' => __('Add add to cart button', 'divi_commerce_options'),
    'section' => 'dc_loop_atc',
    'type' => 'checkbox',
    'settings' => 'dc_add_act1_loop'
    ));
    /*header add to cart background*/
    $wp_customize->add_setting('dc_loop_atc_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_bg_colour', array(
    'label' => __('background colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_border_colour', array(
    'label' => __('border colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_text_colour', array(
    'label' => __('text colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_loop_atc_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_font_size', array(
    'label' => __('Font Size'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_loop_atc_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_fw', array(
    'type' => 'select',
    'label' => 'font weight',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_loop_atc_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_tt', array(
    'type' => 'select',
    'label' => 'Text Transform',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_loop_atc_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_border_width', array(
    'label' => __('border width'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_bg_colour_h', array(
    'label' => __('Hover background colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_border_colour_h', array(
    'label' => __('Hover border colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_loop_atc_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_atc_text_colour_h', array(
    'label' => __('text Hover colour'),
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_loop_atc_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_font_size_h', array(
    'label' => __('Hover Font Size'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_loop_atc_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_fw_h', array(
    'type' => 'select',
    'label' => 'hover font weight',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_loop_atc_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_tt_h', array(
    'type' => 'select',
    'label' => 'Hover Text Transform',
    'section' => 'dc_loop_atc',
    'settings' => 'dc_loop_atc_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_loop_atc_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_atc_border_width_h', array(
    'label' => __('Hover border width'),
    'section' => 'dc_loop_atc',
    'type' => 'option',
    'settings' => 'dc_loop_atc_border_width_h'
    ));
/*Loop view details button*/
  $wp_customize->add_section('dc_loop_vdb', array(    
    'title' => __('Product Loop | details button' ),
    'panel' => 'divi_commerce_panel',
    ));
    /*Add view details button to loop*/
    $wp_customize->add_setting('dc_add_vdb_loop', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_add_vdb_loop', array(
    'label' => __('Add view detail button', 'divi_commerce_options'),
    'section' => 'dc_loop_vdb',
    'type' => 'checkbox',
    'settings' => 'dc_add_vdb_loop'
    ));
    $wp_customize->add_setting('dc_loop_vdb_bg_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_vdb_bg_colour', array(
    'label' => __('background colour'),
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_bg_colour',
    ) ) );
    /*ADD TO CART BORDER COLOUR*/
    $wp_customize->add_setting('dc_loop_vdb_border_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_vdb_border_colour', array(
    'label' => __('border colour'),
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_border_colour',
    ) ) );
    /*ADD TO CART TEXT COLOUR*/
    $wp_customize->add_setting('dc_loop_vdb_text_colour', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_vdb_text_colour', array(
    'label' => __('text colour'),
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_text_colour',
    ) ) );
    /*ADD TO CART FONT SIZE*/
    $wp_customize->add_setting('dc_loop_vdb_font_size', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_font_size', array(
    'label' => __('Font Size'),
    'section' => 'dc_loop_vdb',
    'type' => 'option',
    'settings' => 'dc_loop_vdb_font_size'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_loop_vdb_fw', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_fw', array(
    'type' => 'select',
    'label' => 'font weight',
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_fw',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_loop_vdb_tt', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_tt', array(
    'type' => 'select',
    'label' => 'Text Transform',
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_tt',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART BORDER WIDTH*/
    $wp_customize->add_setting('dc_loop_vdb_border_width', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_border_width', array(
    'label' => __('border width'),
    'section' => 'dc_loop_vdb',
    'type' => 'option',
    'settings' => 'dc_loop_vdb_border_width'
    ));
    /*ADD TO CART HOVER BACKGROUND COLOUR*/
    $wp_customize->add_setting('dc_loop_vdb_bg_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_vdb_bg_colour_h', array(
    'label' => __('Hover background colour'),
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_bg_colour_h',
    ) ) );
    /*ADD TO CART HOVER BORDER COLOUR*/
    $wp_customize->add_setting('dc_loop_vdb_border_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_vdb_border_colour_h', array(
    'label' => __('Hover border colour'),
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_border_colour_h',
    ) ) );
    /*ADD TO CART TEXT HOVER COLOUR*/
    $wp_customize->add_setting('dc_loop_vdb_text_colour_h', array(
    'transport' => 'refresh',
    )) ;
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_loop_vdb_text_colour_h', array(
    'label' => __('text Hover colour'),
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_text_colour_h',
    ) ) );
    /*ADD TO CART HOVER FONT SIZE*/
    $wp_customize->add_setting('dc_loop_vdb_font_size_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_font_size_h', array(
    'label' => __('Hover Font Size'),
    'section' => 'dc_loop_vdb',
    'type' => 'option',
    'settings' => 'dc_loop_vdb_font_size_h'
    ));
    /*ADD TO CART FONT WEIGHT*/
    $wp_customize->add_setting('dc_loop_vdb_fw_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_fw_h', array(
    'type' => 'select',
    'label' => 'hover font weight',
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_fw_h',
    'choices' => array(
    'bold' => 'bold',
    'normal' => 'normal',
    ),
    ));
    /*ADD TO CART HOVER TEXT TRANSFORM*/
    $wp_customize->add_setting('dc_loop_vdb_tt_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_tt_h', array(
    'type' => 'select',
    'label' => 'Hover Text Transform',
    'section' => 'dc_loop_vdb',
    'settings' => 'dc_loop_vdb_tt_h',
    'choices' => array(
    'uppercase' => 'Uppercase',
    'lowercase' => 'Lowercase',
    'capitalize' => 'Capitalize',
    ),
    ));
    /*ADD TO CART HOVER BORDER WIDTH*/
    $wp_customize->add_setting('dc_loop_vdb_border_width_h', array(
    'type' => 'theme_mod',
    'capability' => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_loop_vdb_border_width_h', array(
    'label' => __('Hover border width'),
    'section' => 'dc_header_atc',
    'type' => 'option',
    'settings' => 'dc_loop_vdb_border_width_h'
    ));
//Advanced
  $wp_customize->add_section('dc_advanced_options', array(
    'title' => __('checkout | Remove fields' ),
    'panel' => 'divi_commerce_panel',
    ));
    $wp_customize->add_setting('dc_remove_first_name', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_first_name', array(
    'label' => __('Remove First Name'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_first_name',
    ));
    $wp_customize->add_setting('dc_remove_last_name', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_last_name', array(
    'label' => __('Remove Last Name'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_last_name',
    ));
    $wp_customize->add_setting('dc_remove_company', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_company', array(
    'label' => __('Remove Company'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_company',
    ));
    $wp_customize->add_setting('dc_remove_address_1', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_address_1', array(
    'label' => __('Remove Address'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_address_1',
    ));
    $wp_customize->add_setting('dc_remove_address_2', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_address_2', array(
    'label' => __('Remove Address'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_address_2',
    ));
    $wp_customize->add_setting('dc_remove_city', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_city', array(
    'label' => __('Remove City'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_city',
    ));
    $wp_customize->add_setting('dc_remove_post_code', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_post_code', array(
    'label' => __('Remove Postcode'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_post_code',
    ));
    $wp_customize->add_setting('dc_remove_country', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_country', array(
    'label' => __('Remove Country'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_country',
    ));
    $wp_customize->add_setting('dc_remove_state', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_state', array(
    'label' => __('Remove State'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_state',
    ));
    $wp_customize->add_setting('dc_remove_phone', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_phone', array(
    'label' => __('Remove Phone'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_phone',
    ));
    $wp_customize->add_setting('dc_remove_email', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_email', array(
    'label' => __('Remove email'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_email',
     ));
    $wp_customize->add_setting('dc_remove_order_comments', array(
    'default' => false,
    'type'        => 'theme_mod',
    'capability'  => 'edit_theme_options',
    ));
    $wp_customize->add_control('dc_remove_order_comments', array(
    'label' => __('Remove Notes'),
    'section' => 'dc_advanced_options',
    'type' => 'checkbox',
    'settings' => 'dc_remove_order_comments',
    ));
/**/
//My account page
$wp_customize->add_section('dc_myaccount_options', array(
'title' => __('My Account options' ),
'panel' => 'divi_commerce_panel',
));
$wp_customize->add_setting('dc_account_nav_border', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_nav_border', array(
'label' => __('Navigation Border colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_nav_border',
) ) );
$wp_customize->add_setting('dc_account_nav_link_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_nav_link_colour', array(
'label' => __('Navigation links'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_nav_link_colour',
) ) );
$wp_customize->add_setting('dc_account_nav_font_size', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_nav_font_size', array(
'label' => __('Navigation links font size'),
'section' => 'dc_myaccount_options',
'type' => 'option',
'settings' => 'dc_account_nav_font_size'
));
$wp_customize->add_setting('dc_account_nav_fw', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_nav_fw', array(
'type' => 'select',
'label' => 'Navigation links font weight',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_nav_fw',
'choices' => array(
'bold' => 'bold',
'bolder' => 'bolder',
'normal' => 'normal',
),
));
$wp_customize->add_setting('dc_account_nav_tt', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_nav_tt', array(
'type' => 'select',
'label' => 'account nav text transform',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_nav_tt',
'choices' => array(
'uppercase' => 'Uppercase',
'lowercase' => 'Lowercase',
'capitalize' => 'Capitalize',
),
));
$wp_customize->add_setting('dc_account_nav_active_hover', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_nav_active_hover', array(
'label' => __('Navigation hover and active colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_nav_active_hover',
) ) );
$wp_customize->add_setting('dc_account_content_background', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_content_background', array(
'label' => __('Tab content background'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_content_background',
) ) );
$wp_customize->add_setting('dc_account_content_text_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_content_text_colour', array(
'label' => __('Tab content text colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_content_text_colour',
) ) );
$wp_customize->add_setting('dc_account_content_link_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_content_link_colour', array(
'label' => __('Tab content link colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_content_link_colour',
) ) );
$wp_customize->add_setting('dc_account_content_link_colour_hover', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_content_link_colour_hover', array(
'label' => __('Tab content link hover colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_content_link_colour_hover',
) ) );
$wp_customize->add_setting('dc_account_content_area_padding', array(
'type' => 'theme_mod',
'default' => '20px',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_content_area_padding', array(
'label' => __('Tabs content padding'),
'section' => 'dc_myaccount_options',
'type' => 'option',
'settings' => 'dc_account_content_area_padding'
));
$wp_customize->add_setting('dc_account_content_border_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_content_border_colour', array(
'label' => __('Tab Content border colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_content_border_colour',
) ) );
$wp_customize->add_setting('dc_account_table_header_background', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_table_header_background', array(
'label' => __('table header background colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_table_header_background',
) ) );
$wp_customize->add_setting('dc_account_header_text_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_header_text_colour', array(
'label' => __('table header text colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_header_text_colour',
) ) );

$wp_customize->add_setting('dc_account_table_header_tt', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_table_header_tt', array(
'type' => 'select',
'label' => 'Table header text transform',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_table_header_tt',
'choices' => array(
'uppercase' => 'Uppercase',
'lowercase' => 'Lowercase',
'capitalize' => 'Capitalize',
),
));
$wp_customize->add_setting('dc_account_row_even_background', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_row_even_background', array(
'label' => __('Row even background colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_even_background',
) ) );
$wp_customize->add_setting('dc_account_row_even_text_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_row_even_text_colour', array(
'label' => __('Row even text colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_even_text_colour',
) ) );
$wp_customize->add_setting('dc_account_row_even_link_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_row_even_link_colour', array(
'label' => __('Row even link colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_even_link_colour',
) ) );
$wp_customize->add_setting('dc_row_even_link_hover_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_row_even_link_hover_colour', array(
'label' => __('Row even link hover colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_row_even_link_hover_colour',
) ) );
$wp_customize->add_setting('dc_account_row_even_font_size', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_row_even_font_size', array(
'label' => __('row even font size'),
'section' => 'dc_myaccount_options',
'type' => 'option',
'settings' => 'dc_account_row_even_font_size'
));
$wp_customize->add_setting('dc_account_row_even_fw', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_row_even_fw', array(
'type' => 'select',
'label' => 'Row even font weight',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_even_fw',
'choices' => array(
'bold' => 'bold',
'bolder' => 'bolder',
'normal' => 'normal',
),
));
$wp_customize->add_setting('dc_account_row_even_tt', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_row_even_tt', array(
'type' => 'select',
'label' => 'row even text transform',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_even_tt',
'choices' => array(
'uppercase' => 'Uppercase',
'lowercase' => 'Lowercase',
'capitalize' => 'Capitalize',
),
));
$wp_customize->add_setting('dc_account_row_odd_background', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_row_odd_background', array(
'label' => __('Row odd background colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_odd_background',
) ) );
$wp_customize->add_setting('dc_account_row_odd_text_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_row_odd_text_colour', array(
'label' => __('Row odd text colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_odd_text_colour',
) ) );
$wp_customize->add_setting('dc_account_row_odd_link_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_row_odd_link_colour', array(
'label' => __('Row odd link colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_odd_link_colour',
) ) );
$wp_customize->add_setting('dc_row_odd_link_hover_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_row_odd_link_hover_colour', array(
'label' => __('Row odd link hover colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_row_odd_link_hover_colour',
) ) );
$wp_customize->add_setting('dc_account_row_odd_font_size', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_row_odd_font_size', array(
'label' => __('row odd font size'),
'section' => 'dc_myaccount_options',
'type' => 'option',
'settings' => 'dc_account_row_odd_font_size'
));
$wp_customize->add_setting('dc_account_row_odd_fw', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_row_odd_fw', array(
'type' => 'select',
'label' => 'Row odd font weight',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_odd_fw',
'choices' => array(
'bold' => 'bold',
'bolder' => 'bolder',
'normal' => 'normal',
),
));
$wp_customize->add_setting('dc_account_row_odd_tt', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_account_row_odd_tt', array(
'type' => 'select',
'label' => 'row odd text transform',
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_row_odd_tt',
'choices' => array(
'uppercase' => 'Uppercase',
'lowercase' => 'Lowercase',
'capitalize' => 'Capitalize',
),
));
$wp_customize->add_setting('dc_account_mark_background', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'background', array(
'label' => __('Highlited text background'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_mark_background',
) ) );
$wp_customize->add_setting('dc_account_mark_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_account_mark_colour', array(
'label' => __('Highlited text colour'),
'section' => 'dc_myaccount_options',
'settings' => 'dc_account_mark_colour',
) ) );
//Login & Register
//Login & Register
$wp_customize->add_section('dc_login_reg', array(
'title' => __('Login & Register options' ),
'panel' => 'divi_commerce_panel',
));
$wp_customize->add_setting('dc_login_bg', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_bg', array(
'label' => __('Login and register form background'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_bg',
) ) );
$wp_customize->add_setting('dc_login_text_color', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_text_color', array(
'label' => __('text colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_text_color',
) ) );
$wp_customize->add_setting('dc_login_border_radius', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_border_radius', array(
'label' => __('Login and register form border radius'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_border_radius'
));
$wp_customize->add_setting('dc_login_border_top_color', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_border_top_color', array(
'label' => __('Border top colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_border_top_color',
) ) );
$wp_customize->add_setting('dc_login_border_top_width', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_border_top_width', array(
'label' => __('Border Top Width'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_border_top_width'
));
$wp_customize->add_setting('dc_login_border_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_border_colour', array(
'label' => __('border colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_border_colour',
) ) );
$wp_customize->add_setting('dc_login_border_width', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_border_width', array(
'label' => __('Border Width'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_border_width'
));
$wp_customize->add_setting('dc_login_padding', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_padding', array(
'label' => __('Login and register form padding'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_padding'
));
$wp_customize->add_setting('dc_login_button_width', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_width', array(
'label' => __('Button width'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_button_width'
));
$wp_customize->add_setting('dc_login_button_bg_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_button_bg_colour', array(
'label' => __('button background colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_bg_colour',
) ) );
$wp_customize->add_setting('dc_login_button_border_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_button_border_colour', array(
'label' => __('button border colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_border_colour',
) ) );
$wp_customize->add_setting('dc_login_button_text_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_button_text_colour', array(
'label' => __('button text colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_text_colour',
) ) );
$wp_customize->add_setting('dc_login_button_font_size', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_font_size', array(
'label' => __('button Font Size'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_button_font_size'
));
$wp_customize->add_setting('dc_login_button_fw', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_fw', array(
'type' => 'select',
'label' => 'button font weight',
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_fw',
'choices' => array(
'bold' => 'bold',
'bolder' => 'bolder',
'normal' => 'normal',
),
));
$wp_customize->add_setting('dc_login_button_tt', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_tt', array(
'type' => 'select',
'label' => 'button Text Transform',
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_tt',
'choices' => array(
'uppercase' => 'Uppercase',
'lowercase' => 'Lowercase',
'capitalize' => 'Capitalize',
),
));
$wp_customize->add_setting('dc_login_button_border_width', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_border_width', array(
'label' => __('button border width'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_button_border_width'
));
$wp_customize->add_setting('dc_login_button_bg_colour_h', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_button_bg_colour_h', array(
'label' => __('button Hover background colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_bg_colour_h',
) ) );
$wp_customize->add_setting('dc_login_button_border_colour_h', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_button_border_colour_h', array(
'label' => __('button Hover border colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_border_colour_h',
) ) );
$wp_customize->add_setting('dc_login_button_text_colour_h', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_button_text_colour_h', array(
'label' => __('button text Hover colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_text_colour_h',
) ) );
$wp_customize->add_setting('dc_login_button_font_size_h', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_font_size_h', array(
'label' => __('button Hover Font Size'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_button_font_size_h'
));
$wp_customize->add_setting('dc_login_button_fw_h', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_fw_h', array(
'type' => 'select',
'label' => 'button hover font weight',
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_fw_h',
'choices' => array(
'bold' => 'bold',
'bolder' => 'bolder',
'normal' => 'normal',
),
));
$wp_customize->add_setting('dc_login_button_tt_h', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_tt_h', array(
'type' => 'select',
'label' => 'button Hover Text Transform',
'section' => 'dc_login_reg',
'settings' => 'dc_login_button_tt_h',
'choices' => array(
'uppercase' => 'Uppercase',
'lowercase' => 'Lowercase',
'capitalize' => 'Capitalize',
),
));
$wp_customize->add_setting('dc_login_button_border_width_h', array(
'type' => 'theme_mod',
'capability' => 'edit_theme_options',
));
$wp_customize->add_control('dc_login_button_border_width_h', array(
'label' => __('button Hover border width'),
'section' => 'dc_login_reg',
'type' => 'option',
'settings' => 'dc_login_button_border_width_h'
));
$wp_customize->add_setting('dc_login_forgot_colour', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_forgot_colour', array(
'label' => __('forgot password link colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_forgot_colour',
) ) );
$wp_customize->add_setting('dc_login_forgot_colour_h', array(
'transport' => 'refresh',
)) ;
$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'dc_login_forgot_colour_h', array(
'label' => __('forgot password link hover colour'),
'section' => 'dc_login_reg',
'settings' => 'dc_login_forgot_colour_h',
) ) );
$wp_customize->add_section('ds_dc_settings', array(
'title' => __('Settings' ),
'panel' => 'divi_commerce_panel',
));
$wp_customize->add_setting('dc_remove_dc_builder', array(
'default' => false,
'type'        => 'theme_mod',
'capability'  => 'edit_theme_options',
));
$wp_customize->add_control('dc_remove_dc_builder', array(
'label' => __('Remove page builder from Divi Commerce'),
'section' => 'ds_dc_settings',
'type' => 'checkbox',
'settings' => 'dc_remove_dc_builder'
));
$wp_customize->add_setting('dc_no_template', array(
'default' => false,
'type'        => 'theme_mod',
'capability'  => 'edit_theme_options',
));
$wp_customize->add_control('dc_no_template', array(
'label' => __('Do not use custom product template'),
'section' => 'ds_dc_settings',
'type' => 'checkbox',
'settings' => 'dc_no_template'
));
}
add_action('customize_register', 'ds_dc_woo_customizer');
/*Customizer CSS*/
function dc_customize_css(){ 
?>
<style type="text/css">
/*Product basics CSS Section start here*/
/*SINGLE PRODUCT PAGE BACKGROUND COLOUR*/
.single-product #main-content{
background:<?php echo get_theme_mod('dc_base_page_background'); ?>!important;
}
/*BREADCRUMB TEXT COLOUR*/
nav.woocommerce-breadcrumb, nav.woocommerce-breadcrumb a{
  color:<?php echo get_theme_mod('dc_base_breadcrumbs_colour');?>!important;
}
/*SUMMARY CONTAINER BACKGROUND*/
/*SUMMARY CONTAINER BORDER WIDTH*/
/*INFO BORDER COLOUR*/
/*INFO CONTAINER PADDING*/
.summary.entry-summary{
background:<?php echo get_theme_mod('dc__base_info_con'); ?>!important;
border-style:solid;
border-width: <?php echo get_theme_mod('dc_base_info_border_width'); ?>!important;
border-color:<?php echo get_theme_mod('dc__base_info_border_colour'); ?>!important;
padding:<?php echo get_theme_mod('dc_base_info_padding'); ?>!important;
}
/*IMAGE BORDER COLOUR*/
/*MAIN IMAGE BORDER WIDTH*/
.single-product .images img {
border-style: solid!important;
border-color: <?php echo get_theme_mod('dc__base_img_border_colour'); ?>!important;
border-width: <?php echo get_theme_mod('dc_base_main_img_border_width'); ?>!important;
}
/*TITLE FONT SIZE*/
/*TITLE COLOUR*/
/*TITLE FONT WEIGHT*/
/*TITLE TEXT TRANSFORM*/
.product_title { 
font-size:<?php echo get_theme_mod('dc_base_title_font_size'); ?>!important;
color:<?php echo get_theme_mod('dc__base_title_colour'); ?>!important;
font-weight:<?php echo get_theme_mod('dc_base_title_fw'); ?>!important;
text-transform:<?php echo get_theme_mod('dc_base_title_tt'); ?>!important;
}
/*PRICE COLOUR*/
/*PRICE FONT SIZE*/
/*PRICE FONT WEIGHT*/
.entry-summary p.price span { 
color:<?php echo get_theme_mod('dc__base_price_colour'); ?>!important;
font-size:<?php echo get_theme_mod('dc_base_price_font_size'); ?>!important;
font-weight:<?php echo get_theme_mod('dc_price_fw'); ?>!important;
}
/*SHORT DESCRIPTION TEXT FONT SIZE*/
/*SHORT DESCRIPTION TEXT COLOUR*/
/*SHORT DESCRIPTION FONT WEIGHT*/
.entry-summary p:not(.price) { 
color:<?php echo get_theme_mod('dc_main_text_colour'); ?>!important;
font-size:<?php echo get_theme_mod('dc_main_text_font_size'); ?>!important;
font-weight:<?php echo get_theme_mod('dc_main_text_fw'); ?>!important;
}
/*QUANTITY BACKGROUND COLOUR*/
/*QUANTITY TEXT COLOURe*/
.woocommerce div.product form.cart div.quantity { 
background:<?php echo get_theme_mod('dc_quantity_bg_colour'); ?>!important;
}
.woocommerce div.product form.cart div.quantity .input-text.qty.text{
color:<?php echo get_theme_mod('dc_quantity_text_colour'); ?>!important;
}
/*ADD TO CART BACKGROUND COLOUR*/
/*ADD TO CART BORDER COLOUR*/
/*ADD TO CART TEXT COLOUR*/
/*ADD TO CART FONT SIZE*/
/*ADD TO CART FONT WEIGHT*/
/*ADD TO CART TEXT TRANSFORM*/
/*ADD TO CART BORDER WIDTH*/
.woocommerce .product .cart .button { 
background:<?php echo get_theme_mod('dc_atc_bg_colour'); ?>!important;
color:<?php echo get_theme_mod('dc_atc_text_colour'); ?>!important;
font-size:<?php echo get_theme_mod('dc_atc_font_size'); ?>!important;
border-width:<?php echo get_theme_mod('dc_act_border_width'); ?>!important;
border-color:<?php echo get_theme_mod('dc_atc_border_colour'); ?>!important;
border-style:solid;
font-weight:<?php echo get_theme_mod('dc_act_fw'); ?>!important;
text-transform:<?php echo get_theme_mod('dc_act_tt'); ?>!important;
}
/*ADD TO CART HOVER BACKGROUND COLOUR*/
/*ADD TO CART HOVER BORDER COLOUR*/
/*ADD TO CART TEXT HOVER COLOUR*/
/*ADD TO CART HOVER FONT SIZE*/
/*ADD TO CART FONT WEIGHT*/
/*ADD TO CART HOVER TEXT TRANSFORM*/
/*ADD TO CART HOVER BORDER WIDTH*/
.woocommerce .product .cart .button:hover { 
background:<?php echo get_theme_mod('dc_atc_bg_colour_h'); ?>!important;
color:<?php echo get_theme_mod('dc_atc_text_colour_h'); ?>!important;
font-size:<?php echo get_theme_mod('dc_atc_font_size_h'); ?>!important;
border-width:<?php echo get_theme_mod('dc_act_border_width_h'); ?>!important;
border-color:<?php echo get_theme_mod('dc_atc_border_colour_h'); ?>!important;
border-style:solid;
font-weight:<?php echo get_theme_mod('dc_act_fw_h'); ?>!important;
text-transform:<?php echo get_theme_mod('dc_act_tt_h'); ?>!important;
}
/*META FONT SIZE*/
/*META COLOUR*/
/*META FONT WEIGHT*/
/*META text transform*/
.product_meta { 
font-size:<?php echo get_theme_mod('dc_base_meta_font_size'); ?>!important;
color:<?php echo get_theme_mod('dc__base_meta_colour'); ?>!important;
font-weight:<?php echo get_theme_mod('dc_base_meta_fw'); ?>!important;
text-transform:<?php echo get_theme_mod('dc_base_meta_tt'); ?>!important;
}
/*META LINK COLOUR*/
/*META FONT WEIGHT*/
.product_meta a {
color:<?php echo get_theme_mod('dc__base_metalink_colour'); ?>!important;
font-weight:<?php echo get_theme_mod('dc_base_metalink_fw'); ?>!important;
}
/*META LINK HOVER COLOUR*/
.product_meta a:hover {
color:<?php echo get_theme_mod('dc__base_metalink_colour_h'); ?>!important;
}
/*Single Product Tab Area Customizer*/

/*SELECT TABS CONTENT AREA BACKGROUND*/
/*TAB AREA MARGIN TOP*/
/*TAB AREA MARGIN bottom*/
.woocommerce-tabs.wc-tabs-wrapper{
background:<?php echo get_theme_mod('dc_tabs_main_bg'); ?>!important;
margin-top:<?php echo get_theme_mod('dc_tabs_margin_top'); ?>!important;
margin-bottom:<?php echo get_theme_mod('dc_tabs_margin_bottom'); ?>!important;
border-color:<?php echo get_theme_mod('dc_tabs_border_colour'); ?>!important;}

/*SELECT TABS BACKGROUND COLOUR*/
body.woocommerce div.product .woocommerce-tabs ul.tabs li a, body.woocommerce #content-area div.product .woocommerce-tabs ul.tabs li a {
background:<?php echo get_theme_mod('dc_tab_n_bg_color'); ?>!important;
color:<?php echo get_theme_mod('dc_tab_n_text_color'); ?>!important;
}

body.woocommerce div.product .woocommerce-tabs ul.tabs li.active a, body.woocommerce #content-area div.product .woocommerce-tabs ul.tabs li.active a {
background:<?php echo get_theme_mod('dc_tab_a_bg_color'); ?>!important;
color:<?php echo get_theme_mod('dc_tab_a_text_color'); ?>!important;
}

ul.tabs.wc-tabs{
background:<?php echo get_theme_mod('dc_tabs_bg'); ?>!important;
text-align:<?php echo get_theme_mod('dc_tabs_position'); ?>!important;
}

.tabs.wc-tabs a {
text-transform:<?php echo get_theme_mod('dc_tab_title_tt'); ?>!important;
font-weight:<?php echo get_theme_mod('dc_tab_title_fw'); ?>!important;
}
/*SELECT TABS AREA BORDER COLOUR*/

/*TABS POSITION*/

/*TABS BACKGROUND COLOUR*/

/*TABS ACTIVE BACKGROUND COLOUR*/

/*TITLE COLOUR*/

/*TITLE ACTIVE COLOUR*/

/*TITLE TEXT TRANSFORM*/

/*TITLE FONT WEIGHT*/
.single-product ul.tabs.wc-tabs{
<?php if( false != get_theme_mod( 'dc_hide_tabs_bar' ) ) { ?>
    display: none;
<?php } ?>
}
.single-product.et_pb_pagebuilder_layout #tab-description h2{
<?php if( false != get_theme_mod( 'dc_tab_main_header' ) ) { ?>
    padding:0px;
    display:none;
<?php } ?>
}
.single-product.et_pb_pagebuilder_layout #tab-description{
<?php if( false != get_theme_mod( 'dc_tab_main_header_padding' ) ) { ?>
    padding:0px!important;
<?php } ?>
}

div.woocommerce-Tabs-panel.woocommerce-Tabs-panel--description.panel.entry-content.wc-tab{}
/*Remove single product sections*/
.single-product nav.woocommerce-breadcrumb {
  <?php if( false != get_theme_mod( 'dc_hide_pro_bread' ) ) { ?>
    display: none;
<?php } ?>
}
.single-product .woocommerce-product-gallery {
  <?php if( false != get_theme_mod( 'dc_hide_default_img' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product span.onsale {
  <?php if( false != get_theme_mod( 'dc_hide_default_img' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product h1.product_title.entry-title {
  <?php if( false != get_theme_mod( 'dc_hide_pro_title' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product p.price {
  <?php if( false != get_theme_mod( 'dc_hide_pro_price' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product div.woocommerce-product-details__short-description p {
  <?php if( false != get_theme_mod( 'dc_hide_pro_info' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product input.input-text.qty.text {
  <?php if( false != get_theme_mod( 'dc_hide_pro_qty' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product button.single_add_to_cart_button.button.alt {
  <?php if( false != get_theme_mod( 'dc_hide_pro_atc_but' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product div.product_meta {
  <?php if( false != get_theme_mod( 'dc_hide_pro_meta' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product div.summary.entry-summary {
  <?php if( false != get_theme_mod( 'dc_hide_whole_summary' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product .woocommerce-tabs.wc-tabs-wrapper {
  <?php if( false != get_theme_mod( 'dc_hide_pro_tab' ) ) { ?>
    display: none;
    <?php } ?>
}
.single-product .related.products{
  <?php if( false != get_theme_mod( 'dc_hide_related_pro' ) ) { ?>
    display:none;
    <?php } ?>
}
/*Header ATC*/
#header-atc button.single_add_to_cart_button {
    font-size: <?php echo get_theme_mod('dc_add_header_font_size'); ?>!important;
    margin-top: -12px;
    background: <?php echo get_theme_mod('dc_add_header_bg_colour'); ?>;
    border-color: <?php echo get_theme_mod('dc_add_header_border_colour'); ?>;
    border-width: <?php echo get_theme_mod('dc_add_header_border_width'); ?>;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_add_header_text_colour'); ?>;
    font-weight:<?php echo get_theme_mod('dc_add_header_fw'); ?>;
    text-transform: <?php echo get_theme_mod('dc_add_header_act_tt'); ?>;
}

#header-atc button.single_add_to_cart_button:hover {
    font-size: <?php echo get_theme_mod('dc_add_header_font_size_h'); ?>!important;
    background: <?php echo get_theme_mod('dc_add_header_bg_colour_h'); ?>;
    border-color: <?php echo get_theme_mod('dc_add_header_border_colour_h'); ?>;
    border-width: <?php echo get_theme_mod('dc_add_header_border_width_h'); ?>;
    color: <?php echo get_theme_mod('dc_add_header_text_colour_h'); ?>;
    font-weight:<?php echo get_theme_mod('dc_add_header_fw_h'); ?>;
    text-transform: <?php echo get_theme_mod('dc_add_header_tt_h'); ?>;
}
/*Header mini cart hover*/
.et-cart-info span:before{
    color: <?php echo get_theme_mod('dc_header_cart_icon_colour'); ?>;
}
#cartcontents {
background: <?php echo get_theme_mod('dc_header_mini_cart_background'); ?>;
} 
#cartcontents li.woocommerce-mini-cart-item.mini_cart_item {
    border-bottom: 1px solid <?php echo get_theme_mod('dc_header_mini_cart_seperator'); ?>;
}

#cartcontents a.remove {
    color: <?php echo get_theme_mod('dc_header_mini_cart_remove_icon_colour'); ?>!important;
}
#cartcontents a.remove:hover {
    color: <?php echo get_theme_mod('dc_header_mini_cart_remove_icon_colour'); ?>!important;
    background: <?php echo get_theme_mod('dc_header_mini_cart_remove_icon_bg'); ?>!important;
}
#cartcontents .quantity, #cartcontents .woocommerce-mini-cart__total.total{
    color: <?php echo get_theme_mod('dc_header_mini_cart_text_colour'); ?>!important;
    }
#cartcontents li a, #cartcontents{
    color: <?php echo get_theme_mod('dc_header_mini_cart_heading_colour'); ?>!important;
    }
  /*view cart button*/
  #cartcontents a.button.wc-forward{
    font-size: <?php echo get_theme_mod('dc_header_cart_but_1_font_size'); ?>!important;
    background: <?php echo get_theme_mod('dc_header_cart_but_1_bg_colour'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_header_cart_but_1_border_colour'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_header_cart_but_1_border_width'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_header_cart_but_1_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_header_cart_but_1_fw'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_header_cart_but_1_tt'); ?>!important;
  }
  #cartcontents a.button.wc-forward:hover{
    font-size: <?php echo get_theme_mod('dc_header_cart_but_1_font_size_h'); ?>!important;
    background: <?php echo get_theme_mod('dc_header_cart_but_1_bg_colour_h'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_header_cart_but_1_border_colour_h'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_header_cart_but_1_border_width_h'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_header_cart_but_1_text_colour_h'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_header_cart_but_1_fw_h'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_header_cart_but_1_tt_h'); ?>!important;
  }
  /*checkout button*/
  #cartcontents a.button.checkout.wc-forward{
    font-size: <?php echo get_theme_mod('dc_header_cart_but_2_font_size'); ?>!important;
    background: <?php echo get_theme_mod('dc_header_cart_but_2_bg_colour'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_header_cart_but_2_border_colour'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_header_cart_but_2_border_width'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_header_cart_but_2_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_header_cart_but_2_fw'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_header_cart_but_2_tt'); ?>!important;
  }
  #cartcontents a.button.checkout.wc-forward:hover{
    font-size: <?php echo get_theme_mod('dc_header_cart_but_2_font_size_h'); ?>!important;
    background: <?php echo get_theme_mod('dc_header_cart_but_2_bg_colour_h'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_header_cart_but_2_border_colour_h'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_header_cart_but_2_border_width_h'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_header_cart_but_2_text_colour_h'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_header_cart_but_2_fw_h'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_header_cart_but_2_tt_h'); ?>!important;
  }

/*related products*/
.single-product .woocommerce .related.products ul.products li.product a.button.addtocartbutton {
    margin-left: 10px;
    margin-right: 10px;
    width: 90%!important;
}

.single-product .woocommerce .related.products ul.products li.product a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart {
    margin-bottom: 0;
    margin-left: 10px;
    margin-right: 10px;
    width: 90%!important;
}

.related.products h2.woocommerce-loop-product__title {
    font-size: <?php echo get_theme_mod('dc_related_products_title_font_size'); ?>!important;
    color:<?php echo get_theme_mod('dc_related_products_title_color'); ?>!important;
    text-transform:<?php echo get_theme_mod('dc_related_products_title_tt'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_related_products_title_fw'); ?>!important;
    padding-left:10px!important;
}

.related.products li.product {
    background-color: <?php echo get_theme_mod('dc_related_products_bg'); ?>!important;
    padding-bottom:10px!important;
}

.woocommerce .related.products ul.products li.product span.price del {
    font-size: <?php echo get_theme_mod('dc_related_products_price_font_size'); ?>!important;
    color:<?php echo get_theme_mod('dc_related_products_price_color'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_related_products_price_fw'); ?>!important;
    width: auto;
    float: left;
    padding-right:5px;
}

.woocommerce .related.products ul.products li.product span.price ins {
    font-size: <?php echo get_theme_mod('dc_related_products_price_font_size'); ?>!important;
    color:<?php echo get_theme_mod('dc_related_products_price_color'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_related_products_price_fw'); ?>!important;
    text-align: center;
    width: auto;
    float: left;
    padding-left:10px;
}
/*default products*/
li.product h2.woocommerce-loop-product__title {
    font-size: <?php echo get_theme_mod('dc_loop_basic_title_font_size'); ?>!important;
    color:<?php echo get_theme_mod('dc_loop_basic_title_color'); ?>!important;
    text-transform:<?php echo get_theme_mod('dc_loop_basic_title_tt'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_basic_title_fw'); ?>!important;
    padding-left:10px!important;
}

li.product {
    background-color: <?php echo get_theme_mod('dc_loop_basic_bg'); ?>!important;
    padding-bottom:10px!important;
} 
.woocommerce ul.products li.product .price{
  width:100%!important;
  float: left;
    padding-left:10px;
}
.woocommerce ul.products li.product .price del {
    font-size: <?php echo get_theme_mod('dc_loop_basic_price_font_size'); ?>!important;
    color:<?php echo get_theme_mod('dc_loop_basic_price_color'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_basic_price_fw'); ?>!important;
    width: auto;
    float: left;
    padding-right:5px;
}

.woocommerce ul.products li.product .price ins, .woocommerce ul.products li.product span.woocommerce-Price-amount.amount, .woocommerce ul.products li.product span.price {
    font-size: <?php echo get_theme_mod('dc_loop_basic_price_font_size'); ?>!important;
    color:<?php echo get_theme_mod('dc_loop_basic_price_color'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_basic_price_fw'); ?>!important;
    text-align: center;
    width: auto;
}
/*Loop buttons*/
a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart {
  <?php if( false != get_theme_mod( 'dc_loop_button_full' ) ) { ?>
    float: left;
    min-width:100%;
    max-width:100%;
    width:100%;
  <?php } ?>
}
a.button.product_type_variable {
  <?php if( false != get_theme_mod( 'dc_loop_button_full' ) ) { ?>
    float: left;
    min-width:100%;
    max-width:100%;
    width:100%;
  <?php } ?>
}
a.button.addtocartbutton {
  <?php if( false != get_theme_mod( 'dc_loop_button_full' ) ) { ?>
    min-width:100%;
    max-width:100%;
    width:100%;
  <?php } ?>
}

a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart {
  <?php if( true != get_theme_mod( 'dc_loop_button_full' ) ) { ?>
    float: left;
    min-width:45%;
    max-width:45%;
    width:45%; 
    float:left;
    margin-left:10px;
  <?php } ?> 
}

a.button.addtocartbutton {
  <?php if( true != get_theme_mod( 'dc_loop_button_full' ) ) { ?>
    min-width:45%;
    max-width:45%;
    width:45%;
    float:right;
    margin-right:10px;
  <?php } ?>
}
a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart {
font-size: <?php echo get_theme_mod('dc_loop_atc_font_size'); ?>!important;
    background: <?php echo get_theme_mod('dc_loop_atc_bg_colour'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_loop_atc_border_colour'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_loop_atc_border_width'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_loop_atc_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_atc_fw'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_loop_atc_tt'); ?>!important;
}
a.button.product_type_variable {
font-size: <?php echo get_theme_mod('dc_loop_atc_font_size'); ?>!important;
    background: <?php echo get_theme_mod('dc_loop_atc_bg_colour'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_loop_atc_border_colour'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_loop_atc_border_width'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_loop_atc_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_atc_fw'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_loop_atc_tt'); ?>!important;
}
a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart:hover, , ul.product li.product-type-variable a.button.product_type_variable.add_to_cart_button:hover {
font-size: <?php echo get_theme_mod('dc_loop_atc_font_size_h'); ?>!important;
    background: <?php echo get_theme_mod('dc_loop_atc_bg_colour_h'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_loop_atc_border_colour_h'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_loop_atc_width_h'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_loop_atc_text_colour_h'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_atc_fw_h'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_loop_atc_tt_h'); ?>!important;
}

a.button.addtocartbutton {
font-size: <?php echo get_theme_mod('dc_loop_vdb_font_size'); ?>!important;
    background: <?php echo get_theme_mod('dc_loop_vdb_bg_colour'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_loop_vdb_border_colour'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_loop_vdb_border_width'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_loop_vdb_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_vdb_fw'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_loop_vdb_tt'); ?>!important;
}
a.button.addtocartbutton:hover {
font-size: <?php echo get_theme_mod('dc_loop_vdb_font_size_h'); ?>!important;
    background: <?php echo get_theme_mod('dc_loop_vdb_bg_colour_h'); ?>!important;
    border-color: <?php echo get_theme_mod('dc_loop_vdb_border_colour_h'); ?>!important;
    border-width: <?php echo get_theme_mod('dc_loop_vdb_border_width_h'); ?>!important;
    border-style: solid;
    color: <?php echo get_theme_mod('dc_loop_vdb_text_colour_h'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_loop_vdb_fw_h'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_loop_vdb_tt_h'); ?>!important;
}
a.button.addtocartbutton:hover:after{
  font-size: calc(<?php echo get_theme_mod('dc_loop_vdb_font_size_h'); ?> + 3px)!important;
  margin-top: calc(<?php echo get_theme_mod('dc_loop_vdb_font_size_h'); ?> - 10px);
}
a.button.product_type_simple.add_to_cart_button.ajax_add_to_cart:hover:after{
  font-size: calc(<?php echo get_theme_mod('dc_loop_atc_font_size_h'); ?> + 3px)!important;
  margin-top: calc(<?php echo get_theme_mod('dc_loop_atc_font_size_h'); ?> - 10px);
}
/*Forms*/
.woocommerce-checkout input.input-text {
background:<?php echo get_theme_mod('dc_woo_forms_bg'); ?>!important;
border:<?php echo get_theme_mod('dc_woo_forms_border_width'); ?> <?php echo get_theme_mod('dc_woo_forms_border_colour'); ?> <?php echo get_theme_mod('dc_woo_forms_border_style'); ?>!important;
border-radius:<?php echo get_theme_mod('dc_tabs_main_bg'); ?>!important;
padding-top:<?php echo get_theme_mod('dc_woo_forms_bandt_padding'); ?>!important;
padding-bottom:<?php echo get_theme_mod('dc_woo_forms_bandt_padding'); ?>!important;
padding-left:<?php echo get_theme_mod('dc_woo_forms_;andr_padding'); ?>!important;
padding-right:<?php echo get_theme_mod('dc_woo_forms_landr_padding'); ?>!important;
border-radius:<?php echo get_theme_mod('dc_woo_forms_border_radius'); ?>!important;
color:<?php echo get_theme_mod('dc_woo_form_colour'); ?>!important;
}
.woocommerce-checkout a.select2-choice {
background: <?php echo get_theme_mod('dc_woo_forms_bg'); ?>!important;
border:<?php echo get_theme_mod('dc_woo_forms_border_width'); ?> <?php echo get_theme_mod('dc_woo_forms_border_colour'); ?> <?php echo get_theme_mod('dc_woo_forms_border_style'); ?>!important;
padding-top:<?php echo get_theme_mod('dc_woo_forms_bandt_padding'); ?>!important;
padding-bottom:<?php echo get_theme_mod('dc_woo_forms_bandt_padding'); ?>!important;
padding-left:<?php echo get_theme_mod('dc_woo_forms_;andr_padding'); ?>!important;
padding-right:<?php echo get_theme_mod('dc_woo_forms_landr_padding'); ?>!important;
border-radius:<?php echo get_theme_mod('dc_woo_forms_border_radius'); ?>!important;
color:<?php echo get_theme_mod('dc_woo_form_colour'); ?>!important;
}
.woocommerce-checkout textarea {
background:<?php echo get_theme_mod('dc_woo_forms_bg'); ?>!important;
border:<?php echo get_theme_mod('dc_woo_forms_border_width'); ?> <?php echo get_theme_mod('dc_woo_forms_border_colour'); ?> <?php echo get_theme_mod('dc_woo_forms_border_style'); ?>!important;
padding-top:<?php echo get_theme_mod('dc_woo_forms_bandt_padding'); ?>!important;
padding-bottom:<?php echo get_theme_mod('dc_woo_forms_bandt_padding'); ?>!important;
padding-left:<?php echo get_theme_mod('dc_woo_forms_;andr_padding'); ?>!important;
padding-right:<?php echo get_theme_mod('dc_woo_forms_landr_padding'); ?>!important;
min-height:150px!important;
border-radius:<?php echo get_theme_mod('dc_woo_forms_border_radius'); ?>!important;
color:<?php echo get_theme_mod('dc_woo_form_colour'); ?>!important;
}
.woocommerce-checkout ::-webkit-input-placeholder {
color:<?php echo get_theme_mod('dc_woo_placeholder'); ?>!important;
}
.woocommerce-checkout :-moz-placeholder { /* Firefox 18- */
color:<?php echo get_theme_mod('dc_woo_placeholder'); ?>!important;
}
.woocommerce-checkou t::-moz-placeholder {  /* Firefox 19+ */
color:<?php echo get_theme_mod('dc_woo_placeholder'); ?>!important;
}
.woocommerce-checkout :-ms-input-placeholder {  
color:<?php echo get_theme_mod('dc_woo_placeholder'); ?>!important;
}
.woocommerce-checkout .form-row label{
color:<?php echo get_theme_mod('dc_woo_label_colour'); ?>!important;
}
.form-row .button.alt{
background: <?php echo get_theme_mod('dc_woo_button_bg_colour'); ?>!important;
border:<?php echo get_theme_mod('dc_woo_button_border_width'); ?> <?php echo get_theme_mod('dc_woo_button_border_colour'); ?> <?php echo get_theme_mod('dc_woo_button_border_style'); ?>!important;
color:<?php echo get_theme_mod('dc_woo_button_colour'); ?>!important;
border-radius:<?php echo get_theme_mod('dc_woo_button_border_radius'); ?>!important;
}
.form-row .button.alt:hover {
background: <?php echo get_theme_mod('dc_woo_button_bg_colour_hover'); ?>!important;
border:<?php echo get_theme_mod('dc_woo_button_border_width_hover'); ?> <?php echo get_theme_mod('dc_woo_button_border_colour_hover'); ?> <?php echo get_theme_mod('dc_woo_button_border_style_hover'); ?>!important;
color:<?php echo get_theme_mod('dc_woo_button_colour_hover'); ?>!important;
border-radius:<?php echo get_theme_mod('dc_woo_button_border_radius_hover'); ?>!important;
} 
.woocommerce-MyAccount-content {
    background:<?php echo get_theme_mod('dc_account_content_background'); ?>!important;
    width: 78%!important;
    padding: <?php echo get_theme_mod('dc_account_content_area_padding'); ?>!important;
    border: 1px solid <?php echo get_theme_mod('dc_account_content_border_colour'); ?>!important;
    color:<?php echo get_theme_mod('dc_account_content_text_colour'); ?>!important;
}
div.woocommerce-MyAccount-content p a{
  color:<?php echo get_theme_mod('dc_account_content_link_colour'); ?>!important;
}
div.woocommerce-MyAccount-content p a:hover{
  color:<?php echo get_theme_mod('dc_account_content_link_colour_hover'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table {
    margin: -<?php echo get_theme_mod('dc_account_content_area_padding'); ?>!important;
    width: calc(100% + <?php echo get_theme_mod('dc_account_content_area_padding'); ?> + <?php echo get_theme_mod('dc_account_content_area_padding'); ?> + 1px)!important;
}
.woocommerce-MyAccount-content .shop_table a{
color:<?php echo get_theme_mod('dc_account_content_link_colour'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table a:hover{
  color:<?php echo get_theme_mod('dc_account_content_link_colour_hover'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table th {
    background: <?php echo get_theme_mod('dc_account_table_header_background'); ?>!important;
    padding-top: 10px!important;
    padding-bottom: 10px!important;
}
.entry-content .woocommerce-MyAccount-content thead th{
color: <?php echo get_theme_mod('dc_account_header_text_colour'); ?>!important;
text-transform:<?php echo get_theme_mod('dc_account_table_header_tt'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table tr:nth-child(even) {
    background: <?php echo get_theme_mod('dc_account_row_even_background'); ?>!important;
    color:<?php echo get_theme_mod('dc_account_row_even_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_account_row_even_fw'); ?>!important;
    font-size:<?php echo get_theme_mod('dc_account_row_even_font_size'); ?>!important;
    text-transform:<?php echo get_theme_mod(''); ?>!important;
}
.woocommerce-MyAccount-content .shop_table tr:nth-child(even) a{
    font-weight:<?php echo get_theme_mod('dc_account_row_even_fw'); ?>!important;
    font-size:<?php echo get_theme_mod('dc_account_row_even_font_size'); ?>!important;
    text-transform:<?php echo get_theme_mod('dc_account_row_even_tt'); ?>!important;
    color:<?php echo get_theme_mod('dc_account_row_even_link_colour');?>!important;
}
.woocommerce-MyAccount-content .shop_table tr:nth-child(even) a:hover{
    color:<?php echo get_theme_mod('dc_row_even_link_hover_colour'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table tr:nth-child(odd) {
    background: <?php echo get_theme_mod('dc_account_row_odd_background'); ?>!important;
    color:<?php echo get_theme_mod('dc_account_row_odd_text_colour'); ?>!important;
    font-weight:<?php echo get_theme_mod('dc_account_row_odd_fw'); ?>!important;
    font-size:<?php echo get_theme_mod('dc_account_row_odd_font_size'); ?>!important;
    text-transform:<?php echo get_theme_mod('dc_account_row_odd_tt'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table tr:nth-child(odd) a{
    font-weight:<?php echo get_theme_mod('dc_account_row_odd_fw'); ?>!important;
    font-size:<?php echo get_theme_mod('dc_account_row_odd_font_size'); ?>!important;
    text-transform:<?php echo get_theme_mod('dc_account_row_odd_tt'); ?>!important;
    color:<?php echo get_theme_mod('dc_account_row_odd_link_colour'); ?>!important;
}
.woocommerce-MyAccount-content .shop_table tr:nth-child(odd) a:hover{
    color:<?php echo get_theme_mod('dc_row_odd_link_hover_colour'); ?>!important;
}
.woocommerce-MyAccount-content .woocommerce-Pagination {
    margin-left: -10px!important;
    margin-bottom: -10px!important;
    margin-top: -19px!important;
}

.woocommerce-MyAccount-navigation {
    width: 20%!important;
}

.woocommerce-MyAccount-navigation li {
    list-style: none!important;
    border-bottom: 1px solid <?php echo get_theme_mod('dc_account_nav_border'); ?>!important;
    padding-top: 10px!important;
    padding-bottom: 10px!important;
}
.woocommerce-MyAccount-navigation-link.is-active a, .woocommerce-MyAccount-navigation li a:hover{
color:<?php echo get_theme_mod('dc_account_nav_active_hover'); ?>!important;
}
.woocommerce-MyAccount-navigation li:first-of-type {
    border-top: 1px solid <?php echo get_theme_mod('dc_account_nav_border'); ?>!important;
}

.woocommerce-MyAccount-navigation li a {
    font-size: <?php echo get_theme_mod('dc_account_nav_font_size'); ?>!important;
    color: <?php echo get_theme_mod('dc_account_nav_link_colour'); ?>!important;
    font-weight: <?php echo get_theme_mod('dc_account_nav_fw'); ?>!important;
    text-transform: <?php echo get_theme_mod('dc_account_nav_tt'); ?>!important;
}

mark {
    background-color: <?php echo get_theme_mod('dc_account_mark_background'); ?>!important;
    color: <?php echo get_theme_mod('dc_account_mark_colour'); ?>!important;
    padding-left: 3px!important;
    padding-right: 3px!important;
    padding-top: 3px!important;
    padding-bottom: 3px!important;
}
/* Landscape Phones */
@media ( min-width: 480px ) and ( max-width: 767px ) {
.woocommerce-MyAccount-content, .woocommerce-MyAccount-navigation{
width:100%!important;
}
}
 
/* Portrait Phones */
@media ( max-width: 479px ) {
.woocommerce-MyAccount-content, .woocommerce-MyAccount-navigation{
width:100%!important;
}
}
.single-product #main-content{
background:<?php echo get_theme_mod('dc_base_page_background'); ?>!important;
}
nav.woocommerce-breadcrumb, nav.woocommerce-breadcrumb a{
  color:<?php echo get_theme_mod('dc_base_breadcrumbs_colour');?>!important;
}
.woocommerce-LostPassword.lost_password a{
color:<?php echo get_theme_mod('dc_login_forgot_colour');?>!important;
}
.woocommerce-LostPassword.lost_password a:hover{
color:<?php echo get_theme_mod('dc_login_forgot_colour_h');?>!important;
}
.single-product .et_pb_module h2 {
    display: block!important;
}
</style>
<?php }

add_action( 'wp_head', 'dc_customize_css' );