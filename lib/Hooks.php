<?php
/**
 * Main plugin hooks.
 *
 * @author Iron Bound Designs
 * @since  1.0
 */

namespace ITETOS;

/**
 * Class Hooks
 * @package ITETOS
 */
class Hooks {

	/**
	 * Constructor.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		add_action( 'it_exchange_content_checkout_before_transaction_methods', array(
			$this,
			'add_terms_to_super_widget'
		) );

		add_action( 'it_exchange_content_checkout_before_actions', array(
			$this,
			'add_terms_to_checkout'
		) );

		add_action( 'wp_enqueue_scripts', array(
			$this,
			'scripts_and_styles'
		), 20 );

		add_filter( 'it_exchange_generate_transaction_object', array(
			$this,
			'add_terms_to_transaction_object'
		) );
	}

	/**
	 * Get the entire text of the terms of service.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	private static function get_tos() {

		$main = Settings::get( 'terms' );

		if ( trim( $main ) !== '' ) {
			$main = wpautop( $main );
		}

		foreach ( it_exchange_get_cart_products() as $product ) {
			if ( ! it_exchange_product_has_feature( $product['product_id'], 'terms-of-service' ) ) {
				continue;
			}

			$product = it_exchange_get_product( $product['product_id'] );

			$title = '<h5>' . $product->post_title . '</h5>';

			/**
			 * Filter the product heading section.
			 *
			 * By default this is the product title wrapped in H5 tags.
			 *
			 * @since 1.0
			 *
			 * @param string               $title
			 * @param \IT_Exchange_Product $product
			 */
			$title = apply_filters( 'itetos_product_heading', $title, $product );

			$main .= $title;

			$custom = it_exchange_get_product_feature( $product->ID, 'terms-of-service', array( 'field' => 'terms' ) );

			$custom = wpautop( $custom );

			/**
			 * Filter the terms for a certain product.
			 *
			 * @since 1.0
			 *
			 * @param string               $custom
			 * @param \IT_Exchange_Product $product
			 */
			$custom = apply_filters( 'itetos_product_terms', $custom, $product );

			$main .= $custom;
		}

		$main = trim( $main );

		/**
		 * Filter the entirety of the Terms of Service.
		 *
		 * @since 1.0
		 *
		 * @param string $main
		 */
		$main = apply_filters( 'itetos_terms', $main );

		return trim( $main );
	}

	/**
	 * Add our terms to the super widget.
	 *
	 * These are displayed before the transaction methods.
	 *
	 * @since 1.0
	 */
	public function add_terms_to_super_widget() {

		$tos = self::get_tos();

		if ( ! $tos ) {
			return;
		}

		?>

		<div class="terms-of-service-container">

			<p class="tos-agree-container">
				<input type="checkbox" id="agree-terms" value="agree">
				<label for="agree-terms"><?php echo $agree = Settings::get( 'label' ); ?></label>
			</p>

			<a href="javascript:" id="show-terms"><?php _e( "Show Terms", Plugin::SLUG ); ?></a>

			<div class="terms">
				<?php echo $tos; ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Add our terms to the checkout page.
	 *
	 * These are displayed before the transaction methods.
	 *
	 * @since 1.0
	 */
	public function add_terms_to_checkout() {

		$tos = self::get_tos();

		if ( ! $tos ) {
			return;
		}
		?>

		<div class="terms-of-service-container">

			<p class="tos-agree-container">
				<input type="checkbox" id="agree-terms" value="agree">
				<label for="agree-terms"><?php echo $agree = Settings::get( 'label' ); ?></label>
			</p>

			<a href="javascript:" id="show-terms"><?php _e( "Show Terms", Plugin::SLUG ); ?></a>

			<div class="terms">
				<?php echo $tos; ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Enqueue scripts and styles onto the front-end.
	 *
	 * @since 1.0
	 */
	public function scripts_and_styles() {

		$tos = self::get_tos();

		if ( ! $tos ) {
			return;
		}

		if ( it_exchange_in_superwidget() || it_exchange_is_page( 'product' ) ) {
			wp_enqueue_script( 'itetos-sw' );
			wp_enqueue_style( 'itetos-sw' );

			wp_localize_script( 'itetos-sw', 'ITETOS', array(
				'show' => __( "Show Terms", Plugin::SLUG ),
				'hide' => __( "Hide Terms", Plugin::SLUG )
			) );
		}

		if ( it_exchange_is_page( 'checkout' ) ) {
			wp_enqueue_script( 'itetos-checkout' );
			wp_enqueue_style( 'itetos-checkout' );

			wp_localize_script( 'itetos-checkout', 'ITETOS', array(
				'show' => __( "Show Terms", Plugin::SLUG ),
				'hide' => __( "Hide Terms", Plugin::SLUG )
			) );
		}
	}

	/**
	 * Save the entire Terms of Service to the transaction object on checkout.
	 *
	 * @since 1.0
	 *
	 * @param object $transaction_object
	 *
	 * @return object
	 */
	public function add_terms_to_transaction_object( $transaction_object ) {

		$tos = self::get_tos();

		if ( trim( $tos ) != '' ) {
			$transaction_object->terms_of_service = $tos;
		}

		return $transaction_object;
	}
}