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

		foreach ( it_exchange_get_cart_products() as $product ) {
			if ( ! it_exchange_product_has_feature( $product['product_id'], 'terms-of-service' ) ) {
				continue;
			}

			$main .= '<h4>' . it_exchange_get_product( $product['product_id'] )->post_title . '</h4>';
			$main .= it_exchange_get_product_feature( $product['product_id'], 'terms-of-service', array( 'field' => 'terms' ) );
		}

		return $main;
	}
}