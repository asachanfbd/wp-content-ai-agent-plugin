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
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_admin_menu() {
		add_menu_page(
			'AI Content Creator Settings',
			'AI Content Creator',
			'manage_options',
			'wp-content-ai-agent',
			array( $this, 'create_admin_page' ),
			'dashicons-superhero',
			6
		);
	}

	public function page_init() {
		register_setting(
			'wp_content_ai_agent_option_group', // Option group
			'wp_content_ai_agent_api_key', // Option name
			array( $this, 'sanitize_api_key' ) // Sanitize
		);

		register_setting(
			'wp_content_ai_agent_option_group', // Option group
			'wp_content_ai_agent_model_id', // Option name
			array( $this, 'sanitize_text_field' ) // Sanitize
		);

		add_settings_section(
			'wp_content_ai_agent_setting_section', // ID
			'API Configuration', // Title
			array( $this, 'section_info' ), // Callback
			'wp-content-ai-agent' // Page
		);

		add_settings_field(
			'api_key', // ID
			'API Key', // Title
			array( $this, 'api_key_callback' ), // Callback
			'wp-content-ai-agent', // Page
			'wp_content_ai_agent_setting_section' // Section
		);

		add_settings_field(
			'model_id', // ID
			'Model ID', // Title
			array( $this, 'model_id_callback' ), // Callback
			'wp-content-ai-agent', // Page
			'wp_content_ai_agent_setting_section' // Section
		);
	}

	public function sanitize_api_key( $input ) {
		return sanitize_text_field( $input );
	}

	public function sanitize_text_field( $input ) {
		return sanitize_text_field( $input );
	}

	public function section_info() {
		print 'Enter your OpenRouter API settings below:';
	}

	public function api_key_callback() {
		printf(
			'<input type="password" id="api_key" name="wp_content_ai_agent_api_key" value="%s" class="regular-text" />',
			esc_attr( get_option( 'wp_content_ai_agent_api_key' ) )
		);
	}

	public function model_id_callback() {
		printf(
			'<input type="text" id="model_id" name="wp_content_ai_agent_model_id" value="%s" class="regular-text" placeholder="google/gemini-2.0-flash-exp" />',
			esc_attr( get_option( 'wp_content_ai_agent_model_id' ) )
		);
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1>AI Content Creator</h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'wp_content_ai_agent_option_group' );
				do_settings_sections( 'wp-content-ai-agent' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}

// Initialize the class.
if ( class_exists( 'WP_Content_AI_Agent' ) ) {
	$wp_content_ai_agent = new WP_Content_AI_Agent();
}
