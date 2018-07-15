<?php
  add_action('woocommerce_archive_description', 'show_link');
  function show_link() {
  if ( is_product_category() ) {
  echo '<h1>Hello</h1>';
   }
}