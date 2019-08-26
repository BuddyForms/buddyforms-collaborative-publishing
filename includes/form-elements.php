<?php

/*
 * Add CPUBLISHING form elementrs in the form elements select box
 */
add_filter( 'buddyforms_add_form_element_select_option', 'buddyforms_cpublishing_elements_to_select', 1, 2 );
function buddyforms_cpublishing_elements_to_select( $elements_select_options ) {
	global $post;

	if ( $post->post_type != 'buddyforms' ) {
		return;
	}
	$elements_select_options['cpublishing']['label']                = 'Colaburative Publishing';
	$elements_select_options['cpublishing']['class']                = 'bf_show_if_f_type_post';
	$elements_select_options['cpublishing']['fields']['colaburative-publishing'] = array(
		'label' => __( 'Colaburative Publishing', 'buddyforms' ),
	);

	return $elements_select_options;
}

/*
 * Create the new CPUBLISHING Form Builder Form Elements
 *
 */
add_filter( 'buddyforms_form_element_add_field', 'buddyforms_cpublishing_form_builder_form_elements', 1, 5 );
function buddyforms_cpublishing_form_builder_form_elements( $form_fields, $form_slug, $field_type, $field_id ) {
	global $field_position, $buddyforms;


	switch ( $field_type ) {
		case 'colaburative-publishing':

			//unset( $form_fields );



			$roles = get_editable_roles();

			$roles_array = array( 'all' => 'All Roles' );
			foreach ( $roles as $role_kay => $role ) {
				$roles_array[$role_kay] = $role['name'];
			}





			$cpublishing_editors = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors'] ) ) {
				$cpublishing_editors = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_editors'];
			}
			$form_fields['general']['cpublishing_editors'] = new Element_Select( '<b>' . __( 'Select Editors', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_editors]", $roles_array, array(
				'value'         => $cpublishing_editors,
				'data-field_id' => $field_id
			) );
			$multiple_editors                         = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_editors'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_editors'] : 'false';
			$form_fields['general']['multiple_editors']       = new Element_Checkbox( '<b>' . __( 'Multiple Editors', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_editors]", array( 'multiple_editors' => '<b>' . __( 'Multiple Editors', 'buddyforms' ) . '</b>' ), array( 'value' => $multiple_editors ) );





			$cpublishing_moderators = 'false';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators'] ) ) {
				$cpublishing_moderators = $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['cpublishing_moderators'];
			}
			$form_fields['general']['cpublishing_moderators'] = new Element_Select( '<b>' . __( 'Select Moderators', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][cpublishing_moderators]", $roles_array, array(
				'value'         => $cpublishing_moderators,
				'data-field_id' => $field_id
			) );
			$multiple_moderators                         = isset( $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_moderators'] ) ? $buddyforms[ $form_slug ]['form_fields'][ $field_id ]['multiple_moderators'] : 'false';
			$form_fields['general']['multiple_moderators']       = new Element_Checkbox( '<b>' . __( 'Multiple Moderators', 'buddyforms' ) . '</b>', "buddyforms_options[form_fields][" . $field_id . "][multiple_moderators]", array( 'multiple_moderators' => '<b>' . __( 'Multiple moderators', 'buddyforms' ) . '</b>' ), array( 'value' => $multiple_moderators ) );






			$form_fields['general']['slug']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][slug]", 'CPUBLISHING_field_key' );
			$form_fields['general']['type']  = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][type]", $field_type );
			$form_fields['general']['order'] = new Element_Hidden( "buddyforms_options[form_fields][" . $field_id . "][order]", $field_position, array( 'id' => 'buddyforms/' . $form_slug . '/form_fields/' . $field_id . '/order' ) );
			break;

	}

	return $form_fields;
}

/*
 * Display the new CPUBLISHING Fields in the frontend form
 *
 */
add_filter( 'buddyforms_create_edit_form_display_element', 'buddyforms_cpublishing_frontend_form_elements', 1, 2 );
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
		case 'colaburative-publishing':

		if($customfield['cpublishing_editors'] == 'all'){
			$blogusers = get_users();
		} else {
			$blogusers = get_users( array(
				'role' => $customfield['cpublishing_editors']
			));
		}
		// Array of WP_User objects.
		foreach ( $blogusers as $user ) {
			$options[$user->ID] = $user->user_nicename;
		}

		if ( ! empty( $customfield['frontend_reset'][0] ) ) {
			$element_attr['data-reset'] = 'true';
		}

		$element_attr['class'] = $element_attr['class'] . ' bf-select2';
		$element               = new Element_Select( __( 'Select Editors', 'buddyforms' ), $slug, $options, $element_attr );

		if ( isset( $customfield['multiple_editors'] ) && is_array( $customfield['multiple_editors'] ) ) {
			$element->setAttribute( 'multiple', 'multiple' );
		}

		BuddyFormsAssets::load_select2_assets();

		$form->addElement( $element );







		if($customfield['cpublishing_moderators'] == 'all'){
			$blogusers = get_users();
		} else {
			$blogusers = get_users( array(
				'role' => $customfield['cpublishing_moderators']
			));
		}
		// Array of WP_User objects.
		foreach ( $blogusers as $user ) {
			$options[$user->ID] = $user->user_nicename;
		}

		if ( ! empty( $customfield['frontend_reset'][0] ) ) {
			$element_attr['data-reset'] = 'true';
		}

		$element_attr['class'] = $element_attr['class'] . ' bf-select2';
		$element               = new Element_Select( __( 'Select Moderators', 'buddyforms' ), $slug, $options, $element_attr );

		if ( isset( $customfield['multiple_moderators'] ) && is_array( $customfield['multiple_moderators'] ) ) {
			$element->setAttribute( 'multiple', 'multiple' );
		}

		BuddyFormsAssets::load_select2_assets();

		$form->addElement( $element );












			break;

	}

	return $form;
}
