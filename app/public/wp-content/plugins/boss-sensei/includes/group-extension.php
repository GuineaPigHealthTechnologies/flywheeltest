<?php
/**
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :

	class GType_Course_Group extends BP_Group_Extension {

		private $extension_slug;

		/**
		 * Your __construct() method will contain configuration options for 
		 * your extension, and will pass them to parent::init()
		 */
		function __construct() {
			global $bp;
			if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {

				$group_status = groups_get_groupmeta( $bp->groups->current_group->id, 'bp_course_attached', true );

				if ( $group_status ) {
					$name = __( 'Course', 'boss-sensei' );

					$this->extension_slug = 'experiences';
					$args = array(
						'slug' => $this->extension_slug,
						'name' => $name,
						'nav_item_position' => 10,
					);

					parent::init( $args );
				}
			}
		}

		function load_grid_display( $flag ) {
			return 'yes';
		}

		/**
		 * display() contains the markup that will be displayed on the main 
		 * plugin tab
		 */
		function display( $group_id = null ) {
            
			$group_id = bp_get_group_id();

			$group_status = groups_get_groupmeta( $group_id, 'bp_course_attached', true );
			$post = get_post( ( int ) $group_status );

            //Single course content part template
            boss_sensei_single_course_content_part( $post->ID );
		}

	}

endif; // if ( class_exists( 'BP_Group_Extension' ) )