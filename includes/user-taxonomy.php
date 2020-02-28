<?php

/**
 * Taxonomy used to related the post with the user
 */
function buddyforms_cpublishing_register_user_posts_taxonomy() {
	global $buddyforms;

	$labels = array(
		"name"          => __( "User Posts", "buddyforms-collaborative-publishing" ),
		"singular_name" => __( "User Post", "buddyforms-collaborative-publishing" ),
	);

	$args = array(
		"label"                 => __( "User Posts", "buddyforms-collaborative-publishing" ),
		"labels"                => $labels,
		"public"                => false,
		"publicly_queryable"    => false,
		"hierarchical"          => false,
		"show_ui"               => false,
		"show_in_menu"          => false,
		"show_in_nav_menus"     => false,
		"query_var"             => false,
		"rewrite"               => false,
		"show_admin_column"     => false,
		"show_in_rest"          => false,
		"rest_base"             => "user_posts",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit"    => false,
	);


	$cforms = buddyforms_cpublishing_get_team_forms();

	$post_types = array();
	foreach ( $cforms as $slug => $form_name ) {
		$post_types[] = $buddyforms[ $slug ]['post_type'];
	}

	register_taxonomy( "buddyforms_editors", $post_types, $args );

	register_taxonomy( "buddyforms_user_posts", 'user', $args );
}

add_action( 'init', 'buddyforms_cpublishing_register_user_posts_taxonomy' );
