<?php

// Add a shortcode to invite new users as editors to my post.
function buddyforms_become_an_editor( $args ) {

	$post_id = 0;

	extract( shortcode_atts( array(
		'post_id'     => '',
	), $args ) );



	ob_start();
		buddyforms_cbublishing_invite_new_editor($post_id);
	$tmp = ob_get_clean();

	return $tmp;
}
add_shortcode('buddyforms_become_an_editor', 'buddyforms_become_an_editor');

