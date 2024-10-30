<?php

if(!class_exists('Dukagate_Markets')) {

	class Dukagate_Markets extends Markets_Extensions{

		//Name of the plugin.
		var $plugin_name = 'Dukagate';
		
		//shortname of plugin
		var $plugin_slug = 'dukagate';

		//main file of plugin
		var $plugin_file = 'dukagate/dukagate.php';

		//id of plugin
		var $plugin_id = 'dkgt_';

		var $post_type = 'dg_product';
	}
}
?>