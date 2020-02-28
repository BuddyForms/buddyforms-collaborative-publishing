<?php

/**
 * Delete post action with the popup
 *
 * @param $post_id
 * @param $form_slug
 *
 * @return string
 */
function buddyforms_cbublishing_delete_post( $post_id, $form_slug ) {
	ob_start();
	?>
	<?php echo sprintf( "<a data-form_slug=\"%s\" id=\"buddyforms_delete\" href=\"#TB_inline?width=800&height=auto&inlineId=buddyforms_delete_modal_%s\" title=\"%s\" class=\"thickbox\"><span aria-label=\"%s\" title=\"%s\" class=\"dashicons dashicons-trash\"> </span> %s</a>", $form_slug, $post_id, __( 'Delete Request', 'buddyforms' ), __( 'Delete Request', 'buddyforms' ), __( 'Delete Request', 'buddyforms' ), __( 'Delete Request', 'buddyforms' ) ); ?>

	<div id="buddyforms_delete_modal_<?php echo $post_id ?>" style="display:none;">
		<div id="buddyforms_delete_wrap">
			<?php
			global $buddyforms;
			// Create the form object
			$form_id = "buddyforms_delete_post_" . $post_id;

			$delete_form = new Form( $form_id );

			// Set the form attribute
			$delete_form->configure( array(
				"prevent" => array( "bootstrap", "jQuery", "focus" ),
				'method'  => 'post'
			) );
			$delete_form->addElement( new Element_Textbox( __( 'Subject', 'buddyforms-collaborative-publishing' ), 'post_delete_email_subject_' . $post_id, array(
				'value'    => __( 'Delete Post Request', 'buddyforms-collaborative-publishing' ),
				'required' => 'required',
			) ) );

			$delete_request_message = '';
			if ( isset( $buddyforms[ $form_slug ]['form_fields'] ) ) {
				foreach ( $buddyforms[ $form_slug ]['form_fields'] as $key => $form_field ) {
					if ( $form_field['type'] == 'collaborative-publishing' ) {
						$delete_request_message = $form_field['delete_request_message'];
					}
				}
			}

			$delete_form->addElement( new Element_Textarea( __( 'Add a Message', 'buddyforms-collaborative-publishing' ), 'post_delete_email_message_' . $post_id, array(
				'value' => $delete_request_message,
				'class' => 'collaburative-publishiing-message',
			), array( 'required' => 'required', ) ) );
			$delete_form->render();
			?>
			<br>
			<button data-post_id="<?php echo esc_attr( $post_id ) ?>" data-form_slug="<?php echo esc_attr( $form_slug ) ?>" href="#" class="button buddyforms_send_delete_request"><?php echo __( 'Sent Delete Request', 'buddyforms-collaborative-publishing' ) ?></button>
		</div>
	</div>
	<?php
	$content = ob_get_clean();

	return $content;
}
