<?php

// Add a shortcode to invite new users as editors to my post.
function buddyforms_become_an_editor( $args ) {

	$post_id   = 0;
	$form_slug = '';

	extract( shortcode_atts( array(
		'post_id'   => '',
		'form_slug' => '',
	), $args ) );


	ob_start();
	buddyforms_cbublishing_invite_new_editor( $post_id, $form_slug );
	$tmp = ob_get_clean();

	return $tmp;
}

add_shortcode( 'buddyforms_become_an_editor', 'buddyforms_become_an_editor' );



function buddyforms_cpublishing_list_editor_posts( $args ) {

	ob_start();

	echo get_current_user_id();

	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts' );

	echo '<pre>';
	print_r($user_posts);
	echo '</pre>';

	$tmp = ob_get_clean();




	return $tmp;
}
add_shortcode( 'buddyforms_list_editor_posts', 'buddyforms_cpublishing_list_editor_posts' );