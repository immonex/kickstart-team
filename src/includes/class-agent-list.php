<?php
/**
 * Class Agent_List
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Agent CPT list rendering
 */
class Agent_List extends Base_CPT_List {

	/**
	 * Element base name
	 *
	 * @var string
	 */
	protected $base_name = 'agent';

	/**
	 * Related CPT name
	 *
	 * @var string
	 */
	protected $post_type_name = 'inx_agent';

	/**
	 * Create taxonomy and meta query arrays based on the given parameters.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $params Query parameters.
	 * @param bool    $apply_filters Flag to indicate whether the generated queries
	 *                               should be changeable (true by default).
	 *
	 * @return mixed[] Array with sub-arrays for tax and meta queries.
	 */
	public function get_tax_and_meta_queries( $params, $apply_filters = true ) {
		$prefix     = $this->config['public_prefix'];
		$inx_prefix = 'inx-';
		$queries    = parent::get_tax_and_meta_queries( $params, false );

		if ( empty( $queries['meta_query'] ) ) {
			$queries['meta_query'] = array( 'relation' => 'AND' );
		}

		foreach ( $params as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			switch ( $key ) {
				case "{$inx_prefix}agency":
					$queries['meta_query'][] = array(
						'key'     => '_inx_team_agency_id',
						'value'   => is_array( $value ) ? $value : array( $value ),
						'compare' => 'IN',
					);
					break;
			}
		}

		if ( $apply_filters ) {
			$queries = apply_filters(
				"inx_{$this->base_name}_list_tax_and_meta_queries",
				$queries,
				$params,
				$prefix,
				$inx_prefix
			);
		}

		return $queries;
	} // get_tax_and_meta_queries

} // Agent_List
