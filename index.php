<?php
/**
 * Plugin Name: First block plugin
 * Plugin URI:  https://github.com/vurghus-minar/
 * Description: First block plugin.
 * Version:     1.0.0
 * Author:      Vurghus Minar
 * Author URI:  https://github.com/vurghus-minar
 * License:     MIT
 * License URI: https://opensource.org/licenses/MIT
 */
class First_Block {
	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      object   $instance   A single instance of this class.
	 */
	private static $instance;
	/**
	 * The slug of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $slug    The plugin slug.
	 */
	private $slug;
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
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	/**
	 * Registers the plugin.
	 *
	 * @since     1.0.0
	 * @access    public
	 * @return    object    A single instance of this class.
	 */
	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new First_Block();
		}
	}
	/**
	 * Defines the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function __construct() {
		$this->version = '1.0.0';
		$this->slug    = 'first-block';
		$this->url     = untrailingslashit( plugins_url( '/', __FILE__ ) );
		add_action( 'init', array( $this, 'init_block' ) );
	}
	/**
	 * Initializes the block.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function init_block() {
		if ( function_exists( 'register_block_type' ) ) {
			register_block_type(
				'first-block/my-first-block',
				array(
					'editor_script'   => $this->slug,
					'editor_style'    => $this->slug . '-editor',
					'render_callback' => array( $this, 'render_block' ),
					'style'           => $this->slug,
				)
			);
			add_action( 'enqueue_block_assets', array( $this, 'enqueue_block_assets' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
		}
	}
	/**
	 * Renders the block.
	 *
	 * @since  1.0.0
	 * @param  array  $attributes Block attributes.
	 * @param  string $content    Block inner content.
	 * @return string Markup.
	 * @access public
	 */
	public function render_block( $attributes, $content ) {
		if ( ! empty( $content ) ) {
			return $content;
		}
		if ( in_the_loop() ) {
			$title = $this->get_post_meta( 'first_block_title' );

			if ( isset( $attributes ) && isset( $attributes['backgroundColor'] ) ) {
				$background_color = $attributes['backgroundColor'];
			} else {
				$background_color = '';
			}
			ob_start();
			include 'partials/first-block.php';
			return ob_get_clean();
		}
	}
	/**
	 * Enqueues block assets for use within Gutenberg.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_block_editor_assets() {
		// Scripts
		wp_enqueue_script(
			$this->slug,
			$this->url . '/script.js',
			array( 'wp-blocks', 'wp-components', 'wp-editor' ),
			$this->version
		);
		// Styles
		wp_enqueue_style(
			$this->slug . '-editor',
			$this->url . '/editor.css',
			array( 'wp-edit-blocks' ),
			$this->version
		);
	}
	/**
	 * Enqueues block assets for use within Gutenberg, as well as on the front-end.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function enqueue_block_assets() {
		// Styles
		wp_enqueue_style(
			$this->slug,
			$this->url . '/style.css',
			array(),
			$this->version
		);
	}

	/**
	 * Registers a meta field.
	 *
	 * @since  1.0.0
	 * @param  string $meta_key   Meta field key.
	 * @param  bool   $is_integer true if the value of the meta field is an integer, false otherwise.
	 * @access private
	 */
	private function register_meta_field( $meta_key ) {
		register_meta(
			'post',
			$meta_key,
			array(
				'show_in_rest' => true,
				'single'       => true,
			)
		);
	}
	/**
	 * Retrieves post meta field.
	 *
	 * @since  1.0.0
	 * @param  string $meta_key Meta field key.
	 * @access private
	 */
	private function get_post_meta( $meta_key ) {
		return apply_filters(
			$meta_key,
			get_post_meta( get_the_ID(), $meta_key, true ),
			get_the_ID()
		);
	}

}

First_Block::init();
