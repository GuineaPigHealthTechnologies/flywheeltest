<?php
/**
 * Group Course settings tab
 * The class_exists() check is recommended, to prevent problems during upgrade
 * or when the Groups component is disabled
 */
if ( class_exists( 'BP_Group_Extension' ) ) :

	class Group_Extension_Sensei_Course_Settings extends BP_Group_Extension {

		/**
		 * Your __construct() method will contain configuration options for 
		 * your extension, and will pass them to parent::init()
		 */
		function __construct() {
			$args = apply_filters( 'bp_sensei_group_extension_course_settings', array(
				'slug' => 'group-course-settings',
				'name' => 'Course Settings',
				'enable_nav_item'	=> false
			));

			parent::init( $args );
		}

		function display( $group_id = null ) {
		}
		
		/**
		 * settings_screen() is the catch-all method for displaying the content 
		 * of the edit, create, and Dashboard admin panels
		 */
		function settings_screen( $group_id = NULL ) {
			$group_status = groups_get_groupmeta( $group_id, 'bp_course_attached', true );
			$courses = get_posts( array(
				'post_type' => 'course',
				'posts_per_page' => 9999,
				'post_status'	=> 'publish'
			) );
			
			if ( !empty($courses) ) { ?>
				<div class="bp-sensei-group-course">
					<h4><?php _e('Group Course','sensei-buddypress'); ?></h4>
					<select name="bp_group_course" id="bp-group-course">
						<option value="-1"><?php _e( '--Select--', 'sensei-buddypress' ); ?></option>
						<?php
						foreach ( $courses as $course ) {
							$group_attached = get_post_meta( $course->ID, 'bp_course_group', true );
							if ( !empty( $group_attached ) && ( '-1' != $group_attached ) && $course->ID != $group_status ) {
								continue;
							}
							?><option value="<?php echo $course->ID; ?>" <?php echo (( $course->ID == $group_status )) ? 'selected' : ''; ?>><?php echo $course->post_title; ?></option><?php
						}
						?>
					</select>
				</div><br><br/><br/><?php
			}
			
			if ( !empty($group_status) && ( '-1' != $group_status )  ) {
				$bp_sensei_course_activity = groups_get_groupmeta( $group_id, 'group_extension_course_setting_activities' );
				if ( empty($bp_sensei_course_activity) ) {
					$bp_sensei_course_activity = array();
				}
				?>
				<div class="bp-sensei-course-activity-checkbox">
					<input type="hidden" name="activity-checkbox-enable" value="1" />
					<h4><?php _e('Course Activity','sensei-buddypress'); ?></h4>
					<p><?php _e('Which course activity should be displayed in this group?','sensei-buddypress'); ?></p><br/>
					<input type="checkbox" name="user_course_start" value="true" <?php echo $this->bp_is_checked( 'user_course_start', $bp_sensei_course_activity ); ?>><?php _e('User starts a course','sensei-buddypress'); ?><br>
					<input type="checkbox" name="user_course_end" value="true" <?php echo $this->bp_is_checked( 'user_course_end', $bp_sensei_course_activity ); ?> ><?php _e('User completes a course','sensei-buddypress'); ?><br>
					<input type="checkbox" name="user_lesson_start" value="true" <?php echo $this->bp_is_checked( 'user_lesson_start', $bp_sensei_course_activity ); ?> ><?php _e('User creates a lesson','sensei-buddypress'); ?><br>
					<input type="checkbox" name="user_lesson_end" value="true" <?php echo $this->bp_is_checked( 'user_lesson_end', $bp_sensei_course_activity ); ?> ><?php _e('User completes a lesson','sensei-buddypress'); ?><br>
					<input type="checkbox" name="user_quiz_pass" value="true" <?php echo $this->bp_is_checked( 'user_quiz_pass', $bp_sensei_course_activity ); ?> ><?php _e('User passes a quiz','sensei-buddypress'); ?><br>
					<input type="checkbox" name="user_lesson_comment" value="true" <?php echo $this->bp_is_checked( 'user_lesson_comment', $bp_sensei_course_activity );; ?> ><?php _e('User comments on single lesson page','sensei-buddypress'); ?><br>
				</div><br/>
				<?php
			}
		}

		/**
		 * settings_screen_save() contains the catch-all logic for saving 
		 * settings from the edit, create, and Dashboard admin panels
		 */
		function settings_screen_save( $group_id = NULL ) {
			
			$bp_sensei_course_activity = array();
			$old_course_id = groups_get_groupmeta( $group_id, 'bp_course_attached', true );

			if ( isset( $_POST[ 'bp_group_course' ] )  && ( $_POST[ 'bp_group_course' ] ) != '-1' ) {
				
				if ( ! empty( $old_course_id ) && $old_course_id != $_POST['bp_group_course'] ) {
					delete_post_meta($old_course_id, 'bp_course_group');
					groups_delete_groupmeta( $group_id, 'bp_course_attached' );
					bp_sensei_remove_members_group( $old_course_id, $group_id );
				}
				
				update_post_meta( $_POST[ 'bp_group_course' ], 'bp_course_group', $group_id );
				groups_add_groupmeta( $group_id, 'bp_course_attached', $_POST[ 'bp_group_course' ] );

				//since buddypress automatically create a forum if group forum is enables, we have to comment out bp_sensei_attach_forum function
				//bp_sensei_attach_forum($group_id);
				
				//Updating visibilty of group
				$group = groups_get_group( array( 'group_id' => $group_id ) );
				if ( 'public' == $group->status ) {
					$group->status = 'private';
				} elseif ( 'hidden' == $group->status ) {
					$group->status = 'hidden';
				}
				$group->save();
				
				//Adding teacher as admin of group
				bp_sensei_course_teacher_group_admin($_POST[ 'bp_group_course' ], $group_id);
				bp_sensei_update_group_avatar( $_POST[ 'bp_group_course' ], $group_id );
				
				bp_sensei_add_members_group($_POST[ 'bp_group_course' ], $group_id);

			} else {
				delete_post_meta($old_course_id, 'bp_course_group');
				groups_delete_groupmeta( $group_id, 'bp_course_attached' );
			}

			if ( !isset($_POST['activity-checkbox-enable'] ) ) {
				$bp_sensei_course_activity = array(
					'user_course_start'	=> 'true',
					'user_course_end'	=> 'true',
					'user_lesson_start'	=> 'true',
					'user_lesson_end'	=> 'true',
					'user_quiz_pass'	=> 'true',
					'user_lesson_comment'	=> 'true',
				);
			}

			$bp_sensei_course_activity['user_course_start'] =  isset( $_POST[ 'user_course_start' ] ) ? $_POST[ 'user_course_start' ] : 'false';

			$bp_sensei_course_activity['user_course_end'] =  isset( $_POST[ 'user_course_end' ] ) ? $_POST[ 'user_course_end' ] : 'false';

			$bp_sensei_course_activity['user_lesson_start'] =  isset( $_POST[ 'user_lesson_start' ] ) ? $_POST[ 'user_lesson_start' ] : 'false';

			$bp_sensei_course_activity['user_lesson_end'] = isset( $_POST[ 'user_lesson_end' ] ) ? $_POST[ 'user_lesson_end' ] : 'false';

			$bp_sensei_course_activity['user_quiz_pass'] = isset( $_POST[ 'user_quiz_pass' ] ) ? $_POST[ 'user_quiz_pass' ] : 'false';

			$bp_sensei_course_activity['user_lesson_comment'] =  isset( $_POST[ 'user_lesson_comment' ] )  ? $_POST[ 'user_lesson_comment' ] : 'false';

			groups_update_groupmeta( $group_id, 'group_extension_course_setting_activities', $bp_sensei_course_activity );
		}
		
		public function bp_is_checked( $value , $array ) {
			if ( ! array_key_exists( $value, $array )
			     || 'true' == $array[$value] ) {
					$checked = 'checked';
			}
			else {
				$checked = '';
			}
			return $checked;
		}
		
	}

 
endif; // if ( class_exists( 'BP_Group_Extension' ) )
