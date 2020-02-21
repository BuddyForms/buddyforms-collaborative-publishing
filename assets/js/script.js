var buddyformsCollaborativePublishingInstance = {
	bfIsEmail: function (email) {
		if (!email || (email && email.length === 0)) {
			return false;
		}
		var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return regex.test(email.trim());
	},
	bfRemoveEmailInvitation: function (event) {
		event.preventDefault();
		var element = jQuery(this);
		var spanContainer = jQuery(this).parent();
		var container = element.closest('li');
		var targetEmail = element.attr('data-target-email');
		var targetPost = element.attr('data-post');
		if (targetEmail && targetPost) {
			spanContainer.html('');
			spanContainer.text('Loading...');
			jQuery.ajax({
			type: 'POST',
			dataType: "json",
			url: ajaxurl,
			data: {
				"action": "buddyforms_collaborative_remove_email_invitation",
				"email": targetEmail,
				"nonce": buddyformsCollaborativePublishingObj.nonce,
				"post_id": targetPost,
			},
			success: function (data) {
				if (data) {
					container.hide('fast', function(){ container.remove(); });
				}
			},
			error: function (request, status, error) {
				console.log(request.responseText);
				spanContainer.text('Error :(');
			}
		});
		}
	},
	bfInviteNewEditors: function (event) {
		event.preventDefault();
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
				"nonce": buddyformsCollaborativePublishingObj.nonce,
				"form_slug": form_slug,
				"user_invite_email_select": user_invite_email_select,
				"user_invite_email_message": bf_invite_mail_message
			},
			success: function (data) {
				if (data) {
					if (data['new_user_email_html']) {
						jQuery('#buddyforms_pending_invites_list').html(data['new_user_email_html']);
					}
					var coLabEditorSelect = jQuery('#col-lab-editors');
					if (coLabEditorSelect && coLabEditorSelect.length > 0) {
						var selected = coLabEditorSelect.select2('data');
						var newSelected = [];
						if (data['old_user_emails']) {
							if (selected.length > 0) {
								jQuery.each(selected, function (index, element) {
									newSelected.push(element.id);
								});
							}
							jQuery.each(data['old_user_emails'], function (index, element) {
								var data2 = {
									id: index,
									text: element
								};
								// Set the value, creating a new option if necessary
								if (coLabEditorSelect.find("option[value='" + data2.id + "']").length) {
									newSelected.push(data2.id);
								} else {
									// Create a DOM Option and pre-select by default
									var newOption = new Option(data2.text, data2.id, true, true);
									// Append it to the select
									coLabEditorSelect.append(newOption).trigger('change');
								}
							});
							coLabEditorSelect.val(newSelected).trigger('change');
						}
					}
				}
				tb_remove();
				btnInviteEditor.text(actionButtonOriginalText);
				btnInviteEditor.removeAttr('disabled');
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
				"post_id": post_id,
				"nonce": buddyformsCollaborativePublishingObj.nonce,
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
					"post_id": post_id,
					"nonce": buddyformsCollaborativePublishingObj.nonce,
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
					"post_id": post_id,
					"nonce": buddyformsCollaborativePublishingObj.nonce,
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
	bfFieldName: function (fieldName, [formSlug, fieldId, form]) {
		var validNames = ['buddyforms_editors', 'collaborative-publishing', 'buddyforms_teams', 'user_invite_email_select'];
		if (fieldName && formSlug && buddyformsGlobal && buddyformsGlobal[formSlug] && buddyformsGlobal[formSlug].form_fields) {
			if (validNames.includes(fieldName)) {
				return 'Collaborative Publishing';
			}
		}

		return fieldName;
	},
	init: function () {
		if (buddyformsCollaborativePublishingObj && buddyformsCollaborativePublishingObj.nonce && buddyformsCollaborativePublishingObj.ajax) {
			jQuery(document.body).on('click', '.bf_cpublishing_delete_post', buddyformsCollaborativePublishingInstance.bfDeletePost);
			jQuery(document.body).on('click', '.bf_remove_as_editor', buddyformsCollaborativePublishingInstance.bfRemoveAsEditor);
			jQuery(document.body).on('click', '.bf_become_an_editor', buddyformsCollaborativePublishingInstance.bfBecomeAnEditor);
			jQuery(document.body).on('click', '#buddyforms_invite_new_user_as_editor', buddyformsCollaborativePublishingInstance.bfInviteNewEditors);
			jQuery(document.body).on('click', '.bf-collaborative-remove-email-invite', buddyformsCollaborativePublishingInstance.bfRemoveEmailInvitation);
		}
	}
};

jQuery(document).ready(function () {
	buddyformsCollaborativePublishingInstance.init();
});
