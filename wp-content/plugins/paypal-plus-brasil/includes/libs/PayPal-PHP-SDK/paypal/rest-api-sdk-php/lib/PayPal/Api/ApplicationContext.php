<?php

namespace PayPal\Api;

use PayPal\Common\PayPalModel;

/**
 * Class ApplicationContext
 *
 * application context.
 *
 * @package PayPal\Api
 *
 * @property string brand_name
 * @property string shipping_preference
 */
class ApplicationContext extends PayPalModel {

	public function setBrandName( $brand_name ) {
		$this->brand_name = $brand_name;
	}

	public function setShippingPreference( $shipping_preference = 'SET_PROVIDED_ADDRESS' ) {
		$this->shipping_preference = $shipping_preference;
	}

}
