var buddyformsCollaborativePublishingInstance = {
	bfIsEmail: function (email) {
		if (!email || (email && email.length === 0)) {
			return false;
		}
		var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return regex.test(email.trim());
	},
	bfInviteNewEditors: function () {
		var currentPopup = jQuery('#TB_ajaxContent');
		if (currentPopup && currentPopup.length === 0) {
			console.log('something went wrong, tickbox is not present, please contact the admin');
			return false;
		}

		var user_invite_email_select = currentPopup.find('#user_invite_email_select').val();
		var bf_invite_mail_message = currentPopup.find('#user_invite_email_message').val();
		var invalidSelectedUserStr = buddyformsCollaborativePublishingObj.language.invalid_invite_editors || 'You need to select a valid user or type a valid email.';
		var invalidMessageStr = buddyformsCollaborativePublishingObj.language.invalid_invite_message || 'Message is a required field.';
		var popupLoading = buddyformsCollaborativePublishingObj.language.popup_loading || 'Loading...';
		if (!user_invite_email_select) {
			alert(invalidSelectedUserStr);
			return false;
		}
		if (bf_invite_mail_message === '') {
			alert(invalidMessageStr);
			return false;
		}

		var btnInviteEditor = currentPopup.find('#buddyforms_invite_new_user_as_editor');
		var post_id = btnInviteEditor.attr("data-post_id");
		var form_slug = btnInviteEditor.attr("data-form_slug");

		btnInviteEditor.attr('disabled', true);
		var actionButtonOriginalText = btnInviteEditor.text();
		btnInviteEditor.text(popupLoading);

		jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data: {
				"action": "buddyforms_invite_new_user_as_editor",
				"post_id": post_id,
				"form_slug": form_slug,
				"user_invite_email_select": user_invite_email_select,
				"user_invite_email_message": bf_invite_mail_message
			},
			success: function (data) {
				console.log(data);
				if (data['new_user_email_html']) {
					jQuery('#buddyforms_panding_invites_list').html(data['new_user_email_html']);
				}
				var selected = jQuery('#col-lab-editors').select2('data');
				if (data['old_user_emails'].length > 0) {
					jQuery.each(data['old_user_emails'], function (index, element) {
						console.log(index + ' - ' + element);
						var data2 = {
							id: index,
							text: element
						};
						// Set the value, creating a new option if necessary
						if (jQuery('#col-lab-editors').find("option[value='" + data2.id + "']").length) {
							selected.push(data2.id);
						} else {
							// Create a DOM Option and pre-select by default
							var newOption = new Option(data2.text, data2.id, true, true);
							// Append it to the select
							jQuery('#col-lab-editors').append(newOption).trigger('change');
						}
					});
					jQuery('#col-lab-editors').val(selected).trigger('change');
				}
				// jQuery('#buddyforms_invite_wrap').html('<p>Invite send successfully</p>');
				tb_remove();
				btnInviteEditor.text(actionButtonOriginalText);
			},
			error: function (request, status, error) {
				btnInviteEditor.text(actionButtonOriginalText);
				btnInviteEditor.removeAttr('disabled');
				alert(request.responseText);
			}
		});
	},
	bfBecomeAnEditor: function () {
		var post_id = jQuery(this).attr('id');
		var editRequestInProcessStr = buddyformsCollaborativePublishingObj.language.edit_request_in_process || 'Edit Request in Process.';
		jQuery.ajax({
			type: 'POST',
			url: buddyformsCollaborativePublishingObj.ajax,
			data: {
				"action": "buddyforms_ask_to_become_an_editor",
				"post_id": post_id
			},
			success: function (data) {
				if (isNaN(data)) {
				} else {
					// var id = "#bf_post_li_";
					// var li = id + data;
					// li = li.replace(/\s+/g, '');
					// jQuery(li).remove();
					jQuery('#' + data).replaceWith("<p>" + editRequestInProcessStr + "</p>");
				}
			},
			error: function (request) {
				alert(request.responseText);
			}
		});
		return false;
	},
	bfRemoveAsEditor: function () {
		var post_id = jQuery(this).attr('id');
		var removeEditorStr = buddyformsCollaborativePublishingObj.language.remove_as_editor || 'Are you sure to remove as Editor?';
		if (confirm(removeEditorStr)) {
			jQuery.ajax({
				type: 'POST',
				url: buddyformsCollaborativePublishingObj.ajax,
				data: {
					"action": "buddyforms_ajax_delete_post",
					"post_id": post_id
				},
				success: function (data) {
					if (isNaN(data)) {
						alert(data);
					} else {
						var id = "#bf_post_li_";
						var li = id + data;
						li = li.replace(/\s+/g, '');
						jQuery(li).remove();
					}
				},
				error: function (request) {
					alert(request.responseText);
				}
			});
		} else {
			return false;
		}
		return false;
	},
	bfDeletePost: function () {
		var post_id = jQuery(this).attr('id');
		var removePostStr = buddyformsCollaborativePublishingObj.language.remove_post || 'Are you sure to delete the Post?';
		if (confirm(removePostStr)) {
			jQuery.ajax({
				type: 'POST',
				url: buddyformsCollaborativePublishingObj.ajax,
				data: {
					"action": "buddyforms_ajax_delete_post",
					"post_id": post_id
				},
				success: function (data) {
					if (isNaN(data)) {
						alert(data);
					} else {
						var id = "#bf_post_li_";
						var li = id + data;
						li = li.replace(/\s+/g, '');
						jQuery(li).remove();
					}
				},
				error: function (request) {
					alert(request.responseText);
				}
			});
		} else {
			return false;
		}
		return false;
	},
	init: function () {
		if (buddyformsCollaborativePublishingObj) {
			jQuery(document.body).on('click', '.bf_cpublishing_delete_post', buddyformsCollaborativePublishingInstance.bfDeletePost);
			jQuery(document.body).on('click', '.bf_remove_as_editor', buddyformsCollaborativePublishingInstance.bfRemoveAsEditor);
			jQuery(document.body).on('click', '.bf_become_an_editor', buddyformsCollaborativePublishingInstance.bfBecomeAnEditor);
			jQuery(document.body).on('click', '#buddyforms_invite_new_user_as_editor', buddyformsCollaborativePublishingInstance.bfInviteNewEditors);
		}
	}
};

jQuery(document).ready(function () {
	buddyformsCollaborativePublishingInstance.init();
});
