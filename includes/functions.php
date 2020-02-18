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

	if ( empty( $buddyforms ) ) {
		return array();
	}

	foreach ( $buddyforms as $form_slug => $buddyform ) {
		if ( ! empty( $buddyform['form_fields'] ) ) {
			foreach ( $buddyform['form_fields'] as $key => $form_field ) {
				if ( $form_field['type'] == 'collaborative-publishing' ) {
					$teams[ $form_slug ] = $buddyform['name'];
				}
			}
		} else {
			return array();
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

add_filter( 'buddyforms_user_can_edit', 'buddyforms_cpublishing_user_can_edit', 10, 4 );

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
function buddyforms_cpublishing_current_user_can( $current_user_can, $form_slug, $post, $type ) {

	if ( $type == 'edit' ) {
		$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

		if ( in_array( $post->ID, $user_posts ) ) {
			$current_user_can = true;
		}
	}


	return $current_user_can;

}

add_filter( 'buddyforms_current_user_can', 'buddyforms_cpublishing_current_user_can', 10, 4 );

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
function buddyforms_cpublishing_user_can_delete( $user_can_delete, $form_slug, $post_id ) {
	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post_id, $user_posts ) ) {
		$user_can_delete = true;
	}

	return $user_can_delete;
}

//add_filter( 'buddyforms_user_can_delete', 'buddyforms_cpublishing_user_can_delete', 10, 3 );


function buddyforms_cpublishing_delete_post( $post_id, $current_user = '' ) {

	if ( empty( $current_user ) ) {
		$current_user = get_current_user_id();
	}

	$user_posts = wp_get_object_terms( $current_user, 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );

	if ( in_array( $post_id, $user_posts ) ) {

		// Delete Logic goes here !!

		// Remove from post meta
		$old_editors = get_post_meta( $post_id, 'buddyforms_editors', true );

		if ( ( $key = array_search( $current_user, $old_editors ) ) !== false ) {
			unset( $old_editors[ $key ] );
		}

		update_post_meta( $post_id, 'buddyforms_editors', $old_editors );

		// Remove from taxonomies
		$user_posts = wp_get_object_terms( $current_user, 'buddyforms_user_posts' );


		// Remove the post from the user posts taxonomy
		wp_remove_object_terms( $current_user, strval( $post_id ), 'buddyforms_user_posts', true );

		// Remove the user from the post editors
		wp_remove_object_terms( $post_id, strval( $current_user ), 'buddyforms_editors', true );


	}

//	echo $post_id;
//	die();
}

add_action( 'buddyforms_delete_post', 'buddyforms_cpublishing_delete_post', 10, 1 );


add_action( 'buddyforms_the_loop_actions_last', 'buddyforms_cpublishing_the_loop_actions' );
function buddyforms_cpublishing_the_loop_actions( $post_id ) {

	$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts', array( 'fields' => 'slugs' ) );
	$form_slug  = get_post_meta( $post_id, '_bf_form_slug', true );

	if ( in_array( $post_id, $user_posts ) ) {
		echo '<li>';
		echo '<a title="' . __( 'Remove as Editor', 'buddyforms' ) . '"  id="' . $post_id . '" class="bf_remove_as_editor" href="#"><span aria-label="' . __( 'Remove as Editor', 'buddyforms' ) . '" title="' . __( 'Remove as Editor', 'buddyforms' ) . '" class="dashicons dashicons-trash"> </span> ' . __( 'Remove as Editor', 'buddyforms' ) . '</a></li>';
		echo '</li>';
		echo '<li>';
		//echo '<a title="' . __( 'Delete Post', 'buddyforms' ) . '"  id="' . $post_id . '" class="bf_cpublishing_delete_post" href="#"><span aria-label="' . __( 'Delete Post', 'buddyforms' ) . '" title="' . __( 'Delete Post', 'buddyforms' ) . '" class="dashicons dashicons-trash"> </span> ' . __( 'Delete Post', 'buddyforms' ) . '</a></li>';
		buddyforms_cbublishing_delete_post( $post_id, $form_slug );
		echo '</li>';


	} else {
		$author_id = get_post_field( 'post_author', $post_id );
		if ( get_current_user_id() != $author_id ) {
			echo '<li>';
			echo '<a title="' . __( 'Become an Editor', 'buddyforms' ) . '"  id="' . $post_id . '" class="bf_become_an_editor" href="#"><span aria-label="' . __( 'Become an Editor', 'buddyforms' ) . '" title="' . __( 'Become an Editor', 'buddyforms' ) . '" class="dashicons dashicons-trash"> </span> ' . __( 'Become an Editor', 'buddyforms' ) . '</a></li>';
			echo '</li>';
		}

	}

}

/**
 * Include the pods field into JS options
 *
 * @param $buddyforms_global_js_data
 * @param $form_slug
 *
 * @return array()
 */
function buddyforms_collaborative_publishing_field_to_global( $buddyforms_global_js_data, $form_slug ) {
	if ( ! empty( $form_slug ) && ! empty( $buddyforms_global_js_data[ $form_slug ] ) && ! empty( $buddyforms_global_js_data[ $form_slug ]['form_fields'] ) ) {
		$new_fields = array();
		foreach ( $buddyforms_global_js_data[ $form_slug ]['form_fields'] as $field_id => $field ) {
			if ( $field['type'] === 'collaborative-publishing' ) {
				$label = __( 'Select Editors', 'buddyforms-collaborative-publishing' );
				if ( ! empty ( $field['cpublishing_editors_label'] ) ) {
					$label = $field['cpublishing_editors_label'];
				}
				$new_fields['buddyforms_editors'] = array(
					'slug'                     => 'buddyforms_editors',
					'label'                    => $label,
					'validation_error_message' => $field['validation_error_message'],
					'type'                     => 'dropdown'
				);
				if ( isset( $field['enable_teams'] ) ) {
					$label = __( 'Select a Team', 'buddyforms-collaborative-publishing' );
					if ( ! empty ( $field['cpublishing_team_label'] ) ) {
						$label = $field['cpublishing_team_label'];
					}
					$new_fields['buddyforms_teams'] = array(
						'slug'                     => 'buddyforms_teams',
						'label'                    => $label,
						'validation_error_message' => $field['validation_error_message'],
						'type'                     => 'dropdown'
					);
				}
			} else {
				$new_fields[ $field_id ] = $field;
			}
		}
		if ( ! empty( $new_fields ) ) {
			$buddyforms_global_js_data[ $form_slug ]['form_fields'] = $new_fields;
		}

	}

	return $buddyforms_global_js_data;
}

add_filter( 'buddyforms_global_localize_scripts', 'buddyforms_collaborative_publishing_field_to_global', 10, 2 );

/**
 * Search for the user in the invite popup. The users will belong to the role selected in the field
 */
function buddyforms_collaborative_publishing_load_users() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) || empty( $_POST['form_slug'] ) ) {
		die();
	}

	if ( ! wp_verify_nonce( $_POST['nonce'], 'bf_collaborative_publishing_load_users' . __DIR__ ) ) {
		die();
	}

	$args = array();

	$form_slug = sanitize_title( $_POST['form_slug'] );

	if ( ! empty( $_POST['search'] ) ) {
		$args['search'] = $_POST['search']; // sanitize_title_for_query( $_POST['search'] );
	}

	$cpublishing_editors = '';
	$field_options       = buddyforms_get_form_field_by( $form_slug, 'collaborative-publishing', 'type' );
	if ( ! empty( $field_options ) ) {
		$cpublishing_editors = ! empty( $field_options['cpublishing_editors'] ) ? $field_options['cpublishing_editors'] : '';
	}

	if ( ! empty( $cpublishing_editors ) ) {
		$args['role__in'] = array( $cpublishing_editors );
	}

	$user_result = false;

	if ( empty( $user_result ) ) {
		$user_result = new WP_User_Query( $args );
	}

	if ( is_wp_error( $user_result ) ) {
		wp_send_json_error( $user_result, 500 );
	} else {
		$response = new stdClass;
		$result   = array();

		if ( ! empty( $user_result->get_results() ) ) {
			foreach ( $user_result->get_results() as $user ) {
				$current       = new stdClass;
				$current->id   = $user->ID;
				$current->text = $user->display_name;
				$result[]      = $current;
			}
		} else {
			if ( is_email( $_POST['search'] ) ) {
				$current       = new stdClass;
				$current->id   = 'new-' . $_POST['search'];
				$current->text = $_POST['search'];
				$result[]      = $current;
			}
		}

		$response->results = $result;
		wp_send_json( $response );
	}
}

add_action( 'wp_ajax_bf_collaborative_publishing_load_users', 'buddyforms_collaborative_publishing_load_users' );
add_action( 'wp_ajax_nopriv_bf_collaborative_publishing_load_users', 'buddyforms_collaborative_publishing_load_users' );

/**
 * Save all related information to the editors
 *
 * @param int $post_id
 * @param array $posted_editors
 * @param $form_slug
 */
function buddyforms_cpublishing_update_editors( $post_id, $posted_editors, $form_slug ) {
	$editors           = array();
	$editors_to_notify = array();
	$old_editors       = get_post_meta( $post_id, 'buddyforms_editors', true );

	if ( ! empty( $posted_editors ) ) {
		// Update the editors array
		foreach ( $posted_editors as $editor ) {
			$editor             = intval( $editor );
			$editors[ $editor ] = $editor;
			$user_info = get_userdata( $editor );
			if ( ! empty( $user_info ) && ! is_wp_error( $user_info ) ) {
				$editors_to_notify[ $user_info->ID ] = $user_info->user_email;
			}
		}
		// Update the editors post meta
		update_post_meta( $post_id, 'buddyforms_editors', $editors );

		//Send notification to all the editors
		$invite_message = __( 'You got an invite to edit a post', 'buddyforms-collaborative-publishing' );
		$field_options  = buddyforms_get_form_field_by( $form_slug, 'collaborative-publishing', 'type' );
		if ( ! empty( $field_options ) ) {
			$invite_message = ! empty( $field_options['invite_message'] ) ? $field_options['invite_message'] : __( 'You got an invite to edit a post', 'buddyforms-collaborative-publishing' );
		}
		$email_body = $invite_message;
		$email_body = buddyforms_cpublishing_message_body( $email_body, $post_id, $form_slug );
		$from_email = get_option( 'admin_email' );
		buddyforms_cpublishing_message_existing_users( $editors_to_notify, $email_body, $from_email, $form_slug, $post_id );
	}

	// Add all editors to the buddyforms_editors taxonomy
	$term_editors = wp_set_post_terms( $post_id, $editors, 'buddyforms_editors', false );

	// Loop through the old editors and remove them from the buddyforms_user_posts taxonomy
	if ( ! empty( $old_editors ) ) {
		foreach ( $old_editors as $post_editor ) {
			if ( ! array_key_exists( intval( $post_editor ), $editors ) ) {
				wp_remove_object_terms( $post_editor, strval( $post_id ), 'buddyforms_user_posts' );
			}
		}
	}

	// Loop thru all editors and add the post to the buddyforms_user_posts taxonomy
	foreach ( $editors as $editor_id ) {
		$user_posts = wp_set_object_terms( $editor_id, strval( $post_id ), 'buddyforms_user_posts', true );
	}
}

/**
 * Save all related information to the Teams
 *
 * @param $post_id
 * @param $posted_teams
 * @param $form_slug
 */
function buddyforms_cpublishing_update_teams( $post_id, $posted_teams, $form_slug ) {
	$old_team = get_post_meta( $post_id, 'buddyforms_teams', true );

	// Update the teams and add all team members to the editors array
	if ( ! empty( $posted_teams ) ) {
		$team_page_id = intval( $posted_teams );
		// Make sure the team is not the same post
		if ( $team_page_id != $post_id ) {
			// Get all editors form the team post
			$team_editors = get_post_meta( $team_page_id, 'buddyforms_editors', true );

			//Add all team editors to the editors array
			if ( ! empty( $team_editors ) ) {
				foreach ( $team_editors as $teditor ) {
					$teditor             = intval( $teditor );
					$editors[ $teditor ] = $teditor;
				}
			}
		}

		// Update the team post meta
		update_post_meta( $post_id, 'buddyforms_teams', $team_page_id );
	}

	// Check if the team has changed and if so remove the old team members from the buddyforms_user_posts taxonomy
	if ( ! empty( $old_team ) ) {
		if ( $old_team != $posted_teams ) {
			$posted_teams = intval( $posted_teams );
			// Gert the old team members
			$old_team_editors = get_post_meta( $posted_teams, 'buddyforms_editors', true );

			// Remove the post_id from the old team members
			foreach ( $old_team_editors as $old_post_editor ) {
				wp_remove_object_terms( $old_post_editor, strval( $post_id ), 'buddyforms_user_posts' );
			}
		}
	}
}

/**
 * Save Fields
 *
 * @param array $custom_field
 * @param $post_id
 * @param $form_slug
 */
function buddyforms_cpublishing_update_post_meta( $custom_field, $post_id, $form_slug ) {
	if ( $custom_field['type'] == 'collaborative-publishing' ) {
		buddyforms_cpublishing_update_editors( $post_id, $_POST['buddyforms_editors'], $form_slug );
		buddyforms_cpublishing_update_teams( $post_id, $_POST['buddyforms_teams'], $form_slug );
	}
}

add_action( 'buddyforms_update_post_meta', 'buddyforms_cpublishing_update_post_meta', 10, 3 );

function buddyforms_cpublishing_process_shortcode( $string, $post, $form_slug ) {
	if ( ! empty( $string ) && ! empty( $post ) && ! empty( $form_slug ) ) {
		if ( is_numeric( $post ) ) {
			$post = WP_Post::get_instance( $post );
		}

		$post_title = $post->post_title;
		$postperma  = get_permalink( $post->ID );

		$user_info = get_userdata( $post->post_author );

		$usernameauth = '';
		if ( ! empty( $user_info->user_login ) ) {
			$usernameauth = $user_info->user_login;
		}
		$user_nicename = '';
		if ( ! empty( $user_info->user_nicename ) ) {
			$user_nicename = $user_info->user_nicename;
		}
		$first_name = '';
		if ( ! empty( $user_info->user_firstname ) ) {
			$first_name = $user_info->user_firstname;
		}
		$last_name = '';
		if ( ! empty( $user_info->user_lastname ) ) {
			$last_name = $user_info->user_lastname;
		}

		$post_link_html = ! empty( $postperma ) ? sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $postperma ), $postperma ) : '';

		$blog_title  = get_bloginfo( 'name' );
		$siteurl     = get_bloginfo( 'wpurl' );
		$siteurlhtml = "<a href='" . esc_url( $siteurl ) . "' target='_blank' >$siteurl</a>";

		$short_codes_and_values = array(
			'[user_login]'                => $usernameauth,
			'[user_nicename]'             => $user_nicename,
			'[first_name]'                => $first_name,
			'[last_name]'                 => $last_name,
			'[published_post_link_plain]' => $postperma,
			'[published_post_link_html]'  => $post_link_html,
			'[published_post_title]'      => $post_title,
			'[site_name]'                 => $blog_title,
			'[site_url]'                  => $siteurl,
			'[site_url_html]'             => $siteurlhtml,
		);

		// If we have content let us check if there are any tags we need to replace with the correct values.
		$string = stripslashes( $string );
		$string = buddyforms_get_field_value_from_string( $string, $post->ID, $form_slug );

		foreach ( $short_codes_and_values as $shortcode => $short_code_value ) {
			$string = buddyforms_replace_shortcode_for_value( $string, $shortcode, $short_code_value );
		}
	}

	return $string;
}

function buddyforms_cpublishing_message_body( $email_body, $post_id, $form_slug ) {
	$email_body = wp_check_invalid_utf8( $email_body );
	$email_body = wp_kses_post( $email_body );
	$email_body = buddyforms_cpublishing_process_shortcode( $email_body, $post_id, $form_slug );

	return $email_body;
}

function buddyforms_cpublishing_message_existing_users( $existing_user_emails, $email_body, $from_email, $form_slug, $post_id ) {
	global $buddyforms;
	foreach ( $existing_user_emails as $existing_user_email ) {
		$permalink = get_permalink( $buddyforms[ $form_slug ]['attached_page'] );
		$permalink = apply_filters( 'buddyforms_collaborative_publishing_the_loop_edit_permalink', $permalink, $buddyforms[ $form_slug ]['attached_page'], $form_slug );

		$edit_post_link = apply_filters( 'buddyforms_collaborative_publishing_loop_edit_post_link', sprintf( "<a title=\"%s\" id=\"%s\" class=\"bf_edit_post\" href=\"%sedit/%s/%s\"><span aria-label=\"%s\" class=\"dashicons dashicons-edit\"> </span> %s</a>", __( 'Edit', 'buddyforms-collaborative-publishing' ), $post_id, $permalink, $form_slug, $post_id, __( 'Edit', 'buddyforms-collaborative-publishing' ), __( 'Edit', 'buddyforms-collaborative-publishing' ) ), $post_id, $form_slug );

		// Now let us send the mail
		$subject    = apply_filters( 'buddyforms_collaborative_publishing_message_subject', __( 'You got an invite to edit', 'buddyforms-collaborative-publishing' ), $form_slug, $post_id, 'existing' );
		$mail_to    = $existing_user_email;
		$inner_body = $email_body;
		$inner_body .= PHP_EOL . $edit_post_link;

		$inner_body = nl2br( $inner_body );
		$result     = buddyforms_email( $mail_to, $subject, $from_email, $from_email, $inner_body, array(), array(), $form_slug, $post_id );

		if ( ! $result ) {
			BuddyFormsCPublishing::error_log( sprintf( "Error Sending the existing user email. Form: %s", $form_slug ) );
		}
	}
}

function buddyforms_cpublishing_invite_new_user_as_editor() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	if ( ! isset( $_POST['action'] ) || ! isset( $_POST['nonce'] ) || empty( $_POST['form_slug'] ) || empty( $_POST['post_id'] ) ) {
		die();
	}

	if ( ! wp_verify_nonce( $_POST['nonce'], BUDDYFORMS_CPUBLISHING_INSTALL_PATH . 'bf_collaborative_publishing' ) ) {
		die();
	}

	global $buddyforms;

	$string_error = apply_filters( 'buddyforms_collaborative_publishing_invalid', __( 'There has been an error sending the message!', 'buddyforms-collaborative-publishing' ) );

	if ( ! isset( $_POST['post_id'] ) ) {
		echo $string_error;
		die();
	}

	$form_slug                = sanitize_title( $_POST['form_slug'] );
	$post_id                  = intval( $_POST['post_id'] );
	$user_invite_email_select = $_POST['user_invite_email_select'];
	$email_body               = ! empty( $_POST['user_invite_email_message'] ) ? $_POST['user_invite_email_message'] : '';

	$email_body = buddyforms_cpublishing_message_body( $email_body, $post_id, $form_slug );

	$from_email = get_option( 'admin_email' );

	$new_user_emails_saved = get_post_meta( $post_id, 'buddyforms_new_user_emails', true );

	$new_user_emails = array();
	if ( ! empty( $new_user_emails_saved ) ) {
		$new_user_emails = array_merge( $new_user_emails, $new_user_emails_saved );
	}
	$existing_user_emails = array();
	foreach ( $user_invite_email_select as $user ) {
		if ( substr( $user, 0, 3 ) == 'new' ) {
			$new_user_email = substr( $user, 4 );
			if ( ! in_array( $new_user_email, $new_user_emails ) ) {
				$new_user_emails[] = sanitize_email( $new_user_email );
			}
		} else {
			$user_id   = intval( $user );
			$user_info = get_userdata( $user_id );
			if ( ! empty( $user_info ) && ! is_wp_error( $user_info ) ) {
				$existing_user_emails[ $user_info->ID ] = $user_info->user_email;
			}
		}
	}

	buddyforms_cpublishing_message_existing_users( $existing_user_emails, $email_body, $from_email, $form_slug, $post_id );

	// Register new User
	$new_user_email_html = '';
	foreach ( $new_user_emails as $new_user_email ) {
		$new_user_email_html .= sprintf( "<li>%s</li>", $new_user_email );

		$field_options = buddyforms_get_form_field_by( $form_slug, 'collaborative-publishing', 'type' );
		if ( empty( $field_options['invite_register_page'] ) ) {
			continue;
		}

		$activation_link = sprintf( "<a href=\"%s?user_email=%s\">%s</a>", get_permalink( $field_options['invite_register_page'] ), $new_user_email, __( 'Register now!', 'buddyforms-collaborative-publishing' ) );
		$subject         = apply_filters( 'buddyforms_collaborative_publishing_message_subject', __( 'You got an invite to register and edit', 'buddyforms-collaborative-publishing' ), $form_slug, $post_id, 'new' );
		$mail_to         = $new_user_email;
		$inner_body      = $email_body;
		$inner_body      .= PHP_EOL . $activation_link;

		$inner_body = nl2br( $inner_body );
		$result     = buddyforms_email( $mail_to, $subject, $from_email, $from_email, $inner_body, array(), array(), $form_slug, $post_id );

		if ( ! $result ) {
			BuddyFormsCPublishing::error_log( sprintf( "Error Sending the new user email. Form: %s", $form_slug ) );
		}
	}

	if ( ! empty( $new_user_emails ) ) {
		update_post_meta( $post_id, 'buddyforms_new_user_emails', $new_user_emails );
	}

	if ( ! empty( $existing_user_emails ) ) {
		buddyforms_cpublishing_update_editors( $post_id, $existing_user_emails, $form_slug );
	}

	$json['old_user_emails'] = $existing_user_emails;
	if ( ! empty( $new_user_email_html ) ) {
		$json['new_user_email_html'] = $new_user_email_html;
	}
	echo json_encode( $json );

	die();
}

add_action( 'wp_ajax_buddyforms_invite_new_user_as_editor', 'buddyforms_cpublishing_invite_new_user_as_editor' );
