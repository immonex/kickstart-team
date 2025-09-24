<?php
/**
 * Class Agent_Schema
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Schema.org related processing of agent data.
 */
class Agent_Schema extends Base_Schema {

	const ENTITY_TYPE         = 'agent';
	const ENTITY_SCHEMA_TYPES = [ 'Person' ];

	/**
	 * Generate and return the "main entity" element of the current agent.
	 *
	 * @since 1.7.0-beta
	 *
	 * @param string $scope Optional scope:
	 *                        - full (default): complete data set incl. agent/agency
	 *                        - extended: like "full", but with agency references (ID/URL only)
	 *                        - reference: ID/URL only
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

		$agent_data = $this->get_entity_data();
		if ( empty( $agent_data ) ) {
			return [];
		}

		$agent_post_type_name = isset( $this->config['custom_post_types']['agent'] ) ?
			$this->config['custom_post_types']['agent']['post_type_name'] :
			'';

		if ( ! $agent_post_type_name ) {
			return [];
		}

		// Internal filter.
		$agent_schema_types = apply_filters( 'inx_team_agent_schema_types', self::ENTITY_SCHEMA_TYPES );

		$url = $agent_data['is_public'] ? $agent_data['permalink_url'] : $agent_data['elements']['url']['value'];

		$entity        = 'reference' === $scope ? [] : [ '@type' => $agent_schema_types ];
		$entity['@id'] = $this->get_schema_id( $url ? $url : $this->post->ID, $agent_schema_types );
		if ( $url ) {
			$entity['url'] = $url;
		}

		if ( 'reference' === $scope && $agent_data['is_public'] ) {
			$this->main_entity_element[ $scope ] = [
				'raw'          => $entity,
				'script_block' => $this->get_json_ld_script_block( $entity ),
			];
			return $this->main_entity_element[ $scope ];
		}

		$featured_image = get_the_post_thumbnail_url( $this->post->ID, 'full' );
		$address        = $this->get_address_element( $url ? $url : $this->post->ID );
		$geo            = in_array( 'RealEstateAgent', $agent_schema_types, true ) ?
			$this->get_geo_element( $url ? $url : $this->post->ID ) : [];
		$network_urls   = $this->get_network_urls();

		if ( $agent_data['agency_id'] ) {
			$agency_element = apply_filters(
				'inx_team_get_schema_data',
				[],
				[
					'entity_type' => 'agency',
					'entity_id'   => $agent_data['agency_id'],
					'scope'       => 'extended' === $scope ? 'reference' : 'full',
				]
			);
		}

		$entity = array_merge(
			$entity,
			[
				'name'        => $this->post->post_title,
				'givenName'   => $agent_data['elements']['first_name']['value'],
				'familyName'  => $agent_data['elements']['last_name']['value'],
				'description' => wp_strip_all_tags( $this->post->post_content ),
				'image'       => $featured_image ? $featured_image : null,
				'address'     => ! empty( $address ) ? $address : null,
				'geo'         => ! empty( $geo ) ? $geo : null,
				'email'       => ! empty( $agent_data['elements']['email']['value'] ) ?
					$agent_data['elements']['email']['value'] : null,
				'telephone'   => ! empty( $agent_data['elements']['phone']['value'] ) ?
					$agent_data['elements']['phone']['value'] : null,
				'worksFor'    => ! empty( $agency_element ) ? $agency_element : null,
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

} // Agent_Schema
