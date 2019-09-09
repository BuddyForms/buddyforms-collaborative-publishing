<?php

/**
 *
 * Get all forms with collaborative publishing functionality
 *
 * @return array
 *
 */
function buddyforms_cpublishing_get_team_forms() {
	global $buddyforms;

	$teams = array();
	foreach ( $buddyforms as $form_slug => $buddyform ) {
		if ( isset( $buddyform['form_fields'] ) ) {
			foreach ( $buddyform['form_fields'] as $key => $form_field ) {
				if ( $form_field['type'] == 'collaborative-publishing' ) {
					$teams[ $form_slug ] = $buddyform['name'];
				}
			}
		}
	}

	return $teams;
}

/**
 *
 * Make sure collaborative editors can edit the posts
 *
 * @param $is_author
 * @param $form_slug
 * @param $post_id
 *
 * @return bool
 */
function buddyforms_cpublishing_user_can_edit( $is_author, $form_slug, $post_id ) {
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post_id, $user_posts ) ) {
		$is_author = true;
	}

	return $is_author;

}
add_filter( 'buddyforms_user_can_edit', 'buddyforms_cpublishing_user_can_edit', 10, 3 );

/**
 *
 * Make sure current_user_can is set to true if collaborative post
 *
 * @param $current_user_can
 * @param $form_slug
 * @param $post
 *
 * @return bool
 */
function buddyforms_cpublishing_current_user_can( $current_user_can, $form_slug, $post ) {

	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post->ID, $user_posts ) ) {
		$current_user_can = true;
	}

	return $current_user_can;

}
add_filter( 'buddyforms_current_user_can', 'buddyforms_cpublishing_current_user_can', 10, 3 );

/**
 * @param $form_slug
 * @param $post_id
 *
 * @return mixed
 */
function buddyforms_cpublishing_loop_form_slug( $form_slug, $post_id ) {
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post_id, $user_posts ) ) {
		$form_slug = get_post_meta( get_the_ID(), '_bf_form_slug', true );
	}

	return $form_slug;
}
add_filter( 'buddyforms_loop_form_slug', 'buddyforms_cpublishing_loop_form_slug', 10, 2 );

/**
 * @param $user_can_delete
 * @param $form_slug
 * @param $post_id
 *
 * @return bool
 */
function buddyforms_cpublishing_user_can_delete( $user_can_delete, $form_slug, $post_id ){
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post_id, $user_posts ) ) {
		$user_can_delete = true;
	}

	return $user_can_delete;
}
add_filter( 'buddyforms_user_can_delete', 'buddyforms_cpublishing_user_can_delete', 10, 3 );


function buddyforms_cpublishing_delete_post( $post_id ){
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post_id, $user_posts ) ) {

		// Delete Logic goes here !!


	}

	echo $post_id;
	die();
}
add_action( 'buddyforms_delete_post', 'buddyforms_cpublishing_delete_post', 10, 1 );