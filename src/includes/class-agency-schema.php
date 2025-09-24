<?php
/**
 * Class Agency_Schema
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Schema.org related processing of agency data.
 */
class Agency_Schema extends Base_Schema {

	const ENTITY_TYPE         = 'agency';
	const ENTITY_SCHEMA_TYPES = [ 'RealEstateAgent' ];

	/**
	 * Generate and return the "main entity" element of the current agency.
	 *
	 * @since 1.7.0-beta
	 *
	 * @param string $scope Optional scope: "full" (default) for a complete
	 *                      data set for detail page embedding, "reference" for
	 *                      ID/URL only (e.g. referencing further data in external
	 *                      pages).
	 *
	 *                      In the latter case, the URL must be determinable.
	 *
	 * @return mixed[]|string Main entity element (or empty array if not indeterminable).
	 */
	public function get_main_entity_element( $scope = 'full' ): array {
		if ( empty( $this->post ) ) {
			return [];
		}

		if ( ! empty( $this->main_entity_element[ $scope ] ) ) {
			return [
				'raw'          => $this->main_entity_element[ $scope ],
				'script_block' => $this->get_json_ld_script_block( $this->main_entity_element[ $scope ] ),
			];
		}

		$agency_data = $this->get_entity_data();
		if ( empty( $agency_data ) ) {
			return [];
		}

		$agency_post_type_name = isset( $this->config['custom_post_types']['agency'] ) ?
			$this->config['custom_post_types']['agency']['post_type_name'] :
			'';

		if ( ! $agency_post_type_name ) {
			return [];
		}

		// Internal filter.
		$agency_schema_types = apply_filters( 'inx_team_agency_schema_types', self::ENTITY_SCHEMA_TYPES );

		$url = $agency_data['is_public'] ? $agency_data['permalink_url'] : $agency_data['elements']['url']['value'];
		if ( ! $url && 1 === wp_count_posts( $agency_post_type_name )->publish ) {
			// The home URL may be used if there is only one agency and no specific URL is set.
			$url = home_url();
		}

		$entity        = 'reference' === $scope ? [] : [ '@type' => $agency_schema_types ];
		$entity['@id'] = $this->get_schema_id( $url ? $url : $this->post->ID, $agency_schema_types );
		if ( $url ) {
			$entity['url'] = $url;
		}

		if ( 'reference' === $scope && $agency_data['is_public'] ) {
			$this->main_entity_element[ $scope ] = [
				'raw'          => $entity,
				'script_block' => $this->get_json_ld_script_block( $entity ),
			];

			return $this->main_entity_element[ $scope ];
		}

		$featured_image = get_the_post_thumbnail_url( $this->post->ID, 'full' );
		$address        = $this->get_address_element( $url ? $url : $this->post->ID );
		$geo            = $this->get_geo_element( $url ? $url : $this->post->ID );
		$network_urls   = $this->get_network_urls();

		$entity = array_merge(
			$entity,
			[
				'name'        => $this->post->post_title,
				'description' => wp_strip_all_tags( $this->post->post_content ),
				'logo'        => $featured_image ? $featured_image : null,
				'image'       => $featured_image ? $featured_image : null,
				'address'     => ! empty( $address ) ? $address : null,
				'geo'         => ! empty( $geo ) ? $geo : null,
				'email'       => ! empty( $agency_data['elements']['email']['value'] ) ?
					$agency_data['elements']['email']['value'] : null,
				'telephone'   => ! empty( $agency_data['elements']['phone']['value'] ) ?
					$agency_data['elements']['phone']['value'] : null,
				'sameAs'      => ! empty( $network_urls ) ? $network_urls : null,
			]
		);

		$entity = array_filter( $entity );

		$this->main_entity_element[ $scope ] = [
			'raw'          => $entity,
			'script_block' => $this->get_json_ld_script_block( $entity ),
		];

		return $this->main_entity_element[ $scope ];
	} // get_main_entity_element

} // Agency_Schema
