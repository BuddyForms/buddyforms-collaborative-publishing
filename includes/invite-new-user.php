<?php

add_action( 'buddyforms_post_edit_meta_box_select_form', 'buddyforms_cbublishing_invite_new_editor' );

function buddyforms_cbublishing_invite_new_editor( $post_id ) {
	global $post;
	add_thickbox();

	?>

	<script>
        jQuery(document).ready(function () {
            jQuery(document).on("click", '#buddyforms_invite_new_user_as_editor', function (evt) {

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
	   title="Invite to Edit" class="thickbox button"><?php _e('Invite People as Editors', 'buddyforms') ?></a>

	<div id="buddyforms_invite_modal" style="display:none;">
		<div id="buddyforms_invite_wrap">

			<table class="form-table">
				<tbody>
				<tr>
					<th><label for="bf_invite_mail_to">Mail to:</label></th>
					<td><input id="bf_invite_mail_to" type="email" value=""></td>
				</tr>
				<tr>
					<th><label for="bf_invite_mail_subject">Mail Subject</label></th>
					<td><input id="bf_invite_mail_subject" type="text" value="<?php echo __('You got Invited to edit this post'); ?>"></td>
				</tr>
				</tbody>
			</table>

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

	$mail_to = $_POST['bf_invite_mail_to'];
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


	$from_email = get_option('admin_email');


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
