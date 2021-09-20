<?php
/**
 * Abstract class Base_CPT_List
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Custom post type list rendering
 */
abstract class Base_CPT_List {

	/**
	 * Element base name
	 *
	 * @var string
	 */
	protected $base_name;

	/**
	 * Related CPT name
	 *
	 * @var string
	 */
	protected $post_type_name;

	/**
	 * Various component configuration data
	 *
	 * @var mixed[]
	 */
	protected $config;

	/**
	 * Helper/Utility objects
	 *
	 * @var object[]
	 */
	protected $utils;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config = $config;
		$this->utils  = $utils;
	} // __construct

	/**
	 * Render a CPT list (PHP template).
	 *
	 * @since 1.0.0
	 *
	 * @param string  $template Template file name (without suffix).
	 * @param mixed[] $atts Rendering attributes.
	 *
	 * @return string Rendered contents (HTML).
	 */
	public function render( $template = '', $atts = array() ) {
		if ( ! $template ) {
			$template = "{$this->base_name}-list/index";
		}

		if ( empty( $atts ) || ! is_array( $atts ) ) {
			$atts = array();
		}

		$org_query = $this->replace_main_query( $atts );

		$template_data = array_merge(
			$this->config,
			$atts
		);
		$output        = $this->utils['template']->render_php_template( $template, $template_data );

		// Restore the original query object.
		if ( $org_query ) {
			$this->restore_main_query( $org_query );
		}

		return $output;
	} // render

	/**
	 * Retrieve and return CPT posts.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $args Additional/Divergent WP query arguments (optional).
	 *
	 * @return \WP_Post[] Matching CPT post objects (if any).
	 */
	public function get_items( $args = array() ) {
		$args = array_merge(
			array(
				'post_type'                     => $this->post_type_name,
				'post_status'                   => array( 'publish' ),
				'posts_per_page'                => -1,
				'suppress_pre_get_posts_filter' => true,
			),
			$args
		);

		return get_posts( $args );
	} // get_items

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

		$tax_query  = array( 'relation' => 'AND' );
		$meta_query = array( 'relation' => 'AND' );

		$special_flags = array( 'demo' );

		foreach ( $special_flags as $flag ) {
			$flag_key = "{$inx_prefix}{$flag}";

			if ( isset( $params[ $flag_key ] ) ) {
				switch ( strtolower( $params[ $flag_key ] ) ) {
					case 'only':
						$meta_query[] = array(
							'key'     => "_immonex_is_{$flag}",
							'value'   => array( 1, 'on' ),
							'compare' => 'IN',
						);
						break;
					case 'no':
						$meta_query[] = array(
							'relation' => 'OR',
							array(
								'key'     => "_immonex_is_{$flag}",
								'compare' => 'NOT EXISTS',
							),
							array(
								'key'     => "_immonex_is_{$flag}",
								'value'   => array( 0, 'off', '' ),
								'compare' => 'IN',
							),
						);
						break;
				}
			}
		}

		$queries = array(
			'tax_query'  => count( $tax_query ) > 1 ? $tax_query : false,
			'meta_query' => count( $meta_query ) > 1 ? $meta_query : false,
		);

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

	/**
	 * Replace and return the current original WP main query before rendering.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $atts Rendering Attributes.
	 *
	 * @return \WP_Query Original WP main query object.
	 */
	private function replace_main_query( $atts ) {
		if ( ! empty( $atts['is_regular_archive_page'] ) ) {
			return false;
		}

		global $wp_query;

		$query_vars = array();

		// Inherit plugin related variables of main query.
		foreach ( $wp_query->query_vars as $key => $value ) {
			if ( substr( $key, 0, strlen( $this->config['public_prefix'] ) ) === $this->config['public_prefix'] ) {
				$query_vars[ $key ] = $value;
			}
		}

		if ( is_array( $atts ) && count( $atts ) > 0 ) {
			foreach ( $atts as $key => $value ) {
				if ( $value ) {
					$query_vars[ $key ] = $value;
				}
			}
		}

		$paged = 1;
		if ( empty( $atts['inx-ignore-pagination'] ) ) {
			$paged = get_query_var( 'paged' );
			if ( ! $paged ) {
				$paged = get_query_var( 'page' );
			}
		}

		$args = array_merge(
			array(
				'post_type'   => $this->post_type_name,
				'post_status' => array( 'publish' ),
				'paged'       => $paged,
			),
			$query_vars
		);

		$cpt_query = new \WP_Query( $args );
		$org_query = $wp_query;
		// @codingStandardsIgnoreStart
		$wp_query  = null;
		$wp_query  = $cpt_query;
		// @codingStandardsIgnoreEnd

		return $org_query;
	} // replace_main_query

	/**
	 * Restore the original WP main query after rendering.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Query $org_query Original WP main query object.
	 */
	private function restore_main_query( $org_query ) {
		global $wp_query;

		wp_reset_postdata();
		// @codingStandardsIgnoreStart
		$wp_query = null;
		$wp_query = $org_query;
		// @codingStandardsIgnoreEnd
	} // restore_main_query

} // Base_CPT_List
