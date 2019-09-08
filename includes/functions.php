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


function buddyforms_cpublishing_activation_data_to_user( $user_id, $post_id, $source = 'invitation' ) {
	$code            = sha1( $user_id . time() );
	$activation_page = get_home_url();
	if ( isset( $buddyforms[ $form_slug ][ $source ]['activation_page'] ) && $buddyforms[ $form_slug ][ $source ]['activation_page'] != 'home' ) {
		if ( $buddyforms[ $form_slug ][ $source ]['activation_page'] == 'referrer' || $buddyforms[ $form_slug ][ $source ]['activation_page'] == 'none' ) {
			if ( ! empty( $_POST["redirect_to"] ) ) {
				$activation_page = $activation_page . esc_url( $_POST["redirect_to"] );
			}
		} else {
			$activation_page = get_permalink( $buddyforms[ $form_slug ][ $source ]['activation_page'] );
		}
	}
	$activation_link = add_query_arg( array(
		'key'       => $code,
		'user'      => $user_id,
		'form_slug' => $form_slug,
		'source'    => $source,
		'_wpnonce'  => buddyforms_create_nonce( 'buddyform_activate_user_link', $user_id )
	), $activation_page );

	add_user_meta( $user_id, 'has_to_be_activated', $code, true );
	add_user_meta( $user_id, 'bf_activation_link', $activation_link, true );

	return $activation_link;
}


add_filter( 'buddyforms_user_can_edit', 'buddyforms_cpublishing_user_can_edit', 10, 3 );

function buddyforms_cpublishing_user_can_edit( $is_author, $form_slug, $post_id ) {
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array('fields' => 'slugs') );

	if( in_array( $post_id, $user_posts) ){
		$is_author = true;
	}

	return $is_author;

}

add_filter( 'buddyforms_current_user_can', 'buddyforms_cpublishing_current_user_can', 10, 3 );

function buddyforms_cpublishing_current_user_can( $current_user_can, $form_slug, $post ) {

	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array('fields' => 'slugs') );

	if( in_array( $post->ID, $user_posts) ){
		$current_user_can = true;
	}

	return $current_user_can;

}