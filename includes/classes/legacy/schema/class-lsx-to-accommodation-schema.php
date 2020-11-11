<?php
/**
 * The Trip Schema for Tours
 *
 * @package tour-operator
 */

/**
 * Returns schema Review data.
 *
 * @since 10.2
 */
class LSX_TO_Accommodation_Schema extends LSX_TO_Schema_Graph_Piece {

	/**
	 * Constructor.
	 *
	 * @param \WPSEO_Schema_Context $context A value object with context variables.
	 */
	public function __construct( WPSEO_Schema_Context $context ) {
		$this->post_type = 'accommodation';
		parent::__construct( $context );
	}

	/**
	 * Returns Accommodation / Product data.
	 *
	 * @return array $data Review data.
	 */
	public function generate() {
		$data = array(
			'@type'            => array(
				'Accommodation',
				'Product',
			),
			'@id'              => $this->context->canonical . '#accommodation',
			'name'             => $this->post->post_title,
			'description'      => wp_strip_all_tags( $this->post->post_content ),
			'url'              => $this->post_url,
			'mainEntityOfPage' => array(
				'@id' => $this->context->canonical . WPSEO_Schema_IDs::WEBPAGE_HASH,
			),
			'mpn'              => $this->context->id,
		);

		if ( $this->context->site_represents_reference ) {
			$data['brand'] = $this->context->site_represents_reference;
		}

		$wetu_ref = get_post_meta( $this->context->id, 'lsx_wetu_id', true );
		if ( false !== $wetu_ref && '' !== $wetu_ref ) {
			$data['sku'] = $wetu_ref;
		}
		$data = $this->add_destinations( $data );
		$data = \lsx\legacy\Schema_Utils::add_image( $data, $this->context );
		$data = $this->add_offers( $data );
		$data = $this->add_reviews( $data );
		$data = $this->add_articles( $data );
		$data = $this->add_custom_field( $data, 'numberOfRooms', 'number_of_rooms' );
		$data = $this->add_taxonomy_terms( $data, 'amenityFeature', 'facility' );
		$data = $this->add_location( $data );
		return $data;
	}
}
