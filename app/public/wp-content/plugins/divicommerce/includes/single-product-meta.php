<?php

add_filter( 'rwmb_meta_boxes', 'dc_single_product_meta_boxes' );
function dc_single_product_meta_boxes( $meta_boxes ) {
    $meta_boxes[] = array (
      'id' => 'divi-commerce-single-product',
      'title' => 'Divi Single Product',
      'pages' =>   array (
         'product',
      ),
      'context' => 'side',
      'priority' => 'high',
      'autosave' => true,
      'fields' =>   array (
         
        array (
          'id' => 'layout_selector',
          'type' => 'image_select',
          'name' => 'layout Select',
          'max_file_uploads' => 3,
          'options' =>       array (
            'deflyo' => 'http://www.divistride.com/wp-content/uploads/2017/08/default-layout.png',
            'deffllyo' => 'http://www.divistride.com/wp-content/uploads/2017/08/tabfull-layout.png',
            'fullyo' => 'http://www.divistride.com/wp-content/uploads/2017/08/full-layout.png',
          ),
          'class' => 'dclayoutimg',
          'std' =>       array (
             'deflyo',
          ),
        ),
      ),
    );

    return $meta_boxes;

}
