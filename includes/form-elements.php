<?php

/**
 * Add CPUBLISHING form elementrs in the form elements select box
 *
 * @param array $elements_select_options
 *
 * @return mixed
 */
function buddyforms_cpublishing_elements_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return $elements_select_options;
	}
	$elements_select_options['cpublishing']['label']                              = __( 'Collaborative Publishing', 'buddyforms-collaborative-publishing' );
	$elements_select_options['cpublishing']['class']                              = 'bf_show_if_f_type_post';
	$elements_select_options['cpublishing']['fields']['collaborative-publishing'] = array(
		'label'  => __( 'Collaborative Publishing', 'buddyforms-collaborative-publishing' ),
		'is_pro' => true,
		'unique' => 'unique'
	);

	return $elements_select_options;
}

add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_cpublishing_elements_to_select', 1, 2 );


/**
 * Create the new CPUBLISHING Form Builder Form Elements
 *
 * @param $form_fields
 * @param $form_slug
 * @param $field_type
 * @param $field_id
 * @param $custom_field
 *
 * @return mixed
 */
function buddyforms_cpublishing_form_builder_form_elements( $form_fields, $form_slug, $field_type, $field_id, $custom_field ) {
	global $field_position, $buddyforms;


	switch ( $field_type ) {
		case 'collaborative-publishing':
			$roles = get_editable_roles();

			$roles_array = array( 'all' => 'All Roles' );
			foreach ( $roles as $role_kay => $role ) {
				$roles_array[ $role_kay ] = $role['name'];
			}

			$name                           = isset( $custom_field['name'] ) ? stripcslashes( $custom_field['name'] ) : __( 'Collaborative Publishing', 'buddyforms-collaborative-publishing' );
			$form_fields['general']['name'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][name]", array(
				'value'    => $name,
				'data'     => $field_id,
				'class'    => "use_as_slug",
				'required' => 1
			) );

			$cpublishing_editors = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors'] ) ) {
				$cpublishing_editors = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors'];
			}
			$form_fields['general']['cpublishing_editors'] = new Element_Select( '<b>' . __( 'Editors', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_editors]", $roles_array, array(
				'value'         => $cpublishing_editors,
				'data-field_id' => $field_id,
				'shortDesc'     => 'You can enable all users or filter the select for a specific user role'
			) );
//			$multiple_editors                                    = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_editors'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_editors'] : 'false';
//			$form_fields['general']['multiple_editors']          = new Element_Checkbox( '<b>' . __( 'Multiple Editors', 'buddyforms-collaborative-publishing') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_editors]", array( 'multiple_editors' => '<b>' . __( 'Multiple Editors', 'buddyforms-collaborative-publishing') . '</b>' ), array( 'value' => $multiple_editors ) );
			$cpublishing_editors_label                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors_label'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors_label'] ) : __( 'Select Editors', 'buddyforms-collaborative-publishing' );
			$form_fields['general']['cpublishing_editors_label'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_editors_label]", array(
				'data'      => $field_id,
				'value'     => $cpublishing_editors_label,
				'shortDesc' => 'Allow one or multiple editors'
			) );


//			$enable_moderation                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_moderation'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_moderation'] : 'false';
//			$form_fields['general']['enable_moderation'] = new Element_Checkbox( '<b>' . __( 'Enable Moderation', 'buddyforms-collaborative-publishing') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_moderation]", array( 'enable_moderation' => '<b>' . __( 'Enable Moderation', 'buddyforms-collaborative-publishing') . '</b>' ),
//				array(
//					'value'              => $enable_moderation,
//					'data'               => $field_id,
//					'class'              => 'bf_enable_moderation_hidden_checkbox',
//					'bf_hidden_checkbox' => 'bf_hide_if_not_multiple_moderators '
//				) );
//
//			$bf_hide_if_not_multiple_moderators = 'bf_hide_if_not_multiple_moderators';
//			if ( $enable_moderation == 'false' ) {
//				$bf_hide_if_not_multiple_moderators = 'bf_hide_if_not_multiple_moderators hidden';
//			}
//
//			$cpublishing_moderators = 'false';
//			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators'] ) ) {
//				$cpublishing_moderators = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators'];
//			}
//			$form_fields['general']['cpublishing_moderators']       = new Element_Select( '<b>' . __( 'Select Moderators', 'buddyforms-collaborative-publishing') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_moderators]", $roles_array, array(
//				'value'         => $cpublishing_moderators,
//				'data-field_id' => $field_id,
//				'class'         => $bf_hide_if_not_multiple_moderators,
//				'shortDesc'     => 'You can enable all users or filter the select for a specific user role'
//			) );
//			$multiple_moderators                                    = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_moderators'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_moderators'] : 'false';
//			$form_fields['general']['multiple_moderators']          = new Element_Checkbox( '<b>' . __( 'Multiple Moderators', 'buddyforms-collaborative-publishing') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_moderators]", array( 'multiple_moderators' => '<b>' . __( 'Multiple moderators', 'buddyforms-collaborative-publishing') . '</b>' ), array(
//				'value'     => $multiple_moderators,
//				'class'     => $bf_hide_if_not_multiple_moderators,
//				'shortDesc' => 'Allow one or multiple moderators.'
//			) );
//			$cpublishing_moderators_label                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators_label'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators_label'] ) : __( 'Select Moderators', 'buddyforms-collaborative-publishing');
//			$form_fields['general']['cpublishing_moderators_label'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms-collaborative-publishing') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_moderators_label]", array(
//				'data'  => $field_id,
//				'value' => $cpublishing_moderators_label,
//				'class' => $bf_hide_if_not_multiple_moderators
//			) );


			$enable_teams                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_teams'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['enable_teams'] : 'false';
			$form_fields['general']['enable_teams'] = new Element_Checkbox( '<b>' . __( 'Enable Teams', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][enable_teams]", array( 'enable_teams' => '<b>' . __( 'Enable Teams', 'buddyforms-collaborative-publishing' ) . '</b>' ),
				array(
					'value'              => $enable_teams,
					'data'               => $field_id,
					'class'              => 'bf_enable_teams_hidden_checkbox',
					'bf_hidden_checkbox' => 'bf_hide_if_not_enable_teams '
				) );

			$bf_hide_if_not_enable_teams = 'bf_hide_if_not_enable_teams';
			if ( $enable_teams == 'false' ) {
				$bf_hide_if_not_enable_teams = 'bf_hide_if_not_enable_teams hidden';
			}

			$cpublishing_teams = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_teams'] ) ) {
				$cpublishing_teams = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_teams'];
			}
			$form_fields['general']['cpublishing_teams'] = new Element_Select( '<b>' . __( 'Select a Team Base', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_teams]", buddyforms_cpublishing_get_team_forms(), array(
				'value'         => $cpublishing_teams,
				'data-field_id' => $field_id,
				'class'         => $bf_hide_if_not_enable_teams,
				'shortDesc'     => 'Select a form to use the form post type'
			) );

			$cpublishing_team_label                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_team_label'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_team_label'] ) : __( 'Select a Team', 'buddyforms-collaborative-publishing' );
			$form_fields['general']['cpublishing_team_label'] = new Element_Textbox( '<b>' . __( 'Label', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_team_label]", array(
				'data'  => $field_id,
				'value' => $cpublishing_team_label,
				'class' => $bf_hide_if_not_enable_teams,
			) );


			$invite_by_mail                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_by_mail'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_by_mail'] : 'false';
			$form_fields['general']['invite_by_mail'] = new Element_Checkbox( '<b>' . __( 'Invite by Mail', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][invite_by_mail]", array( 'invite_by_mail' => '<b>' . __( 'Invite by Mail', 'buddyforms-collaborative-publishing' ) . '</b>' ),
				array(
					'value'              => $invite_by_mail,
					'data'               => $field_id,
					'shortDesc'          => 'Display an invite by email button',
					'class'              => 'bf_invite_by_mail_hidden_checkbox',
					'bf_hidden_checkbox' => 'bf_hide_if_not_invite_by_mail '
				) );


			$bf_hide_if_not_invite_by_mail = 'bf_hide_if_not_invite_by_mail';
			if ( $invite_by_mail == 'false' ) {
				$bf_hide_if_not_invite_by_mail = 'bf_hide_if_not_invite_by_mail hidden';
			}


			// Get all allowed pages
			$all_pages = buddyforms_get_all_pages( 'id' );

			// After Submission Page
			$invite_register_page                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_register_page'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_register_page'] : 'false';
			$form_fields['general']['invite_register_page'] = new Element_Select( '<b>' . __( "Invite Register Page", 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][invite_register_page]", $all_pages, array(
				'value'     => $invite_register_page,
				'shortDesc' => __( 'Select the Page from where the content gets displayed. Will redirected to the page if ajax is disabled, otherwise display the content.', 'buddyforms-collaborative-publishing' ),
				'class'     => $bf_hide_if_not_invite_by_mail,
			) );


			$invite_message                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_message'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['invite_message'] ) : __( 'You got an invite to edit a post', 'buddyforms-collaborative-publishing' );
			$form_fields['general']['invite_message'] = new Element_Textarea( '<b>' . __( 'Message Text', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][invite_message]", array(
				'data'  => $field_id,
				'value' => $invite_message,
				'class' => $bf_hide_if_not_invite_by_mail,
			) );

			$delete_request_message                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['delete_request_message'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['delete_request_message'] ) : __( 'We share a post I like to delete. Please follow the link to approve the delete request.', 'buddyforms-collaborative-publishing' );
			$form_fields['general']['delete_request_message'] = new Element_Textarea( '<b>' . __( 'Delete Request Message Text', 'buddyforms-collaborative-publishing' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][delete_request_message]", array(
				'data'  => $field_id,
				'value' => $delete_request_message,
			) );

//			$edit_request_message                           = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['edit_request_message'] ) ? stripcslashes( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['edit_request_message'] ) : __( 'I like to edit this post', 'buddyforms-collaborative-publishing');
//			$form_fields['general']['edit_request_message'] = new Element_Textbox( '<b>' . __( 'Edit Request Message Text', 'buddyforms-collaborative-publishing') . '</b>', "buddyforms_options[form_fields][" . $field_id . "][edit_request_message]", array(
//				'data'  => $field_id,
//				'value' => $edit_request_message,
//			) );


			$form_fields['general']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'CPUBLISHING_field_key' );
			$form_fields['general']['type']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			$form_fields['general']['order'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][order]", $field_position, array( 'id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order' ) );
			break;

	}

	return $form_fields;
}

add_filter( 'buddyforms_form_element_add_field', 'buddyforms_cpublishing_form_builder_form_elements', 1, 6 );

/**
 * Display the new CPUBLISHING Fields in the frontend form
 *
 * @param Form $form
 * @param $form_args
 *
 * @return mixed
 */
function buddyforms_cpublishing_frontend_form_elements( $form, $form_args ) {
	global $buddyforms, $nonce;

	extract( $form_args );

	$post_type = $buddyforms[ $form_slug ]['post_type'];

	if ( ! $post_type ) {
		return $form;
	}

	if ( ! isset( $customfield['type'] ) ) {
		return $form;
	}

	switch ( $customfield['type'] ) {
		case 'collaborative-publishing':

//			$post_editors = wp_get_object_terms( $post_id, 'buddyforms_editors' );
//			$user_posts = wp_get_object_terms( get_current_user_id(), 'buddyforms_user_posts' );

//			ob_start();
//			echo 'Post Editors:<pre>';
//			print_r( $post_editors );
//			echo '</pre>';

//			echo '<br>User Posts<pre>';
//			print_r( $user_posts );
//			echo '</pre>';
//			$JHG = ob_get_clean();

//			$form->addElement( new Element_HTML( $JHG ) );


			if ( $customfield['cpublishing_editors'] == 'all' ) {
				$blogusers = get_users();
			} else {
				$blogusers = get_users( array(
					'role__in' => array( $customfield['cpublishing_editors'] )
				) );
			}
			// Array of WP_User objects.
			if ( ! ( isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] ) ) ) {
				$options['none'] = __( 'Select an Editor', 'buddyforms-collaborative-publishing' );
			}
			foreach ( $blogusers as $user ) {
				$options[ $user->ID ] = $user->user_nicename;
			}

			if ( ! empty( $customfield['frontend_reset'][0] ) ) {
				$element_attr['data-reset'] = 'true';
			}

			$label = __( 'Select Editors', 'buddyforms-collaborative-publishing' );
			if ( ! empty ( $customfield['cpublishing_editors_label'] ) ) {
				$label = $customfield['cpublishing_editors_label'];
			}

			$element_attr['class'] = $element_attr['class'] . ' bf-select2';
			$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_editors', true );
			$element_attr['id']    = 'col-lab-editors';

			$labels_layout = isset( $buddyforms[ $form_slug ]['layout']['labels_layout'] ) ? $buddyforms[ $form_slug ]['layout']['labels_layout'] : 'inline';

			if ( $labels_layout == 'inline' && isset( $customfield['required'] ) ) {
				$required                         = $form->getRequiredPlainSignal();
				$element_attr['data-placeholder'] = $label . $required;
			}

			$element = new Element_Select( $label, 'buddyforms_editors', $options, $element_attr );

			//if ( isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] ) ) {
			$element->setAttribute( 'multiple', 'multiple' );
			//}

			BuddyFormsAssets::load_select2_assets();

			$form->addElement( $element );


//			if ( isset( $customfield['enable_moderation'] ) ) {
//
//				$label = __( 'Select Moderators', 'buddyforms-collaborative-publishing' );
//				if ( isset ( $customfield['cpublishing_moderators_label'] ) ) {
//					$label = $customfield['cpublishing_moderators_label'];
//				}
//
//				$element_attr['class'] = $element_attr['class'] . ' bf-select2';
//				$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_moderators', true );
//				$element_attr['id']    = $customfield['slug'] . '-moderators';
//
//				$element = new Element_Select( $label, 'buddyforms_moderators', $options, $element_attr );
//
//				if ( isset( $customfield['multiple_moderators'] ) && is_array( $customfield['multiple_moderators'] ) ) {
//					$element->setAttribute( 'multiple', 'multiple' );
//				}
//
//				BuddyFormsAssets::load_select2_assets();
//
//				$form->addElement( $element );
//
//			}

			if ( isset( $customfield['enable_teams'] ) ) {
				$label = __( 'Select a Team', 'buddyforms-collaborative-publishing' );
				if ( ! empty ( $customfield['cpublishing_team_label'] ) ) {
					$label = $customfield['cpublishing_team_label'];
				}

				$element_attr['class'] = $element_attr['class'] . ' bf-select2';
				$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_teams', true );
				$element_attr['id']    = $customfield['slug'] . '-teams';

				$team_forms['none'] = __( 'Select a Team', 'buddyforms-collaborative-publishing' );
				if ( isset( $customfield['cpublishing_teams'] ) ) {

					$args      = array(
						'post_type'      => $buddyforms[ $customfield['cpublishing_teams'] ][ $post_type ],
						'post_status'    => 'publish',
						'posts_per_page' => 100
					);
					$the_query = new WP_Query( $args );


					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$team_forms[ get_the_ID() ] = get_the_title();
					}

				}

				if ( $labels_layout == 'inline' && isset( $customfield['required'] ) ) {
					$required           = $form->renderRequired();
					$team_forms['none'] = $label . $required;
				}

				$element = new Element_Select( $label, 'buddyforms_teams', $team_forms, $element_attr );

				BuddyFormsAssets::load_select2_assets();

				$form->addElement( $element );

			}

			if ( isset( $customfield['invite_by_mail'] ) ) {
				$element = new Element_HTML( do_shortcode( sprintf( "[buddyforms_become_an_editor form_slug=\"%s\" post_id=%s]", $form_slug, $post_id ) ) );

				$form->addElement( $element );


			}

			break;

	}

	return $form;
}

add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_cpublishing_frontend_form_elements', 1, 2 );
