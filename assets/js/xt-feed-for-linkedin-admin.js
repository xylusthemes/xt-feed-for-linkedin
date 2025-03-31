(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	jQuery(document).ready(function(){
		
		$(".xtfefoli_sp_keyword").on("click", function() {
			var keyword = $(this).text();
			var textarea = $("#xtfefoli_custom-share-message");
			var currentText = textarea.val();
			textarea.val(currentText + " " + keyword);
		});

		$(document).on("change", ".lf-status-toggle", function () {
			var isChecked = $(this).prop("checked");
			var userSub   = $(this).data("sub");
			var userEmail = $(this).data("email");
		
			$.ajax({
				type: "POST",
				url: xtfefoli_ajax.ajax_url,
				data: {
					action: "xtfefoli_update_user_status",
					user_sub: userSub,
					user_email: userEmail,
					is_active: isChecked ? "yes" : "no",
					xtfefoli_security: xtfefoli_ajax.update_status_nonce,
				},
				success: function (response) {
					if ( response.success ) {			
						if ( isChecked ){
							$(".lf-status-toggle").prop("checked", false);
							$(".lf-status-toggle").each(function () {
								if ($(this).data("sub") == userSub) {
									$(this).prop("checked", true);
								}
							});
						}
					} else {
						$(this).prop("checked", !isChecked);
					}

					var lfsuccessHtml = `<div class="notice notice-success lf-notice is-dismissible"><p>${response.data.message}</p></div>`;
					$(".ajax_xtfefoli_notice").html( lfsuccessHtml );
				},
				error: function () {			
					// Revert checkbox state on error
					$this.prop("checked", !isChecked);
		
					var lferrorHtml = `<div class="notice notice-error lf-notice is-dismissible"><p>An error occurred. Please try again.</p></div>`;
            		$(".ajax_xtfefoli_notice").html(lferrorHtml);
				}
			});
		});
		
		// User Deletion Code (Ensure it uses the same localized object)
		$(document).on("click", ".lf-delete-btn", function () {
			var userSub = $(this).data("sub");
			var userEmail = $(this).data("email");
			if (confirm("Are you sure you want to delete this user?")) {
				$.ajax({
					type: "POST",
					url: xtfefoli_ajax.ajax_url,
					data: {
						action: "xtfefoli_delete_user",
						user_sub: userSub,
						user_email: userEmail,
						xtfefoli_security: xtfefoli_ajax.delete_user_nonce,
					},
					success: function (response) {
						if (response.success) {
							var updatedUrl = xtfefoli_ajax.plugin_page_url + "&message="+response.data.message+"&status=1";
							window.location.replace(updatedUrl);
						} else {
							alert("Error: " + response.data.message);
						}
					},
					error: function () {
						var lferrorHtml = `<div class="notice notice-error lf-notice is-dismissible"><p>An error occurred. Please try again.</p></div>`;
            			$(".ajax_xtfefoli_notice").html(lferrorHtml);
					}
				});
			}
		});
	});

})( jQuery );


