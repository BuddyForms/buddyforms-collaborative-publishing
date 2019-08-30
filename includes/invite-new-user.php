<?php

add_action( 'buddyforms_post_edit_meta_box_select_form', 'buddyforms_cbublishing_invite_new_editor' );

function buddyforms_cbublishing_invite_new_editor( $post_id ) {
	global $post;
	add_thickbox();

	?>

    <script>
        jQuery(document).ready(function () {
            jQuery(document).on("click", '#buddyforms_invite_new_user_as_editor', function (evt) {


                var user_invite_email_select = jQuery('#user_invite_email_select').val();

                alert(user_invite_email_select);


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
                var user_email = jQuery('#buddyforms_invite_new_user_as_editor').attr("data-user_email");

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        "action": "buddyforms_invite_new_user_as_editor",
                        "post_id": post_id,
                        "bf_invite_mail_to": bf_invite_mail_to,
                        "bf_invite_mail_subject": bf_invite_mail_subject,
                        "bf_invite_mail_message": bf_invite_mail_message
                    },
                    success: function (data) {

                        if (data) {
                            alert(data);
                        } else {
                            window.top.location.reload();
                        }
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
    <a id="buddyforms_invite" href="#TB_inline?width=800&height=600&inlineId=buddyforms_invite_modal"
       title="Invite to Edit" class="thickbox button"><?php _e( 'Invite People as Editors', 'buddyforms' ) ?></a>

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

            $args = array_merge( $args, Array( 'multiple'          => 'multiple',
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
			$maximumSelectionLength  = '';
			$minimumInputLength      = '';
			$ajax_options            = '';
			$is_ajax                 = true;

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
                                             'form_slug: "user_invite_email_select", ' .
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
							    alert("biutte");
				                     
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



			$form2->addElement( new Element_HTML( 'da is eine form' ) );

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

            <textarea id="bf_invite_mail_message">Hi, Your got an invite to edit this post. [edit_post_url]</textarea>

            <br>
            <a id="buddyforms_invite_new_user_as_editor"
               data-post_id="<?php echo $post_id ?>"
               href="#" class="button">Sent Invite</a>
        </div>
    </div>

	<?php

}

add_action( 'wp_ajax_buddyforms_invite_new_user_as_editor', 'buddyforms_invite_new_user_as_editor' );
function buddyforms_invite_new_user_as_editor() {


	if ( ! isset( $_POST['post_id'] ) ) {
		echo __( 'There has been an error sending the message No post to edit is selected!', 'buddyforms' );
		die();

		return;
	}

	$post_id = $_POST['post_id'];

	$post       = get_post( $post_id );
	$post_title = $post->post_title;
	$postperma  = get_permalink( $post->ID );

	$user_info = get_userdata( $post->post_author );

	$usernameauth  = $user_info->user_login;
	$user_nicename = $user_info->user_nicename;
	$first_name    = $user_info->user_firstname;
	$last_name     = $user_info->user_lastname;

	$blog_title  = get_bloginfo( 'name' );
	$siteurl     = get_bloginfo( 'wpurl' );
	$siteurlhtml = "<a href='$siteurl' target='_blank' >$siteurl</a>";


	$subject = $_POST['bf_invite_mail_subject'];

	$mail_to   = $_POST['bf_invite_mail_to'];
	$emailBody = $_POST['bf_invite_mail_message'];

	$emailBody    = str_replace( '[user_login]', $usernameauth, $emailBody );
	$emailBody    = str_replace( '[first_name]', $first_name, $emailBody );
	$emailBody    = str_replace( '[last_name]', $last_name, $emailBody );
	$emailBody    = str_replace( '[published_post_link_plain]', $postperma, $emailBody );
	$postlinkhtml = "<a href='$postperma' target='_blank'>$postperma</a>";
	$emailBody    = str_replace( '[published_post_link_html]', $postlinkhtml, $emailBody );
	$emailBody    = str_replace( '[published_post_title]', $post_title, $emailBody );
	$emailBody    = str_replace( '[site_name]', $blog_title, $emailBody );
	$emailBody    = str_replace( '[site_url]', $siteurl, $emailBody );
	$emailBody    = str_replace( '[site_url_html]', $siteurlhtml, $emailBody );

	$emailBody = stripslashes( htmlspecialchars_decode( $emailBody ) );


	$from_email = get_option( 'admin_email' );


	$mailheaders = "MIME-Version: 1.0\n";
	$mailheaders .= "X-Priority: 1\n";
	$mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
	$mailheaders .= "From: " . $from_email . "<" . $from_email . ">" . "\r\n";

	$message = '<html><head></head><body>' . $emailBody . '</body></html>';

	$result = wp_mail( $mail_to, $subject, $message, $mailheaders );

	if ( ! $result ) {
		echo __( 'There has been an error sending the message!', 'buddyforms' );
	}

	die();
}













add_action( 'wp_ajax_bf_load_users', 'buddyforms_ajax_load_users' );
add_action( 'wp_ajax_nopriv_bf_load_users', 'buddyforms_ajax_load_users' );
function buddyforms_ajax_load_users(){
	if (! (is_array($_POST) && defined('DOING_AJAX') && DOING_AJAX)) {
		return;
	}

	if ( ! isset($_POST['action']) || wp_verify_nonce($_POST['nonce'], 'bf_user_loading') === false ) {
		wp_die();
	}

	$args = array(
		'search_columns' => array( 'user_login', 'user_email' )
	);

	$form_slug = '';
	if ( empty( $_POST['form_slug'] ) ) {
		wp_send_json_error( new WP_Error( 'invalid_form_slug', 'Invalid Form Slug' ), 500 );
	} else {
		$form_slug = sanitize_title( $_POST['form_slug'] );
	}

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
		    if( is_email( $_POST['search'] ) ){
			    $current       = new stdClass;
			    $current->id   = 'new-' . substr( md5( time() * rand() ), 0, 10 );
			    $current->text = $_POST['search'];
			    $result[]      = $current;
            }
        }

		$response->results = $result;
		wp_send_json( $response );
	}
}
