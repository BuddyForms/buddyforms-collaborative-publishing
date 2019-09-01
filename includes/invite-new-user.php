<?php

add_action( 'buddyforms_post_edit_meta_box_select_form', 'buddyforms_cbublishing_invite_new_editor' );

function buddyforms_cbublishing_invite_new_editor( $post_id, $form_slug ) {
	global $post;
	add_thickbox();

	?>

    <script>
        jQuery(document).ready(function () {
            jQuery(document).on("click", '#buddyforms_invite_new_user_as_editor', function (evt) {


                var user_invite_email_select = jQuery('#user_invite_email_select').val();

                var bf_invite_mail_to = jQuery('#bf_invite_mail_to').val();
                var bf_invite_mail_subject = jQuery('#bf_invite_mail_subject').val();
                var bf_invite_mail_message = jQuery('#bf_invite_mail_message').val();

                if (bf_invite_mail_to == '') {
                    alert('Mail to is a required field, you need to select at leased one mail address');
                    return false;
                }
                if (bf_invite_mail_subject == '') {
                    alert('Mail Subject is a required field');
                    return false;
                }
                if (bf_invite_mail_message == '') {
                    alert('Message is a required field');
                    return false;
                }

                var post_id = jQuery('#buddyforms_invite_new_user_as_editor').attr("data-post_id");
                var form_slug = jQuery('#buddyforms_invite_new_user_as_editor').attr("data-form_slug");

                jQuery.ajax({
                    type: 'JSON',
                    url: ajaxurl,
                    data: {
                        "action": "buddyforms_invite_new_user_as_editor",
                        "post_id": post_id,
                        "form_slug": form_slug,
                        "user_invite_email_select": user_invite_email_select,
                        "bf_invite_mail_message": bf_invite_mail_message
                    },
                    success: function (data) {

                        if (data) {
                            jQuery('#buddyforms_panding_invites_list').html(data);
                        }

                        jQuery('#buddyforms_invite_wrap').html('<p>Invite send successfully</p>');


                    },
                    error: function (request, status, error) {
                        alert(request.responseText);
                    }
                });

            });
        });
    </script>
    <style>
        #buddyforms_invite_wrap input[type="text"] {
            width: 100%;
        }

        div#TB_ajaxContent {
            width: 96% !important;
            height: 96% !important;
        }
    </style>
<p><a id="buddyforms_invite" href="#TB_inline?width=800&height=600&inlineId=buddyforms_invite_modal"
      title="" class="thickbox button"><?php _e( 'Invite People as Editors', 'buddyforms' ) ?></a></p>

    <div id="buddyforms_panding_invites">
        <p>Pending Invites</p>
        <div id="buddyforms_panding_invites_list">
        </div>
    </div>

    <div id="buddyforms_invite_modal" style="display:none;">
        <div id="buddyforms_invite_wrap">


			<?php
			// Create the form object
			$form2 = new Form( "buddyforms_invite_new_user" );


			// Set the form attribute
			$form2->configure( array(
				"prevent" => array( "bootstrap", "jQuery", "focus" ),
				'method'  => 'post'
			) );

			$element_attr = array();
			$label        = __( 'Add email Address you want to invite', 'buddyforms' );

			$element_attr['class'] = $element_attr['class'] . ' bf-select2';
			$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_moderators', true );


			BuddyFormsAssets::load_select2_assets();


			$taxonomy = '';
			$order    = '';
			$exclude  = '';
			$include  = '';

			$args = array(
				'hide_empty'    => 0,
				'id'            => 'user_invite_email_select',
				'echo'          => false,
				'selected'      => false,
				'name'          => 'user_invite_email_select[]',
				'class'         => 'postform bf-select2-user_invite_email_select',
				'depth'         => 0,
				'tab_index'     => 0,
				'hide_if_empty' => false,
				'allowClear'    => true,
			);

			$placeholder = 'Select User';

			$args = array_merge( $args, Array(
				'multiple'          => 'multiple',
				'placeholder'       => $placeholder,
				'show_option_none'  => '',
				'option_none_value' => ''
			) );


			$args = apply_filters( 'buddyforms_wp_dropdown_users_args', $args, $post_id );

			$dropdown = wp_dropdown_users( $args );

			// Multiple
			$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );


			// Required
			$dropdown = str_replace( 'id=', 'required id=', $dropdown );


			$dropdown = str_replace( 'id=', 'data-form="user_invite_email_select" id=', $dropdown );
			$dropdown = str_replace( 'id=', 'data-placeholder="' . $placeholder . '" id=', $dropdown );
			$dropdown = str_replace( 'id=', 'style="width:100%;" id=', $dropdown );


			$required = $form2->renderRequired();

			$minimumResultsForSearch = '';
			//$tags                    = 'tags: true,';
			$maximumSelectionLength = '';
			$minimumInputLength     = '';
			$ajax_options           = '';
			$is_ajax                = true;

			if ( $is_ajax ) {
				$ajax_options .= $minimumInputLength;
				$ajax_options .= 'ajax:{ ' .
				                 'url: "' . admin_url( 'admin-ajax.php' ) . '", ' .
				                 'delay: 250, ' .
				                 'dataType: "json", ' .
				                 'cache: true, ' .
				                 'method : "POST", ' .#
				                 'data: function (params) { ' .
				                 'var query = { ' .
				                 'search: params.term, ' .
				                 'type: "public", ' .
				                 'action: "bf_load_users", ' .
				                 'nonce: "' . wp_create_nonce( 'bf_user_loading' ) . '", ' .
				                 //                                           'form_slug: "user_invite_email_select", ' .
				                 //				                 'taxonomy: "' . $taxonomy . '", ' .
				                 //				                 'order: "' . $order . '", ' .
				                 //				                 'exclude: "' . $exclude . '", ' .
				                 //				                 'include: "' . $include . '" ' .
				                 '}; ' .
				                 'console.log(query);' .
				                 'return query; ' .
				                 ' }, ' .

				                 '}, ';
			}
			$dropdown = '
						<script>
							jQuery(document).ready(function () {
							    jQuery(".bf-select2-user_invite_email_select").select2({
							            ' . $minimumResultsForSearch . '
										' . $maximumSelectionLength . '
										' . $ajax_options . '
										    placeholder: function(){
										        jQuery(this).data("placeholder");
										    },
                                     allowClear: true,
//                                     multiple: "multiple",
//                                     tags: true,
							        tokenSeparators: [\',\']
							    })
							    jQuery(".bf-select2-user_invite_email_select").on("change", function () {
							    
				                     
				                });
						    });
						</script>
						<div class="bf_field_group">
	                        <label for="editpost-element-user_invite_email_select">
	                           Invite Users
	                        </label>
	                        <div class="bf_inputs bf-input">' . $dropdown . '</div>
		                	<span class="help-inline">' . $description . '</span>
		                </div>';


			$form2->addElement( new Element_HTML( $dropdown ) );

			$form2->addElement( new Element_Textarea( 'Invite Message Text', 'user_invite_email_message' ) );


			$form2->render();

			?>


            <!--            <table class="form-table">-->
            <!--				<tbody>-->
            <!--				<tr>-->
            <!--					<th><label for="bf_invite_mail_to">Mail to:</label></th>-->
            <!--					<td><input id="bf_invite_mail_to" type="email" value=""></td>-->
            <!--				</tr>-->
            <!--				<tr>-->
            <!--					<th><label for="bf_invite_mail_subject">Mail Subject</label></th>-->
            <!--					<td><input id="bf_invite_mail_subject" type="text" value="-->
			<?php //echo __('You got Invited to edit this post'); ?><!--"></td>-->
            <!--				</tr>-->
            <!--				</tbody>-->
            <!--			</table>-->


            <br>
            <a id="buddyforms_invite_new_user_as_editor"
               data-post_id="<?php echo $post_id ?>"
               data-form_slug="<?php echo $form_slug ?>"
               href="#" class="button">Sent Invite</a>
        </div>
    </div>

	<?php

}

add_action( 'wp_ajax_buddyforms_invite_new_user_as_editor', 'buddyforms_invite_new_user_as_editor' );
function buddyforms_invite_new_user_as_editor() {
	global $buddyforms;

	if ( ! isset( $_POST['post_id'] ) ) {
		echo __( 'There has been an error sending the message No post to edit is selected!', 'buddyforms' );
		die();

		return;
	}

	$user_invite_email_select = $_POST['user_invite_email_select'];


	$new_user_emails = array();
	$old_user_emails = array();
	foreach ( $user_invite_email_select as $user ) {
		if ( substr( $user, 0, 3 ) == 'new' ) {
			$new_user_email    = substr( $user, 4 );
			$new_user_emails[] = $new_user_email;
		} else {
			$user_info         = get_userdata( $user );
			$old_user_emails[] = $user_info->user_email;
		}
	}



	foreach ( $old_user_emails as $old_user_email ) {

		$permalink = get_permalink( $buddyforms[ $_POST['form_slug'] ]['attached_page'] );
		$permalink = apply_filters( 'buddyforms_the_loop_edit_permalink', $permalink, $buddyforms[ $_POST['form_slug'] ]['attached_page'] );

//		$edit_post_link = buddyforms_edit_post_link( $text = null, $before = '', $after = '', $_POST['post_id'], $echo = false );
//		$edit_post_link  = apply_filters( 'buddyforms_loop_edit_post_link', buddyforms_edit_post_link( '<span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ), '', '', 0, false), $_POST['post_id'], $_POST['form_slug'] );
		$edit_post_link  = apply_filters( 'buddyforms_loop_edit_post_link', '<a title="' . __( 'Edit', 'buddyforms' ) . '" id="' . $_POST['post_id'] . '" class="bf_edit_post" href="' . $permalink . 'edit/' . $_POST['form_slug'] . '/' . $_POST['post_id'] . '"><span aria-label="' . __( 'Edit', 'buddyforms' ) . '" class="dashicons dashicons-edit"> </span> ' . __( 'Edit', 'buddyforms' ) . '</a>', $_POST['post_id'] );
		// Now let us send the mail
		$subject = __( 'You got an invite to edit' );

		$mail_to = $old_user_email;

		$emailBody = $_POST['bf_invite_mail_message'];

		$emailBody .= $edit_post_link;

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


	// Register new User
	$new_user_email_html = '';
	foreach ( $new_user_emails as $new_user_email ) {
		$new_user_email_html .= '<p>' . $new_user_email . '</p>';

//		$user_pass = $pass_confirm = wp_generate_password( 12, true );
//		$user_role = 'subscriber';
//		$new_user_id = wp_insert_user( array(
//			'user_pass'       => $user_pass,
//			'user_email'      => $new_user_email,
//			'user_login'      => $new_user_email,
//			'user_registered' => date( 'Y-m-d H:i:s' ),
//			'role'            => $user_role,
//		));
//		if ( ! is_wp_error( $new_user_id ) && is_int( $new_user_id ) ) {


		$invite_register_page = buddyforms_get_form_field_by_slug( $_POST['form_slug'], 'collaborative-publishing' );

		$activation_link = '<a href="' . get_permalink( $invite_register_page['invite_register_page'] ) . '?user_email=' . $new_user_email . '">Register now!</a>';

//			$code            = sha1( $new_user_id . time() );
//
//			$activation_link = add_query_arg( array(
//				'key'       => $code,
//				'user'      => $new_user_id,
//				'source'    => 'invitation',
//				'_wpnonce'  => buddyforms_create_nonce( 'buddyform_activate_user_link', $user_id )
//			), $activation_page );


		// Now let us send the mail
		$subject = __( 'You got an invite to register and edit' );

		$mail_to = $new_user_email;

		$emailBody = $_POST['bf_invite_mail_message'];

		$emailBody .= $activation_link;

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


//	if ( ! $result ) {
//		echo __( 'There has been an error sending the message!', 'buddyforms' );
//	}

    echo $new_user_email_html;

	die();
}


add_action( 'wp_ajax_bf_load_users', 'buddyforms_ajax_load_users' );
add_action( 'wp_ajax_nopriv_bf_load_users', 'buddyforms_ajax_load_users' );
function buddyforms_ajax_load_users() {
	if ( ! ( is_array( $_POST ) && defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return;
	}

	if ( ! isset( $_POST['action'] ) || wp_verify_nonce( $_POST['nonce'], 'bf_user_loading' ) === false ) {
		wp_die();
	}

	$args = array(//	'search_columns' => array( 'user_login', 'user_email' )
	);

//	$form_slug = '';
//	if ( empty( $_POST['form_slug'] ) ) {
//		wp_send_json_error( new WP_Error( 'invalid_form_slug', 'Invalid Form Slug' ), 500 );
//	} else {
//		$form_slug = sanitize_title( $_POST['form_slug'] );
//	}

	if ( ! empty( $_POST['search'] ) ) {
		$args['search'] = $_POST['search']; // sanitize_title_for_query( $_POST['search'] );
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
