<?php

if(!class_exists('Dukapress_Markets')) {

	class Dukapress_Markets extends Markets_Extensions{

		//Name of the plugin.
		var $plugin_name = 'Dukapress';
		
		//shortname of plugin
		var $plugin_slug = 'dukapress';

		//main file of plugin
		var $plugin_file = 'dukapress/dukapress.php';

		//id of plugin
		var $plugin_id = 'dpsc_';

		var $post_type = 'duka';

		function get_price($id){
			if (is_numeric(get_post_meta($id, 'new_price', true))) {
				return get_post_meta($id, 'new_price', true);
			}else{
				return get_post_meta($id, 'price', true);
			}
		}
	}
}
?>