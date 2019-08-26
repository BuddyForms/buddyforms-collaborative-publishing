<?php

function buddyforms_cpublishing_get_team_forms() {
	global $buddyforms;

	$teams = array();
	foreach ( $buddyforms as $form_slug => $buddyform ) {
		if ( isset( $buddyform['form_fields'] ) ) {
			foreach ( $buddyform['form_fields'] as $key => $form_field ) {
				if ( $form_field['type'] == 'collaborative-publishing' ) {
					$teams[$form_slug] = $buddyform['name'];
				}
			}
		}
	}

	return $teams;
}