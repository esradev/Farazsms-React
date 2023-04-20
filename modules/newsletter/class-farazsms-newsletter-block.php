<?php

/**
 * Farazsms newsletter block.
 *
 * @package Farazsms
 * @since 2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Farazsms_News_Block.
 */
class Farazsms_News_Block {
	/**
	 * Instance
	 *
	 * @access private
	 * @var object Class object.
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Initiator
	 *
	 * @return object Initialized object of class.
	 * @since 2.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'adminAssets' ] );
	}

	public function adminAssets() {
		wp_register_style( 'quizeditcss', FARAZSMS_URL . 'build-blocks/index.css' );
		wp_register_script( 'ournewblocktype', FARAZSMS_URL . 'build-blocks/index.js', [
			'wp-blocks',
			'wp-element',
			'wp-editor'
		] );
		register_block_type( 'farazsms/newsletter', [
			'editor_script'   => 'ournewblocktype',
			'editor_style'    => 'quizeditcss',
			'render_callback' => [ $this, 'theHTML' ]
		] );
	}

	public function theHTML( $attributes ) {
		if ( ! is_admin() ) {
			wp_enqueue_script( 'attentionFrontend', FARAZSMS_URL . 'build-blocks/frontend.js', [ 'wp-element' ] );
			wp_enqueue_style( 'attentionFrontendStyles', FARAZSMS_URL . 'build-blocks/frontend.css' );
		}

		ob_start(); ?>
		<div class="paying-attention-update-me">
			<pre style="display: none;"><?php echo esc_html(wp_json_encode( $attributes )) ?></pre>
		</div>
		<?php return ob_get_clean();
	}

}

Farazsms_News_Block::get_instance();
