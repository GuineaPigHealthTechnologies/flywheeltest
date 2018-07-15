<?php 
/*
Template Name: 404 Page
*/
get_header(); 
?> 

<div id="main-content" class="not-found-404"> 

	<div class="et_pb_section" >
		<div class="et_pb_row clearfix et_pb_text_align_center et_pb_bg_layout_light">  

		<p class="large-404">404</p>
		 <h2><span>Oops!</span> Page not found.</h2> 
		<p>Sorry, but the page you are looking for is not found. Please, make sure you have typed the current url.</p>
		
		 <div class="buttons-container">
		 	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="et_pb_button">Back to homepage</a>  
		 </div>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php  get_footer(); ?>