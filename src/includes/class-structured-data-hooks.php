<?php
/**
 * Class Structured_Data_Hooks
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Structured Data embedding (e.g. Schema.org).
 */
class Structured_Data_Hooks {

	/**
	 * Plugin options and other component configuration data
	 *
	 * @var mixed[]
	 */
	private $config;

	/**
	 * Helper/Utility objects
	 *
	 * @var object[]
	 */
	private $utils;

	/**
	 * Agency/Agent schema data cache
	 *
	 * @var mixed[]
	 */
	private $cache = [
		'agency' => [
			'internal'  => [],
			'reference' => [],
			'instance'  => [],
		],
		'agent'  => [
			'internal'  => [],
			'reference' => [],
			'instance'  => [],
		],
	];

	/**
	 * Constructor
	 *
	 * @since 1.7.0-beta
	 *
	 * @param mixed[]  $config Plugin options and other component configuration data.
	 * @param object[] $utils  Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config = $config;
		$this->utils  = $utils;

		/**
		 * Kickstart Core
		 */

		add_filter( 'inx_enable_doc_head_buffering', '__return_true' );
		add_filter( 'inx_doc_head_contents', [ $this, 'maybe_extend_doc_head' ], 20 );

		/**
		 * Plugin-specific actions and filters
		 */

		add_action( 'inx_team_before_render_agency_list_item', [ $this, 'add_list_item_schema_data' ] );
		add_action( 'inx_team_before_render_agent_list_item', [ $this, 'add_list_item_schema_data' ] );

		// Internal filter.
		add_filter( 'inx_team_get_schema_data', [ $this, 'get_schema_data' ], 10, 2 );
	} // __construct

	/**
	 * Retrieve, cache and return agency/agent schema data (filter callback).
	 *
	 * @since 1.7.0-beta
	 *
	 * @param mixed[] $schema_data Empty array.
	 * @param mixed[] $args        Retrieval/Return arguments:
	 *                               - entity_type (agency/agent)
	 *                               - scope (full/extended/reference)
	 *                               - entity_id OR property_id
	 *                               - as_script_block (true/false).
	 *
	 * @return mixed[]|string Main agency/agent schema entity element of the given type
	 *                        as raw data array or "ready-rendered" JS block.
	 */
	public function get_schema_data( $schema_data, $args ) {
		$entity_type = ! empty( $args['entity_type'] ) && in_array( $args['entity_type'], [ 'agency', 'agent' ], true ) ?
			$args['entity_type'] : 'agency';
		$entity_id   = ! empty( $args['entity_id'] ) ? $args['entity_id'] : 0;
		$scope       = ! empty( $args['scope'] ) && in_array( $args['scope'], [ 'full', 'extended', 'reference' ], true ) ?
			$args['scope'] : 'reference';

		if ( ! $entity_id && ! empty( $args['property_id'] ) ) {
			$entity_type_id_key_part = 'agent' === $entity_type ? 'agent_primary' : "{$entity_type}_id";
			$entity_id               = get_post_meta( $args['property_id'], "_inx_team_{$entity_type_id_key_part}", true );
		}

		if ( ! $entity_id ) {
			return ! empty( $args['as_script_block'] ) ? '' : [];
		}

		if ( ! empty( $this->cache[ $entity_type ][ $scope ][ $entity_id ] ) ) {
			return empty( $args['as_script_block'] ) ?
				$this->cache[ $entity_type ][ $scope ][ $entity_id ]['raw'] :
				$this->cache[ $entity_type ][ $scope ][ $entity_id ]['script_block'];
		}

		$transient_name = "inx_team_{$entity_type}_schema_{$scope}_{$entity_id}";
		$transient_data = get_transient( $transient_name );

		if (
			! empty( $transient_data['raw'] )
			&& ! empty( ! empty( $transient_data['script_block'] ) )
		) {
			$this->cache[ $entity_type ][ $scope ][ $entity_id ] = $transient_data;

			return empty( $args['as_script_block'] ) ?
				$transient_data['raw'] :
				$transient_data['script_block'];
		}

		if ( ! empty( $this->cache[ $entity_type ]['instance'][ $entity_id ] ) ) {
			$entity_schema = $this->cache[ $entity_type ]['instance'][ $entity_id ];
		} else {
			$entity_class  = __NAMESPACE__ . '\\' . ucfirst( $entity_type ) . '_Schema';
			$entity_schema = new $entity_class( $this->config, $this->utils );
			$entity_schema->set_post_id( $entity_id );

			$this->cache[ $entity_type ]['instance'][ $entity_id ] = $entity_schema;
		}

		$main_entity_element = $entity_schema->get_main_entity_element( $scope );
		if ( empty( $main_entity_element ) ) {
			return ! empty( $args['as_script_block'] ) ? '' : [];
		}

		$this->cache[ $entity_type ][ $scope ][ $entity_id ] = $main_entity_element;

		$transient_expiration = apply_filters(
			'inx_team_schema_data_transient_expiration',
			MONTH_IN_SECONDS * 2,
			$entity_type,
			$scope
		);
		set_transient( $transient_name, $main_entity_element, $transient_expiration );

		return empty( $args['as_script_block'] ) ?
			$this->cache[ $entity_type ][ $scope ][ $entity_id ]['raw'] :
			$this->cache[ $entity_type ][ $scope ][ $entity_id ]['script_block'];
	} // get_schema_data

	/**
	 * Maybe extend the given HTML head contents by a structured data script block
	 * (filter callback).
	 *
	 * @since 1.7.0-beta
	 *
	 * @param string $head_contents Content string.
	 *
	 * @return string Possibly extended head contents.
	 */
	public function maybe_extend_doc_head( $head_contents ): string {
		$type        = '';
		$struct_data = '';

		foreach ( $this->config['custom_post_types'] as $key => $cpt ) {
			$type = is_post_type_archive( $cpt['post_type_name'] ) ?
				"{$key}_archive" : '';

			// @codingStandardsIgnoreStart
			if (
				! $type
				&& get_post_type() === $cpt['post_type_name']
				&& apply_filters( "{$cpt['post_type_name']}_has_single_view", $this->config["enable_{$key}_single_view"] )
			) {
				$type = "{$key}_single";
			}
			// @codingStandardsIgnoreEnd

			if ( $type ) {
				break;
			}
		}

		if ( ! $type ) {
			return $head_contents;
		}

		switch ( $type ) {
			case 'agency_single':
				$agency_schema = new Agency_Schema( $this->config, $this->utils );
				$agency_schema->set_post_id( get_the_ID() );
				$struct_data = $agency_schema->get_detail_page_graph( true );
				break;
			case 'agent_single':
				$agent_schema = new Agent_Schema( $this->config, $this->utils );
				$agent_schema->set_post_id( get_the_ID() );
				$struct_data = $agent_schema->get_detail_page_graph( true );
				break;
		}

		return $head_contents . $struct_data;
	} // maybe_extend_doc_head

	/**
	 * Embed agency/agent schema data as JSON-LD block before rendering the related
	 * HTML element (action callback).
	 *
	 * @since 1.7.0-beta2
	 */
	public function add_list_item_schema_data(): void {
		foreach ( $this->config['custom_post_types'] as $key => $cpt ) {
			if ( get_post_type() === $cpt['post_type_name'] ) {
				echo apply_filters(
					'inx_team_get_schema_data',
					'',
					[
						'entity_type'     => $key,
						'entity_id'       => get_the_ID(),
						'scope'           => 'full',
						'as_script_block' => true,
					]
				);

				break;
			}
		}
	} // add_list_item_schema_data

} // Structured_Data_Hooks
