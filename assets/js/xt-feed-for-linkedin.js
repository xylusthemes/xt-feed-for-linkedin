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
		
		$('#xtfefoli_dont_share_post_on_linkedin').on('change', function() {
			$('.xtfefoli_inside').toggle(!this.checked);
		}).trigger('change');

        $('#xtfefoli_share_cpt_button').on('click', function (e) {
            e.preventDefault();
    
            let postID       = $('#post_ID').val(); // Get post ID
            let shareMessage = $('#xtfefoli_custom-share-message').val();
            let nonce        = $('#xtfefoli_linkedin_feedpress_meta_box_nonce').val();
            let xtfefoli_post_type = $('#xtfefoli_post_type').val();
    
            let shareAccount = $(this).closest('.xtfefoli_inside').find('.xtfefoli-share-account').val() || '';
    
            if (!shareMessage.trim()) {
                $('#if_meta_box_notice').text('Please enter a message before sharing.').css('color', 'red');
                return;
            }
    
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'xtfefoli_share_to_linkedin',
                    post_id: postID,
                    share_message: shareMessage,
                    xtfefoli_post_type: xtfefoli_post_type,
                    share_account: shareAccount,
                    xtfefoli_share_security: nonce
                },
                beforeSend: function () {
                    var lfsrHtml = `<div class="notice notice-primary is-dismissible"><p>Sharing...</p></div>`;
                    $("#if_meta_box_notice").html(lfsrHtml);
                },
                success: function (response) {
                    if (response.success) {
                        var lfscHtml = `<div class="notice notice-success is-dismissible"><p>${response.data.message || response.data}</p></div>`;
                        $("#if_meta_box_notice").html(lfscHtml);
                        
                        if (response.data.history_html) {
                            if ($('#xtfefoli_shared_history_container').length === 0) {
                                $('#xtfefoli_history_wrapper').html('<div id="xtfefoli_shared_history_container" style="margin-top: 15px;"><strong>Post Shared History:</strong></div>');
                            }
                            $('#xtfefoli_shared_history_container').append(response.data.history_html);
                        }
                    } else {
                        var lferrorHtml = `<div class="notice notice-error is-dismissible"><p>${response.data}</p></div>`;
                        $("#if_meta_box_notice").html(lferrorHtml);
                    }
                
                    // Remove the notice after 3 seconds
                    setTimeout(function () {
                        $("#if_meta_box_notice").fadeOut("slow", function () {
                            $(this).html("").show(); // Clear content and keep the div visible
                        });
                    }, 3000);
                },
                error: function () {
                    var lferrorHtml = `<div class="notice notice-error is-dismissible"><p>An error occurred. Please try again.</p></div>`;
                    $("#if_meta_box_notice").html(lferrorHtml);
                
                    // Remove the notice after 3 seconds
                    setTimeout(function () {
                        $("#if_meta_box_notice").fadeOut("slow", function () {
                            $(this).html("").show();
                        });
                    }, 3000);
                }
            });
        });

        $('.xtfefoli_share_cpt_button_lt').on('click', function (e) {
            e.preventDefault();
    
            let button = $(this);
            let postID = button.data('post_id');
            let xtfefoli_post_type = button.data('post_type');
            let nonce = $('#xtfefoli_linkedin_feedpress_lt_nonce').val();
            let noticeBox = button.closest('.xtfefoli-share-column-container').find('.if_lt_notice'); // Get notice box for this row
            let shareAccount = button.closest('.xtfefoli-share-column-container').find('.xtfefoli-share-account').val() || '';
    
            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: {
                    action: 'xtfefoli_share_to_linkedin_lt',
                    post_id: postID,
                    xtfefoli_post_type: xtfefoli_post_type,
                    share_account: shareAccount,
                    xtfefoli_share_security: nonce
                },
                beforeSend: function () {
                    noticeBox.html('<div class="notice notice-primary is-dismissible"><p>Sharing...</p></div>');
                },
                success: function (response) {
                    if (response.success) {
                        let postText = $('.xtfefoli_post_' + postID );
                        postText.html('<span><strong>Shared</strong></span>');
                        noticeBox.html('<div class="notice notice-success is-dismissible"><p>Post shared successfully!</p></div>');
                    } else {
                        noticeBox.html(`<div class="notice notice-error is-dismissible"><p>${response.data}</p></div>`);
                    }
    
                    // Remove the notice after 3 seconds
                    setTimeout(function () {
                        noticeBox.fadeOut("slow", function () {
                            $(this).html("").show();
                        });
                    }, 3000);
                },
                error: function () {
                    noticeBox.html('<div class="notice notice-error is-dismissible"><p>An error occurred. Please try again.</p></div>');
    
                    // Remove the notice after 3 seconds
                    setTimeout(function () {
                        noticeBox.fadeOut("slow", function () {
                            $(this).html("").show();
                        });
                    }, 3000);
                }
            });
        });

        // Handle Post Edit Tracking (Disable Share Button until saved)
        function disableShareButton() {
            var btn = $('#xtfefoli_share_cpt_button');
            if (!btn.prop('disabled')) {
                btn.prop('disabled', true).css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
                if ($('#xtfefoli_save_notice').length === 0) {
                    btn.after('<div id="xtfefoli_save_notice" style="color: #d63638; margin-top: 8px; font-weight: 500;">Please save or publish the post first to apply your latest changes before sharing.</div>');
                }
            }
        }

        function enableShareButton() {
            if ($('#xtfefoli_auto_share_loader').length > 0) return;
            var btn = $('#xtfefoli_share_cpt_button');
            if (btn.prop('disabled')) {
                btn.prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
                $('#xtfefoli_save_notice').remove();
            }
        }

        // Gutenberg Support
        if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
            var wasSaving = false;
            wp.data.subscribe(function() {
                var isDirty = wp.data.select('core/editor').isEditedPostDirty();
                var isSaving = wp.data.select('core/editor').isSavingPost();
                
                if (isDirty || isSaving) {
                    disableShareButton();
                } else {
                    enableShareButton();
                }

                // Detect when saving completes
                if (wasSaving && !isSaving) {
                    var currentStatus = wp.data.select('core/editor').getCurrentPost().status;
                    if (currentStatus === 'publish') {
                        // Add loader before the 8 second timeout
                        if ($('#xtfefoli_auto_share_loader').length === 0) {
                            $('#xtfefoli_share_cpt_button').after('<div id="xtfefoli_auto_share_loader" style="color: #2271b1; margin-top: 8px; font-weight: 500;">Post sharing... just a little bit more time... <span class="spinner is-active" style="float:none; margin: 0 5px;"></span></div>');
                            $('#xtfefoli_share_cpt_button').prop('disabled', true).css({ 'opacity': '0.5', 'cursor': 'not-allowed' });
                        }

                        // Poll every 5 seconds for the background auto-share cron to finish, up to 6 times (30s max)
                        let attempts = 0;
                        let pollInterval = setInterval(function() {
                            attempts++;
                            let postID = $('#post_ID').val();
                            let nonce = $('#xtfefoli_linkedin_feedpress_meta_box_nonce').val();
                            
                            if (postID && nonce) {
                                $.post(ajaxurl, {
                                    action: 'xtfefoli_get_sharing_history',
                                    post_id: postID,
                                    xtfefoli_share_security: nonce
                                }, function(response) {
                                    // If we got success (data) OR an error message (failed share), stop polling
                                    if ((response.success && response.data && response.data.html) || 
                                        (!response.success && response.data && response.data.message)) {
                                        
                                        clearInterval(pollInterval);
                                        $('#xtfefoli_auto_share_loader').remove();
                                        $('#xtfefoli_share_cpt_button').prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
                                        
                                        if (response.success && response.data && response.data.html) {
                                            $('#xtfefoli_history_wrapper').html(response.data.html);
                                        } else {
                                            // Show error notice if sharing failed
                                            var lferrorHtml = `<div class="notice notice-error is-dismissible"><p>${response.data.message}</p></div>`;
                                            $("#if_meta_box_notice").html(lferrorHtml);
                                            setTimeout(function () {
                                                $("#if_meta_box_notice").fadeOut("slow", function () {
                                                    $(this).html("").show();
                                                });
                                            }, 6000);
                                        }
                                    } else if (attempts >= 6) {
                                        // Timeout reached, stop polling
                                        clearInterval(pollInterval);
                                        $('#xtfefoli_auto_share_loader').remove();
                                        $('#xtfefoli_share_cpt_button').prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
                                    }
                                }).fail(function() {
                                    if (attempts >= 6) {
                                        clearInterval(pollInterval);
                                        $('#xtfefoli_auto_share_loader').remove();
                                        $('#xtfefoli_share_cpt_button').prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
                                    }
                                });
                            } else {
                                clearInterval(pollInterval);
                                $('#xtfefoli_auto_share_loader').remove();
                                $('#xtfefoli_share_cpt_button').prop('disabled', false).css({ 'opacity': '1', 'cursor': 'pointer' });
                            }
                        }, 5000);
                    }
                }
                wasSaving = isSaving;
            });
        } else {
            // Classic Editor Support
            $('#title, #content').on('input change', function() {
                disableShareButton();
            });
            // Classic editor featured image remove/add
            $(document).ajaxSuccess(function(e, xhr, settings) {
                if (settings.data && typeof settings.data === 'string' && settings.data.includes('action=set-post-thumbnail')) {
                    disableShareButton();
                }
            });
        }

	});

})( jQuery );


