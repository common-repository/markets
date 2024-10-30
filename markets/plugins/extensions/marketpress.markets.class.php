<?php
if(!class_exists('MarketPress_Markets')) {
	
	class MarketPress_Markets extends Markets_Extensions{

		//Name of the plugin.
		var $plugin_name = 'MarketPress';
		
		//shortname of plugin
		var $plugin_slug = 'marketpress';

		//main file of plugin
		var $plugin_file = 'marketpress/marketpress.php';

		//id of plugin
		var $plugin_id = 'mp_';

		var $post_type = 'product';

		function get_price($id){
			$meta = get_post_custom($id);
			//unserialize
	 		foreach ($meta as $key => $val) {
			 	$meta[$key] = maybe_unserialize($val[0]);
			 	if (!is_array($meta[$key]) && $key != "mp_is_sale" && $key != "mp_track_inventory" && $key != "mp_product_link")
					$meta[$key] = array($meta[$key]);
			}
			if (is_array($meta["mp_price"])) {
				$price = $meta["mp_price"][0];
			}
			$price = ($price) ? $price : 0;
			return $price;
		}


		function set_price($id, $mp_price){
			$func_curr = '$price = round(preg_replace("/[^0-9.]/", "", $price), 2);return ($price) ? $price : 0;';
			update_post_meta($id, 'mp_price', array_map(create_function('$price', $func_curr), (array)$mp_price));
		}
	}
}
?>