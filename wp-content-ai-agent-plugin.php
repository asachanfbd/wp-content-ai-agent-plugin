<?php
/**
 * Plugin Name:       WP Content AI Agent
 * Plugin URI:        https://www.coravity.com/plugins/wp-content-ai-agent/
 * Description:       An AI content agent for WordPress, helps in creating and refreshing content on posts.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Abhishek Sachan
 * Author URI:        https://www.coravity.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wp-content-ai-agent
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants.
define( 'WP_CONTENT_AI_AGENT_VERSION', '1.0.0' );
define( 'WP_CONTENT_AI_AGENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_CONTENT_AI_AGENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function wp_content_ai_agent_activate() {
	// Activation logic here.
}
register_activation_hook( __FILE__, 'wp_content_ai_agent_activate' );

/**
 * The code that runs during plugin deactivation.
 */
function wp_content_ai_agent_deactivate() {
	// Deactivation logic here.
}
register_deactivation_hook( __FILE__, 'wp_content_ai_agent_deactivate' );

/**
 * Initialize the plugin.
 */
function wp_content_ai_agent_init() {
	// Load text domain for translation.
	load_plugin_textdomain( 'wp-content-ai-agent', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'wp_content_ai_agent_init' );

/**
 * Main plugin class (Optional: For better organization).
 */
class WP_Content_AI_Agent {

	public function __construct() {
		// Hook into actions and filters here.
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
	}

	public function add_admin_menu() {
		add_options_page(
			'WP Content AI Agent Settings',
			'WP AI Agent',
			'manage_options',
			'wp-content-ai-agent',
			array( $this, 'create_admin_page' )
		);
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1>WP Content AI Agent</h1>
			<p>Welcome to the WP Content AI Agent plugin settings.</p>
		</div>
		<?php
	}

	/**
	 * Register the meta box.
	 */
	public function register_meta_boxes() {
		add_meta_box(
			'wp_content_ai_agent_meta_box',          // ID
			__( 'AI Content Instructions', 'wp-content-ai-agent' ), // Title
			array( $this, 'render_meta_box' ),       // Callback
			'post',                                  // Screen (post type)
			'side',                                  // Context
			'default'                                // Priority
		);
	}

	/**
	 * Render the meta box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'wp_content_ai_agent_save_meta_box_data', 'wp_content_ai_agent_meta_box_nonce' );

		// Use get_post_meta() to retrieve an existing value from the database and use the value for the form.
		$value = get_post_meta( $post->ID, '_wp_ai_content_instructions', true );

		echo '<label for="wp_ai_content_instructions">';
		_e( 'Provide the topic or keywords for the AI generation:', 'wp-content-ai-agent' );
		echo '</label>';
		echo '<textarea id="wp_ai_content_instructions" name="wp_ai_content_instructions" rows="4" style="width:100%; margin-top: 10px;">' . esc_textarea( $value ) . '</textarea>';
		
		echo '<div style="margin-top: 10px;">';
		echo '<button type="button" class="button button-primary" disabled>' . __( 'Generate', 'wp-content-ai-agent' ) . '</button>';
		echo '</div>';
	}

	/**
	 * Save the meta box data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_meta_box_data( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['wp_content_ai_agent_meta_box_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['wp_content_ai_agent_meta_box_nonce'], 'wp_content_ai_agent_save_meta_box_data' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST['wp_ai_content_instructions'] ) ) {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_textarea_field( $_POST['wp_ai_content_instructions'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, '_wp_ai_content_instructions', $my_data );
	}
}

// Initialize the class.
if ( class_exists( 'WP_Content_AI_Agent' ) ) {
	$wp_content_ai_agent = new WP_Content_AI_Agent();
}
