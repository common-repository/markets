<?php
if(!class_exists('Markets_Admin')) {

	class Markets_Admin{
        
        
        /**
         * The single instance of the class
         *
         * @since 2.0.0
         */
        protected static $_instance = null;
        
        
        /**
         * Get the instance
         * 
         * @since 2.0.0
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

		/**
		 * Configures the plugin admin.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action('admin_print_styles', array($this, 'scripts') );
			add_action('admin_print_scripts', array($this, 'styles') );

			add_action('admin_menu', array(&$this, 'admin_menu'));
            
            
            
		}

		/**
		 * Load the required javascripts
		 * 
		 * @since 1.0
		 */
		function scripts(){
			wp_enqueue_script('markets_js', MARKETS_PLUGIN_RESOURCES_URL.'js/script.js', array('jquery'), '', false);
			wp_enqueue_script("markets_js");
			wp_localize_script( 'markets_js', 'mrkts', array( 
				'processing' => __("Processing. Please wait", "markets"),
				'delete_confirm' => __("This will delete all products in this plugin", "markets"),
                'copy_confirm' => __("Are you sure you want to copy?", "markets"),
                'empty' => __("All fields required", "markets")
			) );
		}

		/**
		 * Load the required css files
		 * 
		 * @since 1.0
		 */
		function styles(){
			wp_enqueue_style('markets_css', MARKETS_PLUGIN_RESOURCES_URL.'css/style.css');
		}

		/**
		 * Lets create the admin menu
		 * 
		 * @since 1.0
		 */
		function admin_menu(){
			if ( current_user_can('manage_options') && current_user_can('edit_others_posts')){
				add_object_page( __('Markets', 'markets'), __('Markets', 'markets'), 'edit_others_posts', 'markets', '', MARKETS_PLUGIN_RESOURCES_URL . 'images/icon.png');
				add_submenu_page('markets', __('Markets', 'markets'), __('Markets', 'markets'), 'edit_others_posts', 'markets', array(&$this, 'settings'));
				add_submenu_page('markets', __('Product Management', 'markets'), __('Product Management', 'markets'), 'edit_others_posts', 'markets-products', array(&$this, 'manage_products'));
			}
		}

		/**
		 * Admin Settings
		 * 
		 * @since 1.0
		 */
		function settings(){
			// Use nonce for verification
			wp_nonce_field( MARKETS_PLUGIN_BASENAME, 'markets_noncename' );

			//Check if the current user has permission to access this page
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.','markets'));
			}

			global $markets;
            ?>
            <div id="message" class="update-nag">
                <p>
                    <?php _e("The Markets plugin allows you to sync products across supported e-commerce plugins and import them into other plugins activated in your WordPress installation. This allows you to try out and share products across different e-commerce WordPress plugins","markets");?>
                </p>
            </div>
            <?php

			if (! empty( $_POST ) && check_admin_referer('markets_settings','markets_noncename') ){
				
                $mrkts_backup_options = $_POST['mrkts_backup_options'];
			    update_option('mrkts_backup_options', $mrkts_backup_options);
				?>
				<div id="message" class="update-nag">
					<h3><?php _e("Updated","markets"); ?></h3>
				</div>
				<?php
			}
            $mrkts_backup_options = get_option( 'mrkts_backup_options' );
            $options = $markets->backup_options;
			?>
			<div class="wrap">
				<h2><?php _e('Markets Settings','markets'); ?></h2>
				
                <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
                    <?php wp_nonce_field('markets_settings','markets_noncename'); ?>
                    <table class="widefat">
                        <tbody>
                            <tr>
                                <th scope="row"><?php _e("Select Backup Options","markets"); ?></th>
                                <td id="front-static-pages">
                                    <fieldset>
                                        <legend class="screen-reader-text"><span><?php _e("Select Backup Options","markets"); ?></span></legend>
                                        <?php 
                                        $selected = "";
                                        foreach ($options as $key => $value) {
                                            if(is_array($mrkts_backup_options))
                                                if(in_array($key, $mrkts_backup_options)){
                                                    $selected = "checked='checked'";
                                                }else{
                                                    $selected = "";
                                                }
                                                    ?>
                                            <p>
                                            <label>
                                                <input type="checkbox" class="tog" <?php echo $selected; ?> value="<?php echo $key; ?>" name="mrkts_backup_options[]">
                                                <?php _e($value); ?>
                                                </label>
                                            </p>
                                        <?php } 
                                        ?>
                                    </fieldset>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="submit">
                        <input id="submit" class="button button-primary" type="submit" value="<?php _e("Save Changes","markets"); ?>" name="submit" />
                    </p>
                </form>
			</div>
			<?php
		}


		/**
		 * Manage Products
		 *
		 * @since 1.0
		 */
		function manage_products(){
			// Use nonce for verification
			wp_nonce_field( MARKETS_PLUGIN_BASENAME, 'markets_products_noncename' );

			//Check if the current user has permission to access this page
			if (!current_user_can('manage_options')) {
				wp_die(__('You do not have sufficient permissions to access this page.','markets'));
			}

			global $markets;
            $settings = $markets->get_settings();
			$markets_plugins = $settings['plugins'];
            ?>
            <div id="message" class="update-nag">
                <p>
                    <?php _e("Manage your products for the supported plugins","markets"); ?>
                    <div class="process_status"></div>
                </p>
            </div>
			<div class="wrap">
				<h2><?php _e('Manage Products','markets'); ?></h2>
				
				<br/><br/><br/>
				<table class="widefat">
					<tbody>
						<tr>
							<td><?php _e("Export products from ","markets");?></td>
							<td>
								<select class="from_market marketexport">
									<option>--<?php _e("Select ","markets");?>--</option>
									<?php 
										foreach ($markets_plugins as $key => $value) {
											if(is_plugin_active($value['file'])){
												?><option value="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo  $value['name'];?></option><?php
											}
										}
									?>
								</select>
							</td>
							<td><?php _e(" To ","markets");?></td>
							<td>
								<select class="to_market marketexport">
									<option>--<?php _e("Select ","markets");?>--</option>
									<?php 
										foreach ($markets_plugins as $key => $value) {
											if(is_plugin_active($value['file'])){
												?><option value="<?php echo $key; ?>" id="<?php echo $key; ?>"><?php echo  $value['name'];?></option><?php
											}
										}
									?>
								</select>
							</td>
                            <?php
                                $ajax_nonce = wp_create_nonce("copy_markets_nonce");
                            ?>
							<td><button class='button-primary market-copy' data-nonce="<?php echo $ajax_nonce; ?>"><?php _e("Copy","markets");?></button></td>
						</tr>
					</tbody>
				</table>
				<br/>
				<table class="widefat">
					<thead>
						<tr>
							<th scope="col"><?php _e("Name","markets");?></th>
							<th scope="col"><?php _e("Actions","markets");?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							
							foreach ($markets_plugins as $key => $value) {
								$active = "";
								$active_text = "";
								if(!is_plugin_active($value['file'])){
									$active = "disabled";
									$active_text = "  (".__("Plugin not activated","markets").")";
								}
								$ajax_nonce = wp_create_nonce($key."_markets_nonce");
								?>
								<tr>
									<td><?php echo $value['name'].$active_text; ?></td>
									<td>
										<button class='button-primary market-backup' data-slug="<?php echo $key; ?>" data-nonce="<?php echo $ajax_nonce; ?>" <?php echo $active; ?> ><?php _e("Backup Products","markets");?></button>&nbsp;&nbsp;<button class='button-primary market-restore' data-slug="<?php echo $key; ?>" data-nonce="<?php echo $ajax_nonce; ?>" <?php echo $active; ?>><?php _e("Restore Products","markets");?></button>
									</td>
								</tr>
								<?php
							}
						?>
					</tbody>
				</table>
				<script type="text/javascript">
					jQuery(document).ready(function(){
                        jQuery("select.marketexport" ).change(function() {
							markets.filter(jQuery(this));
						});
                        
						jQuery("button.market-backup").on('click', function(e) {
							markets.sync(jQuery(this));
						});
						jQuery("button.market-restore").on('click', function(e) {
							markets.restore(jQuery(this));
						});
						jQuery("button.market-copy").on('click', function(e) {
							markets.copy(jQuery(this));
						});
					});
				</script>
			</div>
			<?php
		}
        
	}
}
?>