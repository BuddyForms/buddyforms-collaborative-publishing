<?php

/**
 * Add a shortcode to invite new users as editors to a post.
 *
 * @param $args
 *
 * @return false|string
 */
function buddyforms_become_an_editor( $args ) {

	$post_id   = 0;
	$form_slug = '';

	extract( shortcode_atts( array(
		'post_id'   => '',
		'form_slug' => '',
	), $args ) );

	if ( empty( $form_slug ) ) {
		return '';
	}

	BuddyFormsCPublishing::setNeedAssets( true, $form_slug );
	ob_start();
	buddyforms_cbublishing_invite_new_editor( $post_id, $form_slug );
	$tmp = ob_get_clean();

	return $tmp;
}

add_shortcode( 'buddyforms_become_an_editor', 'buddyforms_become_an_editor' );

/**
 * Create a shortcode to display the logged in collaborative user posts
 *
 * @return false|string
 */
function buddyforms_cpublishing_list_editor_posts() {
	global $the_lp_query;

	ob_start();

	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( $user_posts ) {
		$the_lp_query = new WP_Query( array( 'post__in' => $user_posts, 'post_status' => 'any', 'post_type' => 'any' ) );
		buddyforms_locate_template( 'the-loop' );
		wp_reset_postdata();
	} else {
		echo __( '<p>There are no collaborative posts for you to edit.</p>', 'buddyforms-collaborative-publishing' );
	}

	$tmp = ob_get_clean();

	return $tmp;
}

add_shortcode( 'buddyforms_list_editor_posts', 'buddyforms_cpublishing_list_editor_posts' );
