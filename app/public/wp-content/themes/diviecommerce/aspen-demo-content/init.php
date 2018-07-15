<?php



// Exit if accessed directly

if ( !defined( 'ABSPATH' ) ) exit;



/** Display verbose errors */

define( 'IMPORT_DEBUG', false );



// Don't duplicate me!

if ( !class_exists( 'AGS_Theme_Demo_Data_Importer' ) ) {



	require_once( dirname( __FILE__ ) . '/importer/aspen-importer.php' ); //load admin theme data importer



	class AGS_Theme_Demo_Data_Importer extends AGS_Theme_Importer {



		/**

		 * Set framewok

		 *

		 * options that can be used are 'default', 'radium' or 'optiontree'

		 *

		 * @since 0.0.3

		 *

		 * @var string

		 */

		public $theme_options_framework = 'radium';



		/**

		 * Set the key to be used to store theme options

		 *

		 * @since 0.0.2

		 *

		 * @var string

		 */

		public $theme_option_name       = 'my_theme_options_name'; //set theme options name here (key used to save theme options). Optiontree option name will be set automatically



		/**

		 * Set name of the theme options file

		 *

		 * @since 0.0.2

		 *

		 * @var string

		 */

		public $theme_options_file_name = 'theme_options.dat';



		/**

		 * Set name of the widgets json file

		 *

		 * @since 0.0.2

		 *

		 * @var string

		 */

		public $widgets_file_name       = 'widgets.json';



		/**

		 * Set name of the content file

		 *

		 * @since 0.0.2

		 *

		 * @var string

		 */

		public $content_demo_file_name  = 'content.xml';


		public $caldera_form_file_names = array('contact-form-export.json', 'footer-newsletter-export.json' , 'newsletter-form-export.json');
		
		/**

		 * Holds a copy of the widget settings

		 *

		 * @since 0.0.2

		 *

		 * @var string

		 */

		public $widget_import_results;



		/**

		 * Constructor. Hooks all interactions to initialize the class.

		 *

		 * @since 0.0.1

		 */

		public function __construct() {



			$this->demo_files_path = dirname(__FILE__) . '/demo-files/'; //can



			self::$instance = $this;

			parent::__construct();



		}



		/**

		 * Add menus - the menus listed here largely depend on the ones registered in the theme

		 *

		 * @since 0.0.1

		 */

		public function set_demo_menus(){



			// Menus to Import and assign - you can remove or add as many as you want

			$secondary_menu    = get_term_by('name', 'Secondary menu', 'nav_menu');

			$main_menu   = get_term_by('name', 'Main menu', 'nav_menu');

			//$footer_menu = get_term_by('name', 'Footer Menu', 'nav_menu');



			set_theme_mod( 'nav_menu_locations', array(

					'secondary-menu' => $secondary_menu->term_id,

					'primary-menu' => $main_menu->term_id,

					//'footer-menu' => $footer_menu->term_id

				)

			);



			$this->flag_as_imported['menus'] = true;



		}



	}

}