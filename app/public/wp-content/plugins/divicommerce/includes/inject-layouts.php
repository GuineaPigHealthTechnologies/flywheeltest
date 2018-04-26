<?php
function ds_dc_section_injector() { ?>
    <!-- Above the header -->
    <?php if( false != get_theme_mod( 'dc_add_header_atc' ) ) { ?>
    <li id="header-atc">
        <?php if ( is_singular( 'product' ) ) {
do_action( 'woocommerce_simple_add_to_cart', 1 ); 
       }  ?>
    </li> <?php } ?>
    <!--mini cart-->
    <?php if( false != get_theme_mod( 'dc_add_header_cart' ) ) { ?>
    <div id="cartcontents">
        <div class="widget_shopping_cart_content">
            <?php woocommerce_mini_cart(); ?>
        </div>
    </div>
    <?php } ?> 
    <?php if( false != get_theme_mod( 'dc_add_bottom_product_info' ) ) { ?>
    <div id="product-info-bottom">
        <?php if ( is_singular( 'product' ) ) { ?>
    <div>
    <?php 
        woocommerce_show_product_sale_flash();
		woocommerce_show_product_images (); 
    ?>
    </div>
    <div class="summary entry-summary">
    <?php
		woocommerce_template_single_title ();
		woocommerce_template_single_price ();
		woocommerce_template_single_excerpt ();
		woocommerce_template_single_add_to_cart ();
		woocommerce_template_single_meta ();
		?>
        </div>
     <?php    }  ?>
    </div>
    <?php } ?>
<script>
        jQuery(function($){ 
           
            $('.wc-tabs-wrapper').after($('#product-info-bottom'));
            $("#product-info-bottom").show();
            <?php if( false != get_theme_mod( 'dc_add_bottom_product_info' ) ) { ?>
            $('.wc-tabs-wrapper').after($('#product-info-bottom'));
            $("#product-info-bottom").show();
            <?php } ?>
            <?php if( false != get_theme_mod( 'dc_add_header_atc' ) ) { ?>
            // Above header - Added inside #main-header wrap
            $("#top-menu").append($("#header-atc"));
            $("#header-atc").show();
        <?php } 
            //Insert mini hover cart
            if( false != get_theme_mod( 'dc_add_header_cart' ) ) { ?>
            $(".et-cart-info").append($("#cartcontents"));
            $("#cartcontents").show();
            <?php } ?>
            });
           
    </script>
<?php 
} 
add_action('wp_footer', 'ds_dc_section_injector');