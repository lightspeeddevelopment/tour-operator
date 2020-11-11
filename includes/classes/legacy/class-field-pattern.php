<?php
/**
 * CMB Field Patterns for the LSX Tour Operator Plugin
 *
 * @package   Field_Pattern
 * @author    LightSpeed
 * @license   GPL3
 * @link
 * @copyright 2016 LightSpeedDevelopment
 */

namespace lsx\legacy;

/**
 * Main plugin class.
 *
 * @package Field_Pattern
 * @author  LightSpeed
 */
class Field_Pattern {

	/**
	 * Initialize the plugin by setting localization, filters, and
	 * administration functions.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	public function __construct() {

	}

	/**
	 * Returns the fields needed for a videos, repeatable box.
	 */
	public static function price() {
		return apply_filters(
			'lsx_to_price_field_pattern',
			array(
				array(
					'id'   => 'price',
					'name' => 'Price',
					'type' => 'text',
				),
			)
		);
	}

}
