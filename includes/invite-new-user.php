<?php
function buddyforms_cbublishing_invite_new_editor( $post_id, $form_slug ) {
	global $post, $buddyforms;
	$description     = '';
	$new_user_emails = get_post_meta( $post_id, 'buddyforms_new_user_emails', true );
	?>
	<p>
		<a id="buddyforms_invite" href="#TB_inline?width=800&height=auto&inlineId=buddyforms_invite_modal" title="" class="thickbox button"><?php _e( 'Invite People as Editors', 'buddyforms-collaborative-publishing' ) ?></a>
	</p>

	<div id="buddyforms_panding_invites">
		<p><strong><?php echo __( 'Pending Invites', 'buddyforms-collaborative-publishing' ) ?></strong></p>
		<div>
			<ul id="buddyforms_pending_invites_list">
				<?php
				if ( ! empty( $new_user_emails ) ) {
					foreach ( $new_user_emails as $new_user_email ) {
						echo sprintf( "<li>%s&nbsp;<span class='bf-collaborative-remove-email-invite-container'><a href='#' data-post='%s' data-target-email='%s' class='bf-collaborative-remove-email-invite'>%s</a></span></li>", esc_attr( $new_user_email ), intval( $post_id ), esc_attr( $new_user_email ), __( 'Remove', 'buddyforms-collaborative-publishing' ) );
					}
				}
				?>
			</ul>
		</div>
	</div>

	<div id="buddyforms_invite_modal" style="display:none;">
		<div id="buddyforms_invite_wrap">
			<?php
			//Load collaborative field needed options
			$selected_roles = '';
			$invite_message = __( 'You got an invite to edit a post', 'buddyforms-collaborative-publishing' );
			$field_options  = buddyforms_get_form_field_by( $form_slug, 'collaborative-publishing', 'type' );
			if ( ! empty( $field_options ) ) {
				$invite_message = ! empty( $field_options['invite_message'] ) ? $field_options['invite_message'] : __( 'You got an invite to edit a post', 'buddyforms-collaborative-publishing' );
				$selected_roles = ! empty( $field_options['cpublishing_editors'] ) ? $field_options['cpublishing_editors'] : '';
			}

			// Create the form object
			$form2 = new Form( "buddyforms_invite_new_user" );

			// Set the form attribute
			$form2->configure( array(
				"prevent" => array( "bootstrap", "jQuery", "focus" ),
				'method'  => 'post'
			) );

			$element_attr = array( 'class' => ' bf-select2-user_invite_email_select' );
			$label        = __( 'Add email Address you want to invite', 'buddyforms-collaborative-publishing' );

			$element_attr['class'] = $element_attr['class'] . ' bf-select2-user_invite_email_select';
			$element_attr['value'] = get_post_meta( $post_id, 'buddyforms_moderators', true );

			BuddyFormsAssets::load_select2_assets();

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
				'role__in'      => $selected_roles
			);

			$placeholder = __( 'Search', 'buddyforms-collaborative-publishing' );

			$args = array_merge( $args, Array(
				'multiple'          => 'multiple',
				'placeholder'       => $placeholder,
				'show_option_none'  => '',
				'option_none_value' => ''
			) );


			$args = apply_filters( 'buddyforms_collaborative_publishing_wp_dropdown_users_args', $args, $post_id );

			$dropdown = wp_dropdown_users( $args );

			// Multiple
			$dropdown = str_replace( 'id=', 'multiple="multiple" id=', $dropdown );

			// Required
			//$dropdown = str_replace( 'id=', 'required id=', $dropdown );

			$dropdown = str_replace( 'id=', 'data-form="user_invite_email_select" id=', $dropdown );
			$dropdown = str_replace( 'id=', 'data-placeholder="' . $placeholder . '" id=', $dropdown );
			$dropdown = str_replace( 'id=', 'style="width:100%;" id=', $dropdown );

			//$required = $form2->renderRequired();

			$minimumResultsForSearch = '';
			//$tags                    = 'tags: true,';
			$maximumSelectionLength = '';
			$minimumInputLength     = '';
			$ajax_options           = '';
			$is_ajax                = true;

			if ( $is_ajax ) {
				$ajax_options .= $minimumInputLength;
				$ajax_options .= 'ajax:{ ' .
				                 'success:function(data) {' .
				                 'console.log(data);' .
				                 ' }, ' .
				                 'url: "' . admin_url( 'admin-ajax.php' ) . '", ' .
				                 'delay: 250, ' .
				                 'dataType: "json", ' .
				                 'cache: true, ' .
				                 'method : "POST", ' .#
				                 'data: function (params) { ' .
				                 'var query = { ' .
				                 'search: params.term, ' .
				                 'type: "public", ' .
				                 'action: "bf_collaborative_publishing_load_users", ' .
				                 'form_slug: "' . $form_slug . '", ' .
				                 'nonce: "' . wp_create_nonce( 'bf_collaborative_publishing_load_users' . __DIR__ ) . '", ' .
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
                                     dropdownCssClass: "buddyforms-dropdown",
							        tokenSeparators: [\',\']
							    });
						    });
						</script>
						<div class="bf_field_group">
	                        <label for="editpost-element-user_invite_email_select"><strong>' . __( 'Invite Users', 'buddyforms-collaborative-publishing' ) . '</strong></label>
	                        <div class="bf_inputs bf-input">' . $dropdown . '</div>
		                	<span class="help-inline">' . $description . '</span>
		                </div>';


			$form2->addElement( new Element_HTML( $dropdown ) );

			$form2->addElement( new Element_Textarea( __( 'Invite Message Text', 'buddyforms-collaborative-publishing' ), 'user_invite_email_message', array(
				'value'     => $invite_message,
				'class'     => 'collaburative-publishiing-message',
				'rows'      => '15',
				'shortDesc' => apply_filters( 'buddyforms_collaborative_publishing_message_description', '' ),
			) ) );

			$form2->render();

			?>

			<br>
			<button id="buddyforms_invite_new_user_as_editor" data-post_id="<?php echo $post_id ?>" data-form_slug="<?php echo $form_slug ?>" href="#" class="button"><?php echo __( 'Sent Invite', 'buddyforms-collaborative-publishing' ) ?></button>
		</div>
	</div>
	<?php
}

