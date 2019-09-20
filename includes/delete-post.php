<?php

function buddyforms_cbublishing_delete_post( $post_id, $form_slug ) {
	global $post, $buddyforms;
	add_thickbox();

	?>

    <script>
        jQuery(document).ready(function () {
            jQuery(document).on("click", '#buddyforms_delete_post_as_editor_<?php echo $post_id ?>', function (evt) {

                var post_delete_email_subject = jQuery('#post_delete_email_subject_<?php echo $post_id ?>').val();
                var post_delete_email_message = jQuery('#post_delete_email_message_<?php echo $post_id ?>').val();

                if (post_delete_email_subject == '') {
                    alert('Mail Subject is a required field');
                    return false;
                }
                if (post_delete_email_message == '') {
                    alert('Message is a required field');
                    return false;
                }

                var post_id = jQuery(this).attr("data-post_id");
                var form_slug = jQuery(this).attr("data-form_slug");

                jQuery.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        "action": "buddyforms_delete_post_as_editor",
                        "post_id": post_id,
                        "form_slug": form_slug,
                        "post_delete_email_subject": post_delete_email_subject,
                        "post_delete_email_message": post_delete_email_message
                    },
                    success: function (data) {

                        console.log(data);

                        alert('Delete Request has been send successfully!');
                        tb_remove();

                    },
                    error: function (request, status, error) {
                        alert(request.responseText);
                    }
                });

            });
        });
    </script>
    <style>
        #buddyforms_delete_wrap input[type="text"] {
            width: 100%;
        }

        div#TB_ajaxContent {
            width: 96% !important;
            height: 96% !important;
        }
    </style>

	<?php echo '<a id="buddyforms_delete" href="#TB_inline?width=800&height=600&inlineId=buddyforms_delete_modal_' .  $post_id . '" title="' . __( 'Delete Post', 'buddyforms' ) . '" class="thickbox"><span aria-label="' . __( 'Delete Post', 'buddyforms' ) . '" title="' . __( 'Delete Post', 'buddyforms' ) . '" class="dashicons dashicons-trash"> </span> ' . __( 'Delete Post', 'buddyforms' ) . '</a>'; ?>

    <div id="buddyforms_delete_modal_<?php echo $post_id ?>" style="display:none;">
        <div id="buddyforms_delete_wrap">
            <br><br>

			<?php

			// Create the form object
            $form_id = "buddyforms_delete_post_" . $post_id;

			$delete_form = new Form( $form_id );


			// Set the form attribute
			$delete_form->configure( array(
				"prevent" => array( "bootstrap", "jQuery", "focus" ),
				'method'  => 'post'
			) );
			$delete_form->addElement( new Element_Textbox( 'Subject', 'post_delete_email_subject_' . $post_id, array( 'value' => 'Delete Post Request' ) ) );

			$delete_request_message = '';
			if ( isset( $buddyforms[$form_slug]['form_fields'] ) ) {
				foreach ( $buddyforms[$form_slug]['form_fields'] as $key => $form_field ) {
					if ( $form_field['type'] == 'collaborative-publishing' ) {
						$delete_request_message = $form_field['delete_request_message'];
					}
				}
			}

			$delete_form->addElement( new Element_Textarea( 'Add a Message', 'post_delete_email_message_' . $post_id , array( 'value' => $delete_request_message, 'class' => 'collaburative-publishiing-message' ) ) );


			$delete_form->render();

			?>

            <br>
            <a id="buddyforms_delete_post_as_editor_<?php echo $post_id ?>"
               data-post_id="<?php echo $post_id ?>"
               data-form_slug="<?php echo $form_slug ?>"
               href="#" class="button">Sent Delete Request</a>
        </div>
    </div>

	<?php

}

add_action( 'wp_ajax_buddyforms_delete_post_as_editor', 'buddyforms_delete_post_as_editor' );
function buddyforms_delete_post_as_editor() {
	global $buddyforms;

	if ( ! isset( $_POST['post_id'] ) ) {
		echo __( 'There has been an error sending the message.', 'buddyforms' );
		die();

		return;
	}


	global $current_user, $buddyforms;
	$current_user = wp_get_current_user();

	$post_id  = intval( $_POST['post_id'] );
	$the_post = get_post( $post_id );

	$form_slug = get_post_meta( $post_id, '_bf_form_slug', true );

	$post_editors = wp_get_post_terms( $post_id, 'buddyforms_editors' );


	$post_editors_array = array();
	foreach ($post_editors as $editor){
		$post_editors_array[$editor->slug] = $editor->slug;
    }

	$author_id = get_post_field( 'post_author', $post_id );

	$post_editors_array[$author_id] = $author_id;

	foreach ( $post_editors_array as $post_editor ) {

		$code = sha1( $post_editor . time() );
		update_user_meta( $post_editor, 'buddyform_delete_post_editor_key_' . $post_id, $code );


		$permalink = get_permalink( $post_id );
		//$permalink = apply_filters( 'buddyforms_the_loop_edit_permalink', $permalink, $buddyforms[ $form_slug ]['attached_page'] );

//		$edit_post_link = buddyforms_edit_post_link( $text = null, $before = '', $after = '', $post_id, $echo = false );
//		$edit_post_link  = apply_filters( 'buddyforms_loop_edit_post_link', buddyforms_edit_post_link( '<span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ), '', '', 0, false), $post_id, $form_slug );
//      $edit_post_link = apply_filters( 'buddyforms_loop_edit_post_link', '<a title="' . __( 'Edit', 'buddyforms' ) . '" id="' . $post_id . '" class="bf_edit_post" href="' . $permalink . 'edit/' . $form_slug . '/' . $post_id . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ) . '</a>', $post_id );

		//$delete_post_link = $permalink . '?bf_delete_post_request=' . $post_id;
		$delete_post_link = add_query_arg( array(
			'bf_delete_post_request' => $post_id,
			'key'                    => $code,
			'user'                   => $post_editor,
			'nonce'                  => buddyforms_create_nonce( 'buddyform_delete_post_editor_keys', $post_editor )
		), $permalink );


		// Now let us send the mail
		$subject = $_POST['post_delete_email_subject'];

		$post_editor_info = get_userdata( $post_editor );


		$mail_to = $post_editor_info->user_email;

		$emailBody = $_POST['post_delete_email_message'];

		//$emailBody .= ' ' . $edit_post_link;


		$emailBody .= ' <br>Link to the post: <a href="' . $permalink . '">' . $the_post->post_title . '</a><br>';
		$emailBody .= ' Approve delete Request: <a href="' . $delete_post_link . '">Yes, Delete Now</a>';


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


	$json['test'] = 'step by step it will become a nice and powerful solution';

	echo json_encode( $json );

	die();
}


add_action( 'init', 'buddyforms_delete_post_request' );

function buddyforms_delete_post_request() {

	if ( isset($_GET['bf_delete_post_request']) ) {

		$key     = $_GET['key'];
		$post_id = $_GET['bf_delete_post_request'];
		$user_id = $_GET['user'];
		$nonce   = $_GET['nonce'];


		if ( ! wp_verify_nonce( $nonce, 'buddyform_delete_post_editor_keys' ) ) {
		//	return false;
		}


		$buddyform_delete_post_editor = get_user_meta( $user_id, 'buddyform_delete_post_editor_key_' . $post_id, true );


		if ( isset( $buddyform_delete_post_editor) ) {
			if ( $key == $buddyform_delete_post_editor ) {
				// Delete editor from meta and taxonomies
				buddyforms_cpublishing_delete_post( $post_id, $user_id );


				$post_editors = wp_get_post_terms( $post_id, 'buddyforms_editors' );

				$post_count = count($post_editors);
				if($post_count == 0){
					do_action( 'buddyforms_delete_post', $post_id );
					wp_delete_post( $post_id );
                }

			}
		}



		// if only author is left and the author also has approved teh delete, the post should get deleted


		add_action( 'wp_head', 'buddyforms_delete_post_request_success' );
		//add_action('wp_head', 'buddyforms_delete_post_request_error');
	}
}

function buddyforms_delete_post_request_success() {

	?>
    <script>
        jQuery(document).ready(function () {
            alert('Delete Done');
            document.location.href = "/";
        });
    </script>
	<?php
}

function buddyforms_delete_post_request_error() {

	?>
    <script>
        jQuery(document).ready(function () {
            alert('Delete Error');
        });
    </script>
	<?php
}