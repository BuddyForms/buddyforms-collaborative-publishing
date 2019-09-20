<?php

add_action( 'wp_ajax_buddyforms_ask_to_become_an_editor', 'buddyforms_ask_to_become_an_editor' );
function buddyforms_ask_to_become_an_editor() {

	global $current_user, $buddyforms;
	$current_user = wp_get_current_user();

	$post_id  = intval( $_POST['post_id'] );
	$the_post = get_post( $post_id );

	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );

	$post_editors = wp_get_object_terms( $post_id, 'buddyforms_editors', array( 'fields' => 'slug' ) );

	$author_id = get_post_field( 'post_author', $post_id );

	array_push( $post_editors, $author_id );

	foreach ( $post_editors as $post_editor ) {

		$permalink = get_permalink( $buddyforms[ $form_slug ]['attached_page'] );
		$permalink = apply_filters( 'buddyforms_the_loop_edit_permalink', $permalink, $buddyforms[ $form_slug ]['attached_page'] );

//		$edit_post_link = buddyforms_edit_post_link( $text = null, $before = '', $after = '', $post_id, $echo = false );
//		$edit_post_link  = apply_filters( 'buddyforms_loop_edit_post_link', buddyforms_edit_post_link( '<span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ), '', '', 0, false), $post_id, $form_slug );
		$edit_post_link = apply_filters( 'buddyforms_loop_edit_post_link', '<a title="' . __( 'Edit', 'buddyforms' ) . '" id="' . $post_id . '" class="bf_edit_post" href="' . $permalink . 'edit/' . $form_slug . '/' . $post_id . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ) . '</a>', $post_id );
		// Now let us send the mail
		$subject = __( 'Edit post request' );

		$post_editor_info = get_userdata( $post_editor );


		$mail_to = $post_editor_info->user_email;

		$emailBody = $current_user->display_name . ' Ask to become an editor of the post: ';

		$emailBody .= $edit_post_link;


		$url = apply_filters('buddyforms_ask_to_become_an_author_url', get_author_posts_url( $current_user->ID ), $current_user->ID );

		$emailBody .= ' Link to the User <a href="' . $url . '">' . $current_user->display_name . '</a>';


//	$post       = get_post( $post_id );
//	$post_title = $post->post_title;
//	$postperma  = get_permalink( $post->ID );


		$from_email = get_option( 'admin_email' );


		$mailheaders = "MIME-Version: 1.0\n";
		$mailheaders .= "X-Priority: 1\n";
		$mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
		$mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
		$mailheaders .= "From: " . $from_email . "<" . $from_email . ">" . "\r\n";

		$message = '<html><head></head><body>' . $emailBody . '</body></html>';

		$result = wp_mail( $mail_to, $subject, $message, $mailheaders );
//		}

	}

	echo $post_id;
	die();
}