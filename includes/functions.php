<?php
// Get all forms with collaborative publishing functionality
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


//
// Make sure collaborative editors can edit the posts
//
add_filter( 'buddyforms_user_can_edit', 'buddyforms_cpublishing_user_can_edit', 10, 3 );
function buddyforms_cpublishing_user_can_edit( $is_author, $form_slug, $post_id ) {
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array('fields' => 'slugs') );

	if( in_array( $post_id, $user_posts) ){
		$is_author = true;
	}

	return $is_author;

}

//
// make sure current_user_can is set to true if collaborative post
//
add_filter( 'buddyforms_current_user_can', 'buddyforms_cpublishing_current_user_can', 10, 3 );
function buddyforms_cpublishing_current_user_can( $current_user_can, $form_slug, $post ) {

	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array('fields' => 'slugs') );

	if( in_array( $post->ID, $user_posts) ){
		$current_user_can = true;
	}

	return $current_user_can;

}