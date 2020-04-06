<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Product_Price_calculator extends WC_Product {

	public function __construct( $product ) {

		$this->product_type = 'price_calculator';

		parent::__construct( $product );

	}
}