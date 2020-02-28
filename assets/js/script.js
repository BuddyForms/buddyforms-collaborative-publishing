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
						container.hide('fast', function () {
							container.remove();
						});
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
		var popupLoading = buddyformsCollaborativePublishingObj.language.popup_loading || 'Loading...';
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
	//TODO pending to review if needed
	bfBecomeAnEditor: function () {
		var post_id = jQuery(this).attr('id');
		var form_slug = jQuery(this).attr("data-form_slug");
		var editRequestInProcessStr = buddyformsCollaborativePublishingObj.language.edit_request_in_process || 'Edit Request in Process.';
		jQuery.ajax({
			type: 'POST',
			url: buddyformsCollaborativePublishingObj.ajax,
			data: {
				"action": "buddyforms_ask_to_become_an_editor",
				"post_id": post_id,
				"form_slug": form_slug,
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
	bfRemoveAsEditor: function (event) {
		event.preventDefault();
		var element = jQuery(this);
		if (element.hasClass('clicked')) {
			console.log('already clicked');
			return false;
		}
		var removeEditorStr = buddyformsCollaborativePublishingObj.language.remove_as_editor || 'Are you sure to remove as Editor?';
		if (confirm(removeEditorStr)) {
			element.addClass('clicked');
			var actionOriginalText = element.html();
			var popupLoading = buddyformsCollaborativePublishingObj.language.popup_loading || 'Loading...';
			element.text(popupLoading);
			var form_slug = element.attr("data-form_slug");
			var post_id = element.attr('id');
			jQuery.ajax({
				type: 'POST',
				dataType: "json",
				url: buddyformsCollaborativePublishingObj.ajax,
				data: {
					"action": "buddyforms_remove_as_editor",
					"post_id": post_id,
					"nonce": buddyformsCollaborativePublishingObj.nonce,
					"form_slug": form_slug,
				},
				success: function (data) {
					if (data && data.data && data.data.form_slug && data.data.post_id) {
						alert('Remove as editor successfully!');
						var postContainer = jQuery('.bf_posts_' + data.data.post_id);
						if (postContainer && postContainer.length > 0) {
							postContainer.hide('fast', function () {
								postContainer.remove();
							});
						}
					}
				},
				error: function (request, status, error) {
					alert(request.responseText);
				},
				complete: function () {
					tb_remove();
					element.html(actionOriginalText);
					element.removeClass('clicked');
				}
			});
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
	bfSendRemoveMessage: function (event) {
		event.preventDefault();
		var currentPopup = jQuery('#TB_ajaxContent');
		if (currentPopup && currentPopup.length === 0) {
			console.log('something went wrong, tickbox is not present, please contact the admin');
			return false;
		}

		var element = jQuery(this);
		var post_id = element.attr("data-post_id");
		var form_slug = element.attr("data-form_slug");

		if (!post_id || !form_slug) {
			console.log('something went wrong, parameters are not present, please contact the admin');
			return false;
		}

		var post_delete_email_subject = currentPopup.find('#post_delete_email_subject_' + post_id).val();
		var post_delete_email_message = currentPopup.find('#post_delete_email_message_' + post_id).val();
		var removeRequestSuccessfully = buddyformsCollaborativePublishingObj.language.remove_request_successfully || 'Delete Request has been send successfully.';
		var invalidSubjectStr = buddyformsCollaborativePublishingObj.language.invalid_remove_request_subject || 'Subject is a required field.';
		var invalidMessageStr = buddyformsCollaborativePublishingObj.language.invalid_remove_request_message || 'Message is a required field.';
		if (post_delete_email_subject === '') {
			alert(invalidSubjectStr);
			return false;
		}
		if (post_delete_email_message === '') {
			alert(invalidMessageStr);
			return false;
		}

		element.attr('disabled', true);
		var actionButtonOriginalText = element.text();
		var popupLoading = buddyformsCollaborativePublishingObj.language.popup_loading || 'Loading...';
		element.text(popupLoading);

		jQuery.ajax({
			type: 'POST',
			url: buddyformsCollaborativePublishingObj.ajax,
			data: {
				"action": "buddyforms_editor_remove_request",
				"post_id": post_id,
				"nonce": buddyformsCollaborativePublishingObj.nonce,
				"form_slug": form_slug,
				"remove_request_email_subject": post_delete_email_subject,
				"remove_request_email_message": post_delete_email_message
			},
			success: function (data) {
				if (data.data && data.data.form_slug && data.data.post_id) {
					alert(removeRequestSuccessfully);
				}
				tb_remove();
				element.text(actionButtonOriginalText);
				element.removeAttr('disabled');
			},
			error: function (request, status, error) {
				element.text(actionButtonOriginalText);
				element.removeAttr('disabled');
				alert(request.responseText);
			}
		});
	},
	init: function () {
		if (buddyformsCollaborativePublishingObj && buddyformsCollaborativePublishingObj.nonce && buddyformsCollaborativePublishingObj.ajax) {
			jQuery(document.body).on('click', '.bf_cpublishing_delete_post', buddyformsCollaborativePublishingInstance.bfDeletePost);
			jQuery(document.body).on('click', '.bf_remove_as_editor', buddyformsCollaborativePublishingInstance.bfRemoveAsEditor);
			jQuery(document.body).on('click', '.bf_become_an_editor', buddyformsCollaborativePublishingInstance.bfBecomeAnEditor);
			jQuery(document.body).on('click', '#buddyforms_invite_new_user_as_editor', buddyformsCollaborativePublishingInstance.bfInviteNewEditors);
			jQuery(document.body).on('click', '.bf-collaborative-remove-email-invite', buddyformsCollaborativePublishingInstance.bfRemoveEmailInvitation);
			jQuery(document.body).on('click', '.buddyforms_send_delete_request', buddyformsCollaborativePublishingInstance.bfSendRemoveMessage);
		}
	}
};

jQuery(document).ready(function () {
	buddyformsCollaborativePublishingInstance.init();
});
