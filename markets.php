<?php
/*
Plugin Name: Markets
Description: Markets is an e-commerce plugin that allows you to sync your products. The markets API manages all your products in the database allowing you to sync your products on the supported e-commerce extensions . The Markets plugin allows you to sync products across supported e-commerce plugins and import them into other plugins activated in your WordPress installation. This allows you to try out and share products across different e-commerce WordPress plugins
Version: 2.0.1
Author: rixeo
Author URI: http://thebunch.co.ke/
Plugin URI: http://thebunch.co.ke/
Text Domain: markets
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Markets' ) ) :

    class WP_Markets{
        
        /**
         * Current plugin version.
         *
         * @since 2.0.0
         * @var string
         */
        public $version = '1.0.0';
        
        /**
         * The single instance of the class
         *
         * @since 2.0.0
         */
        protected static $_instance = null;
        
        
        /**
         * Backup options
         *
         * @since 2.0.0
         */
        var $backup_options = array('local' => 'Installation Directory');
        
        
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
        
        function __construct() {
            $this->define_constants();
            
            $this->load_includes();
                        
            $this->init_hooks();
            
            //localize the plugin
		    add_action( 'plugins_loaded', array( &$this, 'localization' ), 9 );
        }
        
        
        /**
         * Define Plugin constants
         *
         * @since 2.0.0
         */
        private function define_constants(){
            $this->define( 'MARKETS_PLUGIN_FILE', __FILE__ );
            $this->define( 'MARKETS_PLUGIN_URL', plugin_dir_url( __FILE__ ) . 'markets/');
            $this->define( 'MARKETS_PLUGIN_RESOURCES_URL', MARKETS_PLUGIN_URL . 'resources/');
            $this->define( 'MARKETS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) . 'markets/');
			$this->define( 'MARKETS_LANGUAGE_DIR', MARKETS_PLUGIN_DIR . 'lang');
            $this->define( 'MARKETS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
            $this->define( 'MARKETS_VERSION', $this->version );
            
            $this->define('MARKETS_DATA_URL', content_url().'/markets'); //Store data outside plugin directory
            $this->define('MARKETS_CONTENT_DIR', WP_CONTENT_DIR.'/markets');
        }
        
        
        /**
         * Define constant if not already set
         *
         * @param  string $name
         * @param  string|bool $value
         *
         * @since 2.0.0
         */
        private function define( $name, $value ) {
  		    if ( ! defined( $name ) ) {
  			   define( $name, $value );
  		    }
  	    }
        
        
        /**
         * Load the installation language
         *
         * @since 2.0.0
         */
        function localization(){
            $lang_dir	 = MARKETS_LANGUAGE_DIR;
            $custom_path = WP_LANG_DIR . '/markets/markets-' . get_locale() . '.mo';
            $mu_plugins	 = wp_get_mu_plugins();
            if ( file_exists( $custom_path ) ) {
                load_textdomain( 'markets', $custom_path );
            } elseif ( in_array( MARKETS_PLUGIN_BASENAME, $mu_plugins ) ) {
                load_muplugin_textdomain( 'markets', $lang_dir );
            } else {
                load_plugin_textdomain( 'markets', false, $lang_dir );
            }
        }
        
        
        /**
         * Backup Directory
         * 
         * @since 2.0.0
         */
        function init_directories(){
			if (!is_dir(MARKETS_CONTENT_DIR)) {
				mkdir(MARKETS_CONTENT_DIR, 0, true);
				chmod(MARKETS_CONTENT_DIR, 0777);
                $blank_file = MARKETS_CONTENT_DIR.'/index.php';
                $file_handle = fopen($blank_file, 'w') or die("can't open file");
			    fclose($file_handle);
			}
        }
        
        
        /**
         * Include all needed files
         *
         * @since 2.0.0
         */
        private function load_includes(){
            $this->load_classes(MARKETS_PLUGIN_DIR.'classes/');
            $this->load_classes(MARKETS_PLUGIN_DIR.'plugins/libs/');
        }
        
        /**
         * Init hooks
         *
         * @since 2.0.0
         */
        private function init_hooks() {
            add_action( 'init', array(&$this, 'init_directories' ), 0 );
            
            //load Extensions
            add_action('wp_loaded', array(&$this, 'load_extensions'));
            
            
            add_action( 'init', array(&$this, 'init_functions') );
            
            
        }
        
        
        /**
         * Init functions
         * Functions called on load
         *
         * @since 2.0.0
         */
        public function init_functions(){
            Markets_Admin::instance();
        }
        
        
        /**
		 * Load Classes in directories
		 * 
		 * @since 1.0
		 */
		private function load_classes($dir= ''){
			$plugins = array();
			if ( !is_dir( $dir ) )
				return;
			if ( ! $dh = opendir( $dir ) )
				return;
				
			while ( ( $plugin = readdir( $dh ) ) !== false ) {
				if ( substr( $plugin, -4 ) == '.php' ){
					$plugins[] = $dir . $plugin;
				}
			}
			closedir( $dh );
			sort( $plugins );
						
			//include them suppressing errors
			foreach ($plugins as $file){
				include_once( $file );
			}
		}
        
        
        /**
         * Load Extensions
         * @since 2.0.0
         */
        function load_extensions() {

            $dir = MARKETS_PLUGIN_DIR.'plugins/extensions/';
            
            //search the dir for files
            $extensions = array();
            $classes = array();
            if (!is_dir($dir)) return;
            if (!$dh = opendir($dir)) return;
            while (($plugin = readdir($dh)) !== false) {
                if (substr($plugin, -4) == '.php') 
                    $extensions[] = $dir . '/' . $plugin;
            }
            closedir($dh);
            sort($extensions);
            
            //include them suppressing errors
            foreach ($extensions as $file) {
                require_once ($file);
                $fp = fopen($file, 'r');
                $class = $buffer = '';
                $i = 0;
                while (!$class) {
                    if (feof($fp)) break;

                    $buffer.= fread($fp, 512);
                    if (preg_match('/class\s+(\w+)(.*)?\{/', $buffer, $matches)) {
                        $classes[] = $matches[1];
                        break;
                    }
                }
            }
            
            //Instantiate classes
            foreach ($classes as $class) {
                $c = new $class();
            }

            //allow plugins from an external location to register themselves
            do_action('markets_load_etension');
        }
        
        
        /*
         * Get settings array without undefined indexes
         * @param string $key A setting key, or -> separated list of keys to go multiple levels into an array
         * @param mixed $default Returns when setting is not set
         *
         * @since 2.0.0
        */
        function get_setting($key, $default = "") {
            $settings = get_option('markets_settings');
            $keys = explode('->', $key);
            array_map('trim', $keys);
            if (count($keys) == 1) 
				$setting = isset($settings[$keys[0]]) ? $settings[$keys[0]] : $default;
            else if (count($keys) == 2) 
				$setting = isset($settings[$keys[0]][$keys[1]]) ? $settings[$keys[0]][$keys[1]] : $default;
            else if (count($keys) == 3) 
				$setting = isset($settings[$keys[0]][$keys[1]][$keys[2]]) ? $settings[$keys[0]][$keys[1]][$keys[2]] : $default;
            else if (count($keys) == 4) 
				$setting = isset($settings[$keys[0]][$keys[1]][$keys[2]][$keys[3]]) ? $settings[$keys[0]][$keys[1]][$keys[2]][$keys[3]] : $default;

            return apply_filters("markets_setting_" . implode('', $keys), $setting, $default);
        }
        
        
        /**
         * Get all settings
         *
         * @since 2.0.0
         */
        function get_settings(){
            return get_option('markets_settings');
        }
        
        
        /**
         * Update Settings
         * @param string $key A setting key
         * @param mixed $value
         *
         * @since 2.0.0
         */
        function update_setting($key, $value) {
            $settings = $this->get_settings();
            $settings[$key] = $value;
            update_option('markets_settings', $settings);
        }
        
        
        /**
         * Update All Settings
         * @param Array $settings
         *
         * @since 2.0.0
         */
        function save_settings($settings = array()) {
            update_option('markets_settings', $settings);
        }
        
        
    }

    $markets = WP_Markets::instance(); //Initialise

else:
    
    /**
    * Show Error
    */
    function easy_courses_error_notice() {
        $message = __("Another plugin already using the class name WP_Markets exists. The Markets plugin will not work as expected");
        echo"<div class='error'> <p>$message</p></div>";
    }
    add_action( 'admin_notices', 'easy_courses_error_notice' );
endif;
?>