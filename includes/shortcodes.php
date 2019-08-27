<?php

// Add a shortcode to invite new users as editors to my post.
function buddyforms_become_an_editor() {


	ob_start();
		buddyforms_cbublishing_invite_new_editor();
	$tmp = ob_get_clean();

	return $tmp;
}
add_shortcode('buddyforms_become_an_editor', 'buddyforms_become_an_editor');

