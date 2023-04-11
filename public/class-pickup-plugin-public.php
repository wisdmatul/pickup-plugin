<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://atul.com
 * @since      1.0.0
 *
 * @package    Pickup_Plugin
 * @subpackage Pickup_Plugin/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Pickup_Plugin
 * @subpackage Pickup_Plugin/public
 * @author     atul.com/atul-plugin <atul@atul.com>
 */
class Pickup_Plugin_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pickup_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pickup_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pickup-plugin-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pickup_Plugin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pickup_Plugin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pickup-plugin-public.js', array( 'jquery' ), $this->version, false );

	}

	//Callback to add_shortcode 
	function display_options_store()
	{
		$stores = get_posts(array(
			'post_type' => 'store',
			'post_status' => 'publish',
			'posts_per_page' => -1,
		));

		$store_data = array();

		foreach ($stores as $store)
		{
			$store_name = get_post_meta($store->ID, '_store_name', true);
			$store_address = get_post_meta($store->ID, '_store_address', true);
			$contact_info = get_post_meta($store->ID, '_contact_info', true);

			$store_data[] = $store_name . "," . $store_address . "," . $contact_info;
		}

		// Set minimum allowed date for pickup date field
		$min_date = date('Y-m-d', strtotime('+1 day'));  // Set minimum date to tomorrow

		$pickup_date_field = '<label for="pickup_date"><h2>Pickup Date</h2></label>
                          <input type="date" name="pickup_date" id="pickup_date" min="' . $min_date . '" required>';

		$options = 'Select Store';
		foreach ($store_data as $store)
		{
			$options .= '<option value="' . $store . '">' . $store . '</option>';
		}

		$var =  '<form>' . $pickup_date_field . '<br><br>
            <label for="store_options"><h2>Select Store</h2></label>
            <select name="store_options">' . $options . '</select>
            </form>';

			echo $var;
	}
}
