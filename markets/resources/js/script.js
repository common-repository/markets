var markets = {
	
	/**
	 * Toggle forms
	 */
	toggle : function(id){
		jQuery('.logintable').hide();
		jQuery('.registrationtable').hide();
		jQuery('.'+id).show();
	},

	filter : function(elem){
		var selected = jQuery(elem).val();
		jQuery(".from_market option").each(function(){
			jQuery(this).show();
		});
		jQuery(".to_market option").each(function(){
			jQuery(this).show();
		});
		if(jQuery(elem).hasClass("from_market")){
			jQuery(".to_market option[value=" + selected + "]").hide();
		}else{
			jQuery(".from_market option[value=" + selected + "]").hide();
		}
	},

	/**
	 * Restore
	 */
	restore : function(elem){
		var r = confirm(mrkts.delete_confirm);
		if (r == true) {
			jQuery("div.process_status").html(mrkts.processing);
			var plugin_slug = jQuery(elem).attr("data-slug");
			var security = jQuery(elem).attr("data-nonce");
			var data = {
				action: 'markets_restore_'+plugin_slug,
				security: security
			};
			jQuery.post(ajaxurl, data, function(response) {
				jQuery("div.process_status").html(response.message);
			});
		}
	},

	/**
	 * Sync
	 */
	sync : function(elem){
		jQuery("div.process_status").html(mrkts.processing);
		var plugin_slug = jQuery(elem).attr("data-slug");
		var security = jQuery(elem).attr("data-nonce");
		var data = {
			action: 'markets_sync_'+plugin_slug,
			security: security
		};
		jQuery.post(ajaxurl, data, function(response) {
			jQuery("div.process_status").html(response.message);
		});
	},
    
    /**
     * Copy
     */
    copy : function(elem){
        var r = confirm(mrkts.copy_confirm);
		if (r == true) {
            var plugin_slug = jQuery(".from_market").val();
            var to = jQuery(".to_market").val();
            if(plugin_slug && to){
                jQuery("div.process_status").html(mrkts.processing);
                var security = jQuery(elem).attr("data-nonce");
                var data = {
                    action: 'markets_copy_'+plugin_slug,
                    security: security,
                    to : to
                };
                jQuery.post(ajaxurl, data, function(response) {
                     jQuery("div.process_status").html(response.message);
                });
            }else{
                jQuery("div.process_status").html(mrkts.empty);
            }
        }
    }
}