<?php // phpcs:ignore
/**
 * The plugin's bootstrap.
 *
 * @package           WpBlockBoilerplate
 * @since             1.0.0
 * @link              https://github.com/vurghus-minar
 */

namespace AM\WpBlockBoilerplate;

/**
 * Prevent direct access to file.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin Name: WP Block Boilerplate
 * Plugin URI:  https://github.com/vurghus-minar/wp-block-boilerplate
 * Description: A WordPress Block Boilerplate.
 * Version:     1.0.0
 * Author:      Vurghus Minar <vurghus.minar@outlook.com>
 * Author URI:  https://github.com/vurghus-minar
 * License:     GPL v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: wp-block-boilerplate
 * Domain Path: /languages
 */

/**
 * The plugin class
 */
class Plugin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object   $instance
	 */
	private static $instance;

	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access public
	 * @var      string    $slug
	 */
	public $slug;

	/**
	 * The base URL path (without trailing slash).
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $url    The base URL path of this plugin.
	 */
	private $url;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version
	 */
	private $version;

	/**
	 * The filesystem directory path fo this plugin file.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_dir
	 */
	private $plugin_dir;

	/**
	 * Configuration object
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      object   $config
	 */
	public $config;

	/**
	 * Class constructor.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    array $config_array    Array of configuration.
	 */
	public function __construct( $config_array ) {
		$this->slug       = $config_array['slug'];
		$this->url        = $config_array['url'];
		$this->version    = $config_array['version'];
		$this->plugin_dir = $config_array['plugin_dir'];
		$this->config     = $this->get_config();

		$this->load_textdomain();

		$this->load_block();

		add_filter( 'plugin_row_meta', array( $this, 'plugin_support_link' ), 10, 2 );

	}

	/**
	 * Registers all directories associated with the plugin and converts them to an object array.
	 *
	 * @since     1.0.0
	 * @access    private
	 */
	private function get_config() {
		return (object) [
			'language_dir' => $this->plugin_dir . '/languages',
			'text_domain'  => $this->slug,
			'vendor'       => $this->url . '/assets/vendor/',
			'style'        => $this->url . '/assets/css/',
			'script'       => $this->url . '/assets/js/',
		];

	}

	/**
	 * Registers the plugin and returns a single instance of this class
	 *
	 * @since     1.0.0
	 * @access    public
	 * @param     array $config    Array of configuation.
	 */
	public static function init( $config ) {
		if ( null === self::$instance ) {
			self::$instance = new self( $config );
		}
	}


	/**
	 * Loads block.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function load_block() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'load_block_editor_assets' ) );
		add_action( 'enqueue_block_assets', array( $this, 'load_block_assets' ) );
	}

	/**
	 * Loads block assets.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function load_block_assets() {
		wp_enqueue_style(
			$this->slug,
			$this->config->style . 'style.css',
			array(),
			$this->version
		);
	}

	/**
	 * Loads block editor assets.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function load_block_editor_assets() {
		// Script.
		wp_enqueue_script(
			$this->slug,
			$this->config->script . 'script.js',
			array( 'wp-blocks', 'wp-components', 'wp-editor' ),
			$this->version
		);
		// Styles.
		wp_enqueue_style(
			$this->slug . '-editor',
			$this->config->style . 'editor.css',
			array( 'wp-edit-blocks' ),
			$this->version
		);
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since     1.0.0
	 * @access    public
	 */
	public function load_textdomain() {

		load_plugin_textdomain(
			'wp-block-boilerplate',
			false,
			$this->config->language_dir
		);

	}

	/**
	 * Add link to the  list of links to display on the plugins page.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @param    array  $links    Array of existing links.
	 * @param    string $file    Name of plugin file.
	 */
	public function plugin_support_link( $links, $file ) {

		if ( strpos( $file, basename( __FILE__ ) ) !== false ) {
			$new_links = array(
				'support' => '<a href=https://github.com/vurghus-minar/wp-block-boilerplate/issues>' . __( 'Support', 'wp-block-boilerplate' ) . '</a>',
			);

			$links = array_merge( $links, $new_links );
		}

		return $links;
	}

}

/**
 * Plugin configuration array
 */
$base_config = [
	'plugin_dir' => plugin_dir_path( __FILE__ ), // Get current plugin directory.
	'slug'       => 'wp-block-boilerplate', // Plugin slug for text domain, settings, prefixes etc.
	'url'        => untrailingslashit( plugins_url( '/', __FILE__ ) ), // Plugin url.
	'version'    => '1.0.0', // Plugin version.
];

/**
 * Instantiate plugin
 */
\AM\WpBlockBoilerplate\Plugin::init( $base_config );
