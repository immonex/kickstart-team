<?php
/**
 * Abstract class CPT_List_Hooks
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * CPT list related actions, filters and shortcodes
 */
abstract class Base_CPT_List_Hooks {

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
	 * Related CPT list object
	 *
	 * @var \immonex\Kickstart\Team\Base_CPT_List
	 */
	protected $cpt_list;

	/**
	 * Supported shortcode atts
	 *
	 * @var object[]
	 */
	protected $supported_shortcode_atts = array(
		'inx-author'            => false,
		'inx-limit'             => false,
		'inx-limit-page'        => false,
		'inx-ignore-pagination' => false,
		'inx-order'             => false,
		'inx-demo'              => 'yes',
	);

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$plugin_slug         = $config['plugin_slug'];
		$plugin_prefix       = $config['plugin_prefix'];
		$public_prefix       = $config['public_prefix'];
		$cpt_list_class_name = __NAMESPACE__ . '\\' . $config['class_base_name'] . '_List';
		$this->config        = $config;
		$this->utils         = $utils;
		$this->cpt_list      = new $cpt_list_class_name( $this->config, $utils );

		/**
		 * WP actions and filters
		 */

		add_action( 'pre_get_posts', array( $this, 'adjust_frontend_query' ) );

		/**
		 * Plugin-specific actions and filters
		 */

		add_action( "{$plugin_prefix}render_{$this->base_name}_list", array( $this, 'render_list' ), 10 );

		/**
		 * Shortcodes
		 */

		if ( ! empty( $this->config['has_shortcode'] ) ) {
			add_shortcode( "{$public_prefix}{$this->base_name}-list", array( $this, 'shortcode_list' ) );
		}
	} // __construct

	/**
	 * Adjust frontend related CPT WP queries.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Query $query WP query object.
	 */
	public function adjust_frontend_query( $query ) {
		if (
			! empty( $query->query_vars['suppress_pre_get_posts_filter'] )
			|| ! empty( $query->query_vars['page'] )
			|| ! empty( $query->query_vars['pagename'] )
			|| is_admin()
			|| (
				$query->get( 'post_type' ) &&
				$query->get( 'post_type' ) !== $this->post_type_name
			)
		) {
			return;
		}

		$inx_prefix = 'inx-';

		/**
		 * Supported query params.
		 */

		$var_names = array_keys( $this->supported_shortcode_atts );

		/**
		 * Retrieve query variable values.
		 */

		$search_query_vars = array();

		if ( count( $var_names ) > 0 ) {
			foreach ( $var_names as $var_name ) {
				$value                          = apply_filters( 'inx_get_query_var_value', false, $var_name, $query );
				$search_query_vars[ $var_name ] = $value;
			}
		}

		if ( ! empty( $search_query_vars[ "{$inx_prefix}author" ] ) ) {
			$author_query = apply_filters( 'inx_get_author_query', $search_query_vars[ "{$inx_prefix}author" ] );

			if ( $author_query ) {
				$query->set( $author_query['type'], $author_query['user_ids'] );
			}
		}

		if ( is_callable( array( $this->cpt_list, 'get_tax_and_meta_queries' ) ) ) {
			$tax_and_meta_queries = $this->cpt_list->get_tax_and_meta_queries( $search_query_vars );
			if ( $tax_and_meta_queries['tax_query'] ) {
				$query->set( 'tax_query', $tax_and_meta_queries['tax_query'] );
			}
			if ( $tax_and_meta_queries['meta_query'] ) {
				$query->set( 'meta_query', $tax_and_meta_queries['meta_query'] );
			}
		}

		if (
			isset( $search_query_vars[ "{$inx_prefix}limit" ] )
			&& (int) $search_query_vars[ "{$inx_prefix}limit" ]
		) {
			$hard_limit = (int) $search_query_vars[ "{$inx_prefix}limit" ];
		} else {
			$hard_limit = false;
		}

		if (
			isset( $search_query_vars[ "{$inx_prefix}limit-page" ] )
			&& (int) $search_query_vars[ "{$inx_prefix}limit-page" ] ) {
			$page_limit = (int) $search_query_vars[ "{$inx_prefix}limit-page" ];
		} else {
			$page_limit = false;
		}

		if ( $hard_limit || $page_limit ) {
			$query->set( 'posts_per_page', $hard_limit ? $hard_limit : $page_limit );
			if ( $hard_limit ) {
				// Disable pagination if a "hard" post number limit is given.
				$query->set( 'no_found_rows', true );
			}
		}

		$query->set( 'order', 'ASC' );
		if ( ! empty( $search_query_vars[ "{$inx_prefix}order" ] ) ) {
			$order = explode( ' ', $search_query_vars[ "{$inx_prefix}order" ] );
			$query->set( 'orderby', $order[0] );
			if (
				isset( $order[1] )
				&& 'DESC' === strtoupper( $order[1] )
			) {
				$query->set( 'order', strtoupper( $order[1] ) );
			}
		} else {
			// Use titles as default sort order.
			$query->set( 'orderby', 'title' );
		}

		return $query;
	} // adjust_frontend_query

	/**
	 * Display the rendered CPT list (action based).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $atts Rendering attributes.
	 */
	public function render_list( $atts = array() ) {
		$template = isset( $atts['template'] ) && $atts['template'] ?
			$atts['template'] :
			"{$this->base_name}-list/index";

		echo $this->cpt_list->render( $template, $atts );
	} // render_list

	/**
	 * Return a rendered CPT post list (shortcode-based).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $atts Rendering Attributes.
	 *
	 * @return string Rendered shortcode contents.
	 */
	public function shortcode_list( $atts ) {
		if ( is_admin() ) {
			return '';
		}

		if ( ! empty( $atts['template'] ) ) {
			$template = $atts['template'];
		} else {
			$template = "{$this->base_name}-list/index";
		}

		$prefix         = $this->config['public_prefix'];
		$supported_atts = $this->supported_shortcode_atts;

		// Add prefixes to user shortcode attributes.
		$prefixed_atts = array();
		if ( is_array( $atts ) && count( $atts ) > 0 ) {
			foreach ( $atts as $key => $value ) {
				if ( isset( $supported_atts[ "inx-{$key}" ] ) ) {
					$prefixed_atts[ "inx-{$key}" ] = $value;
				}
			}
		}
		$shortcode_atts = shortcode_atts( $supported_atts, $prefixed_atts, "{$prefix}{$this->base_name}-list" );

		return $this->cpt_list->render( $template, $shortcode_atts );
	} // shortcode_list

} // Base_CPT_List_Hooks
