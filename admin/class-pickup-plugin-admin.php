<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://atul.com
 * @since      1.0.0
 *
 * @package    Pickup_Plugin
 * @subpackage Pickup_Plugin/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pickup_Plugin
 * @subpackage Pickup_Plugin/admin
 * @author     atul.com/atul-plugin <atul@atul.com>
 */
class Pickup_Plugin_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pickup-plugin-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pickup-plugin-admin.js', array( 'jquery' ), $this->version, false );

	}

	//To Create a custom post type "Stores"
	public function store_post_type()
	{
		register_post_type(
			'store',
			array(
				'labels' => array(
					'name' => __('Stores'),
					'singular_name' => __('Store')
				),
				'public' => true,
				'has_archive' => true,
				'supports' => array('author'),
				'menu_icon' => 'dashicons-store',

			)
		);
	}
	
	//Adding custom meta boxes to the this custom post type
	public function store_meta_box_added()
	{
		add_meta_box(
			'store_information',
			__('Store Information', 'pickup'),
			array($this,'store_information_callback'),
			'store',
			'normal',
			'high'
		);
	}

	//Callback for add_meta_box to create HTML markup for metaboxes
	public function store_information_callback($post)
	{
		$store_name = get_post_meta($post->ID, '_store_name', true);
		$store_address = get_post_meta($post->ID, '_store_address', true);
		$contact_info = get_post_meta($post->ID, '_contact_info', true);

		wp_nonce_field('store_information', 'store_information_nonce');

?>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="store_name"><?php _e('Store Name', 'pickup'); ?></label></th>
					<td><input type="text" id="store_name" name="store_name" value="<?php echo esc_attr($store_name); ?>"></td>
				</tr>
				<tr>
					<th><label for="store_address"><?php _e('Store Address', 'pickup'); ?></label></th>
					<td><textarea id="store_address" name="store_address"><?php echo esc_textarea($store_address); ?></textarea></td>
				</tr>
				<tr>
					<th><label for="contact_info"><?php _e('Contact Info', 'pickup'); ?></label></th>
					<td><input type="text" id="contact_info" name="contact_info" value="<?php echo esc_attr($contact_info); ?>"></td>
				</tr>
			</tbody>
		</table>

<?php
	}

	//To save meta box values in post meta
	public function store_metabox_save($post_id)
	{
		if (!isset($_POST['store_information_nonce']) || !wp_verify_nonce($_POST['store_information_nonce'], 'store_information'))
		{
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		{
			return;
		}

		if (isset($_POST['post_type']) && 'store' == $_POST['post_type'])
		{
			if (current_user_can('edit_post', $post_id))
			{
				if (isset($_POST['store_name']))
				{
					update_post_meta($post_id, '_store_name', sanitize_text_field($_POST['store_name']));
				}
				
				if (isset($_POST['store_address']))
				{
					update_post_meta($post_id, '_store_address', sanitize_text_field($_POST['store_address']));
				}

				if (isset($_POST['contact_info']))
				{
					update_post_meta($post_id, '_contact_info', sanitize_text_field($_POST['contact_info']));
				}
			}
		}
	}

	//To add custom columns to the admin panel's list of stores. 
	public function store_list_add($columns)
	{
		$columns['store_name'] = __('Store Name', 'pickup');
		$columns['store_address'] = __('Store Address', 'pickup');
		$columns['contact_info'] = __('Contact Info', 'pickup');
		return $columns;
	}

	//To display the data for each column added by the previous function.
	public function displaystore_list_display($column, $post_id)
	{
		switch ($column)
		{
			case 'store_name' : echo get_post_meta($post_id, '_store_name', true);
								break;

			case 'store_address' : echo get_post_meta($post_id, '_store_address', true);
									break;

			case 'contact_info' : echo get_post_meta($post_id, '_contact_info', true);
									break;
		}
	}

	//To makes the custom columns sortable
	public function make_store_list_sortable($columns)
	{
		$columns['store_name'] = 'store_name';
		$columns['store_address'] = 'store_address';
		$columns['contact_info'] = 'contact_info';
		return $columns;
	}

	//To send order confirmation mail with store details and pickup date
	public function send_order_mail()
	{
		// Get pickup date and selected store from POST data
		if (isset($_POST['pickup_date']) && !empty($_POST['pickup_date']))
		{
			$pickup_date = sanitize_text_field($_POST['pickup_date']);
		}

		if (isset($_POST['store_options']) && !empty($_POST['store_options']))
		{
			$store_options = sanitize_text_field($_POST['store_options']);
		}

		// Send confirmation email
		$message = '<h2>Store Pickup Details</h2>';
		$formatted_date = date('d-m-Y', strtotime($pickup_date)); // format date as dd-mm-yyyy

		$message .= "Pickup Date: $formatted_date".'<br>';
		$message .= "Selected Store: $selected_store".'<br>';

		echo $message;
	}

	//To save pickup location and date
	public function order_save($order)
	{
		if (isset($_POST['pickup_date']) && isset($_POST['store_options']))
		{
			$pickup_date = sanitize_text_field($_POST['pickup_date']);
			$selected_store = sanitize_text_field($_POST['store_options']);
			$order->update_meta_data( 'pickup_id', $pickup_date );
			$order->update_meta_data( 'store_id', $selected_store );
		}
	}

	//Style to hide checkout fields on pickup option selection
	public function pickup_local()
	{
		if (is_checkout()) :
?>
			<style>
				.hide_pickup {display: none!important;}
			</style>

			<script>
				jQuery( function( $ ) {
					if ( typeof woocommerce_params === 'undefined' ) {
					return false;
					}
					$(document).on( 'change', '#shipping_method input[type="radio"]', function() {
					
						$('.billing-dynamic_pickup').toggleClass('hide_pickup', this.value == 'local_pickup:1');
					});
				});
			</script>
<?php
		endif;
	}

	public function local_pickup_hide( $fields_pickup )
	{
		// change below for the method
		$shipping_method_pickup ='local_pickup:1';
		// change below for the list of fields. Add (or delete) the field name you want (or don’t want) to use
		$hide_fields_pickup = array( 'billing_company', 'billing_country', 'billing_postcode', 'billing_address_1', 'billing_address_2' , 'billing_city', 'billing_state');

		$chosen_methods_pickup = WC()->session->get( 'chosen_shipping_methods' );
		$chosen_shipping_pickup = $chosen_methods_pickup[0];

		foreach($hide_fields_pickup as $field_pickup)
		{
			if ($chosen_shipping_pickup == $shipping_method_pickup)
			{
				$fields_pickup['billing'][$field_pickup]['required'] = false;
				$fields_pickup['billing'][$field_pickup]['class'][] = 'hide_pickup';
			}
			$fields_pickup['billing'][$field_pickup]['class'][] = 'billing-dynamic_pickup';
		}
		return $fields_pickup;
	}

	//Accessing all orders having pickupdate tommorow
	public function pickup_reminder_emails()
	{
		$args = array(
			'post_type' => 'shop_order',
			'posts_per_page' => '-1',
			'post_status' => 'any'
		  );

		$query = new WP_Query($args);
		$posts = $query->posts;

		// Calculate next day
		$next_day = strtotime( '+1 day', current_time( 'timestamp' ) );
		$next_day_date = date( 'Y-m-d', $next_day );

		foreach ( $posts as $post ) {
			$order_id = $post->ID;
			$this->send_pickup_reminder_email($order_id);
		}

	}

	//To send remainder mail
	public function send_pickup_reminder_email($order_id)
	{
		$order = wc_get_order($order_id);
		$pickup_date = $order->get_meta('pickup_id');
		$selected_store = $order->get_meta('store_id');
		$customer_email = $order->get_billing_email();

		// Check if pickup date is exactly one day away from today
		$pickup_timestamp = strtotime($pickup_date);
		$one_day_before_pickup_timestamp = strtotime('-1 day', $pickup_timestamp);
		if (date('Y-m-d', $one_day_before_pickup_timestamp) === date('Y-m-d'))
		{
			// Send reminder email
			$subject = 'Reminder: Store Pickup Tomorrow';
			$message = "<p>Dear customer,</p>";
			$message .= "<p>This is a reminder that you have a store pickup scheduled for tomorrow at the following location:</p>";
			$message .= "<p>Selected Store: $selected_store</p>";
			$formatted_date = date('d-m-Y', strtotime($pickup_date));
			$message .= "<p>Pickup Date: $formatted_date</p>";
			$message .= "<p>Thank you for choosing our store. We look forward to seeing you tomorrow.</p>";
			$headers = array('Content-Type: text/html; charset=UTF-8');
			wp_mail($customer_email, $subject, $message, $headers);
		}
	}
}

