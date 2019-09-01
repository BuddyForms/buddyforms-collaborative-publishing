<?php

function buddyforms_cpublishing_register_user_posts_taxonomy() {

	/**
	 * Taxonomy: User Posts.
	 */

	$labels = array(
		"name" => __( "User Posts", "buddyforms" ),
		"singular_name" => __( "User Post", "buddyforms" ),
	);

	$args = array(
		"label" => __( "User Posts", "buddyforms" ),
		"labels" => $labels,
		"public" => false,
		"publicly_queryable" => false,
		"hierarchical" => false,
		"show_ui" => false,
		"show_in_menu" => false,
		"show_in_nav_menus" => false,
		"query_var" => false,
		"rewrite" => false,
		"show_admin_column" => false,
		"show_in_rest" => false,
		"rest_base" => "user_posts",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
	);
	register_taxonomy( "buddyforms_user_posts", 'user', $args );
}
add_action( 'init', 'buddyforms_cpublishing_register_user_posts_taxonomy' );
