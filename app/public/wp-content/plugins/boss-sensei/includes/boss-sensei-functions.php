<?php
/**
 * Boss sensei custom excerpt
 * @param type $text
 * @return type
 */
function boss_sensei_custom_excerpt($text) {

     $text = strip_shortcodes($text);

     /** This filter is documented in wp-includes/post-template.php */
     $text = apply_filters('the_content', $text);
     $text = str_replace(']]>', ']]&gt;', $text);

     /**
      * Filter the number of words in an excerpt.
      *
      * @since 2.7.0
      *
      * @param int $number The number of words. Default 55.
      */
     $excerpt_length = apply_filters('excerpt_length', 55);
     /**
      * Filter the string in the "more" link displayed after a trimmed excerpt.
      *
      * @since 2.9.0
      *
      * @param string $more_string The string shown within the more link.
      */
     $excerpt_more = apply_filters('excerpt_more', ' ' . '[&hellip;]');
     $text = wp_trim_words($text, $excerpt_length, $excerpt_more);
     return $text;
 }


/**
 * Boss single course content part template
 * @param $course_id
 */
function boss_sensei_single_course_content_part( $course_id ) {
     global $post;

     $post = get_post( $course_id );

     // Set up global course data.
     setup_postdata( $post );

     // Content Access Permissions
     $access_permission = false;

     if ( ! Sensei()->settings->get('access_permission')  || sensei_all_access() ) {

          $access_permission = true;

     } // End If Statement

     // Check if the user is taking the course
     $is_user_taking_course = Sensei_Utils::user_started_course( $course_id, get_current_user_id() );

     if(Sensei_WC::is_woocommerce_active()) {

          $wc_post_id = get_post_meta( $course_id, '_course_woocommerce_product', true );
          $product = Sensei()->sensei_get_woocommerce_product_object( $wc_post_id );

          $has_product_attached = isset ( $product ) && is_object ( $product );

     } else {

          $has_product_attached = false;

     }

     do_action( 'sensei_single_course_content_inside_before', $course_id );

     echo '<section class="entry-content">';


     if ( ( is_user_logged_in() && $is_user_taking_course )
          || ( $access_permission && !$has_product_attached)
          || 'full' == Sensei()->settings->get( 'course_single_content_display' ) ) {

          // compensate for core providing and empty $content

          if( empty( $content ) ){
               remove_filter( 'the_content', array( 'Sensei_Course', 'single_course_content') );
               $content = apply_filters( 'the_content', $post->post_content );
          }

          echo $content;
     }


     // only show modules expand/collapse on the course that has modules
     if( has_term( '', 'module', $post )  )  {
          //Course all modules expand/collapse link
          echo '<div class="listControl"><a class="expandList">'. esc_html__('Expand All', 'boss-sensei').'</a> | <a class="collapseList">'. esc_html__('Collapse All', 'boss-sensei').'</a></div></br>';
     }

     echo '</section>';

     do_action( 'sensei_single_course_content_inside_after', $course_id );

     // Reset global course data.
     wp_reset_postdata( $post );
}

/**
 * boss_sensei_course_lessons function.
 *
 * @access public
 * @param int $course_id (default: 0)
 * @param string $post_status (default: 'publish')
 * @param string $fields (default: 'all'). WP only allows 3 types, but we will limit it to only 'ids' or 'all'
 * @return array{ type WP_Post }  $posts_array
 */
function boss_sensei_course_lessons($course_id = 0, $post_status = 'publish', $fields = 'all') {

    if (is_a($course_id, 'WP_Post')) {
        $course_id = $course_id->ID;
    }

    /**
     * Filter runs inside Sensei_Course::course_lessons function
     *
     * Returns all lessons for a given course
     *
     * @param array $lessons
     * @param int $course_id
     */

    $course_lesson_query_args = array(
        'post_status'       => 'publish',
        'post_type'         => 'lesson',
        'posts_per_page'    => 500,
        'orderby'           => 'date',
        'order'             => 'ASC',
        'meta_query'        => array(
            array(
                'key' => '_lesson_course',
                'value' => intval( $course_id ),
            ),
        ),
        'suppress_filters'  => 0,
    );

    // Exclude lessons belonging to modules as they are queried along with the modules.
    $modules = Sensei()->modules->get_course_modules( $course_id );
    if( !is_wp_error( $modules ) && ! empty( $modules ) && is_array( $modules ) ){

        $terms_ids = array();
        foreach( $modules as $term ){

            $terms_ids[] = $term->term_id;

        }

        $course_lesson_query_args[ 'tax_query'] = array(
            array(
                'taxonomy' => 'module',
                'field'    => 'id',
                'terms'    => $terms_ids,
                'operator' => 'NOT IN',
            ),
        );
    }

    //setting lesson order
    $course_lesson_order = get_post_meta( $course_id, '_lesson_order', true);
    if( !empty( $course_lesson_order ) ){

        $course_lesson_query_args['post__in'] = explode( ',', $course_lesson_order );
        $course_lesson_query_args['orderby']= 'post__in' ;
        unset( $course_lesson_query_args['order'] );

    }

    $wp_query = new WP_Query( $course_lesson_query_args );

    $lessons = $wp_query->posts;

    return apply_filters('boss_sensei_course_lessons', $lessons, $course_id);
}


/**
 * Used for the uasort in boss_sensei_course_lessons function
 *
 * @param array $lesson_1
 * @param array $lesson_2
 * @return int
 */
function boss_sensei_short_course_lessons_callback( $lesson_1, $lesson_2 ) {

    if ( $lesson_1->course_order == $lesson_2->course_order ) {
        return 0;
    }

    return ($lesson_1->course_order < $lesson_2->course_order) ? -1 : 1;
}
// End course_lessons()

/**
 * Learner profile info
 * @param $user
 */
function boss_sensei_learner_profile_info( $user ) {

    /**
     * This hooke fires inside the Sensei_Learner_Profiles::user_info function.
     * just before the htmls is generated.
     * @since 1.0.0
     */
    do_action( 'sensei_learner_profile_info', $user );

    /**
     * This filter runs inside the Sensei_Learner_Profiles::user_info function.
     * Here you can change the user avatar.
     *
     * @since 1.0.0
     *
     * @param false|string `<img>` $user_avatar
     */
    $learner_avatar = apply_filters( 'sensei_learner_profile_info_avatar', get_avatar( $user->ID, 90 ), $user->ID );

    /**
     * This filter runs inside the Sensei_Learner_Profiles::user_info function.
     * Here you can change the learner profile user display name.
     * @since 1.0.0
     *
     * @param string $user_display_name
     * @param string $user_id
     */
    $learner_name = apply_filters( 'sensei_learner_profile_info_name', $user->display_name, $user->ID );

    /**
     * This filter runs inside the Sensei_Learner_Profiles::user_info function.
     * With this filter can change the users description on the learner user info
     * output.
     *
     * @since 1.0.0
     *
     * @param string $user_description
     * @param string $user_id
     */
    $learner_bio = apply_filters( 'sensei_learner_profile_info_bio', $user->description, $user->ID );
    ?>

    <div id="learner-info">

        <div class="learner-avatar"><?php echo $learner_avatar; ?></div>


        <div class="learner-content">

            <h2><?php echo $learner_name; ?></h2>

            <div class="learner-username"><span>@<?php echo $user->user_login ?></span></div>

            <div class="description"><?php echo wpautop( $learner_bio ); ?></div>

        </div>

    </div>

    <?php
}