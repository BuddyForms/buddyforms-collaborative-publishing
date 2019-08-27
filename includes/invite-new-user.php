<?php

add_action( 'buddyforms_post_edit_meta_box_select_form', 'buddyforms_cbublishing_invite_new_editor' );

function buddyforms_cbublishing_invite_new_editor() {
	global $post;
	add_thickbox();
	?>



	<script>
        jQuery(document).ready(function () {
            jQuery(document).on("click", '#buddyforms_invite_new_user_as_editor', function (evt) {

                var bf_invite_mail_from = jQuery('#bf_invite_mail_from').val();
                var bf_invite_mail_subject = jQuery('#bf_invite_mail_subject').val();
                var bf_invite_mail_message = jQuery('#bf_invite_mail_message').val();

                if (bf_invite_mail_from == '') {
                    alert('Mail From is a required field');
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
                        "user_email": user_email,
                        "bf_invite_mail_from": bf_invite_mail_from,
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
	   title="Reject This Post" class="thickbox button">Reject this Post</a>

	<div id="buddyforms_message_history">
		<?php $bf_moderation_message_history = get_post_meta( $post->ID, '_bf_moderation_message_history', true ); ?>
		<ul>
			<?php
			if ( is_array( $bf_moderation_message_history ) ) {
				foreach ( $bf_moderation_message_history as $key => $message ) {
					echo '<li>' . stripslashes( substr( $message, 0, 130 ) ) . '</li>';
				}
			}

			?>
		</ul>
	</div>

	<div id="buddyforms_invite_modal" style="display:none;">
		<div id="buddyforms_invite_wrap">

			<p>Message will be sent to the
				Author <?php echo get_the_author_meta( 'user_nicename', $post->post_author ); ?> to the mail
				address <?php echo get_the_author_meta( 'user_email', $post->post_author ); ?></p>

			<table class="form-table">
				<tbody>
				<tr>
					<th><label for="bf_invite_mail_from">Mail From</label></th>
					<td><input id="bf_invite_mail_from" type="text"
					           value="<?php echo get_bloginfo( 'admin_email' ); ?>"></td>
				</tr>
				<tr>
					<th><label for="bf_invite_mail_subject">Mail Subject</label></th>
					<td><input id="bf_invite_mail_subject" type="text" value="Your Submission has been inviteed"></td>
				</tr>
				</tbody>
			</table>

			<?php

			wp_editor( 'Hi [user_login], Your submitted post [published_post_title] has ben inviteed.', 'bf_invite_mail_message', array(
				'media_buttons' => false,
				'teeny'         => false,
				'textarea_rows' => '10',
			) );


			?>
			<br>
			<a id="buddyforms_invite_new_user_as_editor"
			   data-post_id="<?php echo $post->ID ?>"
			   data-user_email="<?php echo get_the_author_meta( 'user_email', $post->post_author ) ?>"
			   href="#" class="button">Sent Message and Set post status to edit-draft</a>

			<h3>User Shortcodes</h3>
			<ul>
				<li>[user_login] Username</li>
				<li>[first_name] user first name</li>
				<li>[last_name] user last name</li>
			</ul>
			<h3>Published Post Shortcodes</h3>
			<ul>
				<li>[published_post_link_html] the published post link in html</li>
				<li>[published_post_link_plain] the published post link in plain</li>
				<li>[published_post_title] the published post title</li>
			</ul>
			<h3>Site Shortcodes</h3>
			<ul>
				<li>[site_name] the site name</li>
				<li>[site_url] the site url</li>
				<li>[site_url_html] the site url in html</li>
			</ul>

		</div>
	</div>

	<?php

}

add_action( 'wp_ajax_buddyforms_invite_new_user_as_editor', 'buddyforms_invite_new_user_as_editor' );
function buddyforms_invite_new_user_as_editor() {


	if ( ! isset( $_POST['post_id'] ) ) {
		echo __( 'There has been an error sending the message!', 'buddyforms' );
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


	$mail_to = $_POST['user_email'];
	$subject = $_POST['bf_invite_mail_subject'];

	$from_email = $_POST['bf_invite_mail_from'];
	$emailBody  = $_POST['bf_invite_mail_message'];

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

	$mailheaders = "MIME-Version: 1.0\n";
	$mailheaders .= "X-Priority: 1\n";
	$mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
	$mailheaders .= "Content-Transfer-Encoding: 7bit\n\n";
	$mailheaders .= "From: " . $from_email . "<" . $from_email . ">" . "\r\n";

	$message = '<html><head></head><body>' . $emailBody . '</body></html>';

	$result = wp_mail( $mail_to, $subject, $message, $mailheaders );

	$result_update = wp_update_post( array(
		'ID'          => $post_id,
		'post_status' => 'edit-draft',
	) );

	if ( ! $result ) {
		echo __( 'There has been an error sending the message!', 'buddyforms' );
	}

	if ( is_wp_error( $result_update ) ) {
		echo __( 'There has been an error changing the post status!', 'buddyforms' );
	}

	$bf_moderation_message_history = get_post_meta( $post_id, '_bf_moderation_message_history', true );

	$bf_moderation_message_history[] = the_date( 'l, F j, Y' ) . $emailBody;
	update_post_meta( $post_id, '_bf_moderation_message_history', $bf_moderation_message_history );

	die();
}
