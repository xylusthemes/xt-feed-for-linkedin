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
                    xtfefoli_share_security: nonce
                },
                beforeSend: function () {
                    var lfsrHtml = `<div class="notice notice-primary is-dismissible"><p>Sharing...</p></div>`;
                    $("#if_meta_box_notice").html(lfsrHtml);
                },
                success: function (response) {
                    if (response.success) {
                        var lfscHtml = `<div class="notice notice-success is-dismissible"><p>Post shared successfully!</p></div>`;
                        $("#if_meta_box_notice").html(lfscHtml);
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
            let noticeBox = button.closest('div').find('.if_lt_notice'); // Get notice box for this row
    
            $.ajax({
                url: ajaxurl, // WordPress AJAX URL
                type: 'POST',
                data: {
                    action: 'xtfefoli_share_to_linkedin_lt',
                    post_id: postID,
                    xtfefoli_post_type: xtfefoli_post_type,
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

                    xtfefoli_post_postID
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
		
	});

})( jQuery );


