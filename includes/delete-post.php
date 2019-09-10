<?php

function buddyforms_cbublishing_delete_post( $post_id, $form_slug ) {
	global $post;
	add_thickbox();

	?>

    <script>
        jQuery(document).ready(function () {
            jQuery(document).on("click", '#buddyforms_delete_post_as_editor', function (evt) {


                var bf_delete_mail_subject = jQuery('#bf_delete_mail_subject').val();
                var bf_delete_mail_message = jQuery('#bf_delete_mail_message').val();

                if (bf_delete_mail_subject == '') {
                    alert('Mail Subject is a required field');
                    return false;
                }
                if (bf_delete_mail_message == '') {
                    alert('Message is a required field');
                    return false;
                }

                var post_id = jQuery('#buddyforms_delete_post_as_editor').attr("data-post_id");
                var form_slug = jQuery('#buddyforms_delete_post_as_editor').attr("data-form_slug");

                jQuery.ajax({
                    type: 'POST',
                    dataType: "json",
                    url: ajaxurl,
                    data: {
                        "action": "buddyforms_delete_post_as_editor",
                        "post_id": post_id,
                        "form_slug": form_slug,
                        "user_delete_email_select": user_delete_email_select,
                        "bf_delete_mail_message": bf_delete_mail_message
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

	<?php echo '<a id="buddyforms_delete" href="#TB_inline?width=800&height=600&inlineId=buddyforms_delete_modal" title="' . __( 'Delete Post', 'buddyforms' ) . '" class="thickbox"><span aria-label="' . __( 'Delete Post', 'buddyforms' ) . '" title="' . __( 'Delete Post', 'buddyforms' ) . '" class="dashicons dashicons-trash"> </span> ' . __( 'Delete Post', 'buddyforms' ) . '</a>'; ?>

    <div id="buddyforms_delete_modal" style="display:none;">
        <div id="buddyforms_delete_wrap">
            <br><br>

			<?php
			// Create the form object
			$delete_form = new Form( "buddyforms_delete_post" );


			// Set the form attribute
			$delete_form->configure( array(
				"prevent" => array( "bootstrap", "jQuery", "focus" ),
				'method'  => 'post'
			) );
			$delete_form->addElement( new Element_Textbox( 'Subject', 'user_delete_email_subject', array( 'value' => 'Delete Post Request' ) ) );
			$delete_form->addElement( new Element_Textarea( 'Add a Message', 'user_delete_email_message', array( 'value' => 'We share a post I like to delete. Please follow the link to approve the delete request.' ) ) );


			$delete_form->render();

			?>

            <br>
            <a id="buddyforms_delete_post_as_editor"
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
		echo __( 'There has been an error sending the message No post to edit is selected!', 'buddyforms' );
		die();

		return;
	}

	$user_delete_email_select = $_POST['user_delete_email_select'];


	$json['test'] = 'step by step it will become a nice and powerful solution';

	echo json_encode( $json );

	die();
}