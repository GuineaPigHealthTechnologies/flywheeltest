<?php


add_action( 'admin_menu', 'ags_demo_data_admin_menu', 100 );


function ags_demo_data_admin_menu() {


	add_submenu_page('diviecommerce-options', __( 'Import Demo Data', 'Divi' ), __( 'Import Demo Data', 'Divi' ), 'manage_options', 'ags_demo_installer', 'ags_demo_data_admin_page');


}





function ags_demo_data_admin_page() {
	global $AGS_THEME_updater;
	if (!$AGS_THEME_updater->has_license_key()) {
		$AGS_THEME_updater->activate_page();
		return;
	}

	include(dirname(__FILE__).'/init.php');
	$importer = new AGS_Theme_Demo_Data_Importer();
	$importer->demo_installer();
}


add_action('admin_init', 'ags_demo_importer_admin_init');
function ags_demo_importer_admin_init() {
	global $pagenow;
	if ($pagenow == 'admin.php'
		&& isset($_REQUEST['page'])
		&& $_REQUEST['page'] == 'ags_demo_installer'
		) {
		
		wp_enqueue_style('ags-importer', get_stylesheet_directory_uri().'/aspen-demo-content/css/importer.css');
		wp_enqueue_script('jquery-circle-progress', get_stylesheet_directory_uri().'/aspen-demo-content/js/circle-progress.min.js', array(), false, true);
		wp_enqueue_script('ags-importer', get_stylesheet_directory_uri().'/aspen-demo-content/js/importer.js', array(), false, true);
		
		if (isset($_REQUEST['action'])
			&& $_REQUEST['action'] = 'demo-data'
			&& check_admin_referer('radium-demo-code' , 'demononce')) {
				
				include(dirname(__FILE__).'/init.php');
				$importer = new AGS_Theme_Demo_Data_Importer();
				echo('<html><head></head><body style="font-family: sans-serif; font-size: 12px; line-height: 150%;">');
				$importer->process_imports();
				echo('</body></html>');
				exit;
		
		}
	
	}
}

?>