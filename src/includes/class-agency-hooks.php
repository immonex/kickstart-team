<?php
/**
 * Class Agency_Hooks
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

use \immonex\Kickstart\Kickstart;

/**
 * Agency CPT related actions and filters
 */
class Agency_Hooks extends Base_CPT_Hooks {

	/**
	 * Element base name
	 *
	 * @var string
	 */
	protected $base_name = 'agency';

	/**
	 * Related CPT name
	 *
	 * @var string
	 */
	protected $post_type_name = 'inx_agency';

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		parent::__construct( $config, $utils );

		// Hook save_post instead of save_post_inx_agency due to CMB2 priority issue.
		add_action( 'save_post', array( $this, 'maybe_update_post_title' ), 90, 3 );
		add_action( 'save_post', array( $this, 'update_property_agency_ids' ), 90, 3 );
		add_action( 'deleted_post', array( $this, 'remove_outdated_agency_ids' ) );
		add_action( 'template_redirect', array( $this, 'prevent_page_param_redirect' ), 0 );

		add_filter( 'inx_special_query_vars', array( $this, 'add_agency_query_var' ), 10, 2 );
		add_filter( 'inx_search_tax_and_meta_queries', array( $this, 'maybe_add_agency_query' ), 10, 3 );

		// Filters for "manually" creating and updating agencies.
		add_filter( 'inx_team_create_agency', array( $this, 'create_agency' ), 10, 2 );
		add_filter( 'inx_team_update_agency', array( $this, 'update_agency' ), 10, 3 );

		/**
		 * Shortcodes
		 */

		add_shortcode( 'inx-team-agency', array( $this, 'shortcode_agency' ) );
	} // __construct

	/**
	 * Create a new agency post and return its ID (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param null    $agency_id Agency ID (return value only).
	 * @param mixed[] $data Agency data.
	 *
	 * @return int|string|bool|\WP_Error ID of created agency post, false if required
	 *                                   data are missing or WP_Error object on failure.
	 */
	public function create_agency( $agency_id, $data ) {
		if ( empty( $data['post_title'] ) && empty( $data['company'] ) ) {
			return false;
		}

		$initial_agency_post_data = array(
			'post_title' => ! empty( $data['post_title'] ) ? $data['post_title'] : $data['company'],
		);

		$post_author = false;
		if ( ! empty( $data['post_author'] ) ) {
			$post_author = $data['post_author'];
		} elseif ( ! empty( $data['user_id'] ) ) {
			$post_author = $data['user_id'];
		}

		if ( $post_author && get_user_by( 'id', (int) $post_author ) ) {
			$initial_agency_post_data['post_author'] = (int) $post_author;
		}

		$agency_prefix       = '_' . $this->config['plugin_prefix'] . 'agency_';
		$initial_agency_meta = array(
			'_immonex_import_folder'      => ! empty( $data['import_folder'] ) ? $data['import_folder'] : 'global',
			"{$agency_prefix}auto_update" => isset( $data['auto_update'] ) ? (bool) $data['auto_update'] : true,
		);

		$agency    = $this->get_post_instance();
		$agency_id = $agency->create(
			$initial_agency_post_data,
			$initial_agency_meta
		);

		if ( $agency_id ) {
			if ( ! isset( $data['address_publishing_approved'] ) ) {
				$data['address_publishing_approved'] = '1';
			}

			$agency->update( $data );
		}

		return $agency_id;
	} // create_agency

	/**
	 * Update an agency (to be used directly AND as filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $agency_id ID of the agency post to be updated.
	 * @param mixed[]    $data Agency data.
	 * @param bool       $is_auto_update Flag for determining if the current
	 *                                   operation is an auto-update (optional,
	 *                                   false by default).
	 *
	 * @return int|string|bool ID of the updated agency post, false if not
	 *                         existent or -1 on auto-updates if these are
	 *                         disabled for the given post.
	 */
	public function update_agency( $agency_id, $data, $is_auto_update = false ) {
		$agency_prefix = '_' . $this->config['plugin_prefix'] . 'agency_';
		$agency        = $this->get_post_instance( $agency_id );

		if ( ! isset( $agency->post->ID ) ) {
			return false;
		}

		if (
			$is_auto_update
			&& ! get_post_meta( $agency_id, "{$agency_prefix}auto_update", true )
		) {
			return -1;
		}

		$agency->update( $data );

		return $agency_id;
	} // update_agency

	/**
	 * Add special query variables related to agencies (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $special_query_vars Prefixed variable names.
	 * @param string   $inx_prefix Kickstart prefix (normally "inx").
	 *
	 * @return string[] Query variables.
	 */
	public function add_agency_query_var( $special_query_vars, $inx_prefix ) {
		$special_query_vars[] = "{$inx_prefix}agency";

		return $special_query_vars;
	} // add_agency_query_var

	/**
	 * Add an agency related meta query if the related variable/parameter is set
	 * (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $queries Current queries.
	 * @param mixed[] $params Extended query parameters.
	 * @param string  $inx_prefix Kickstart prefix (normally "inx").
	 *
	 * @return string[] Extended queries or original value if agency parameter
	 *                  ist not set.
	 */
	public function maybe_add_agency_query( $queries, $params, $inx_prefix ) {
		if ( empty( $params[ "{$inx_prefix}agency" ] ) ) {
			return $queries;
		}

		$agency_prefix           = $this->get_post_instance()->prefix;
		$agency_query            = $this->get_post_instance()->get_agency_meta_query(
			$params[ "{$inx_prefix}agency" ]
		);
		$queries['meta_query']   = is_array( $queries['meta_query'] ) ?
			$queries['meta_query'] :
			array( 'relation' => 'AND' );
		$queries['meta_query'][] = $agency_query;

		return $queries;
	} // maybe_add_agency_query

	/**
	 * Check if the given ID belongs to an existing agency post.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $agency_id Agency ID to be checked.
	 *
	 * @return bool ID belongs to an existent agency post?
	 */
	public function is_valid_agency_id( $agency_id ) {
		$agency = get_post( $agency_id );

		if (
			$agency
			&& get_post_type( $agency ) === $this->post_type_name
			&& 'publish' === $agency->post_status
		) {
			return true;
		}

		return false;
	} // is_valid_agency_id

	/**
	 * Find an agency post based on the given parameters (including company name
	 * vs. post title similarity check).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|bool $author_id Author ID.
	 * @param string|bool     $import_folder Related import folder.
	 * @param string|bool     $company Agency company name.
	 * @param bool            $best_match_only Indicate if one ("best matching")
	 *                                         agency post shall be returned only.
	 *
	 * @return \WP_Post[]|\WP_Post|bool Agency post(s) or false if no matching
	 *                                  one exists.
	 */
	public function find(
		$author_id = false,
		$import_folder = 'global',
		$company = false,
		$best_match_only = false
	) {
		$meta_query = array(
			'relation' => 'AND',
			array(
				'key'   => '_immonex_import_folder',
				'value' => $import_folder,
			),
		);

		$args = array(
			'post_type'   => $this->post_type_name,
			'numberposts' => -1,
			'meta_query'  => $meta_query,
		);

		if ( (int) $author_id ) {
			$args['post_author'] = (int) $author_id;
		}

		$posts = get_posts( $args );
		if ( 0 === count( $posts ) ) {
			return $best_match_only ? false : array();
		}

		if ( ! $company ) {
			return $best_match_only ? $posts[0] : $posts;
		}

		$agencies = array();

		foreach ( $posts as $post ) {
			$similarity = 0;
			similar_text( $post->post_title, $company, $similarity );

			if ( $similarity > 85 ) {
				$agencies[] = array(
					'post'       => $post,
					'similarity' => $similarity,
				);
			}
		}

		$agencies_return = array();

		if ( count( $agencies ) > 0 ) {
			usort(
				$agencies,
				function( $a, $b ) {
					if ( $a['similarity'] === $b['similarity'] ) {
						return 0;
					}

					return $a['similarity'] < $b['similarity'] ? -1 : 1;
				}
			);

			foreach ( $agencies as $agency_data ) {
				$agencies_return[] = $agency_data['post'];
			}
		}

		if ( $best_match_only ) {
			return count( $agencies_return ) > 0 ? $agencies_return[0] : false;
		} else {
			return $agencies_return;
		}
	} // find

	/**
	 * Maybe update an agency post title after the post has been edited
	 * manually in the WP backend (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $post_id Agency post ID.
	 * @param \WP_Post   $post Updated agency post object.
	 * @param bool       $update Whether this is an existing post being
	 *                           updated or not.
	 */
	public function maybe_update_post_title( $post_id, $post, $update ) {
		if ( get_post_type( $post_id ) !== $this->post_type_name ) {
			return;
		}

		if ( empty( $post->post_title ) ) {
			$agency  = $this->get_post_instance( $post_id );
			$company = $agency->get_element_value( 'legal_company' );

			if ( $company ) {
				$post->post_title = $company;
				wp_update_post( $post );
			}
		}
	} // maybe_update_post_title

	/**
	 * Maybe update the linked agency ID after a PROPERTY post has been edited
	 * manually in the WP backend (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $post_id Property post ID.
	 * @param \WP_Post   $post Updated property post object.
	 * @param bool       $update Whether this is an existing post being
	 *                           updated or not.
	 */
	public function update_property_agency_ids( $post_id, $post, $update ) {
		if ( get_post_type( $post_id ) !== Kickstart::PROPERTY_POST_TYPE_NAME ) {
			return;
		}

		delete_post_meta( $post_id, '_inx_team_agency_id' );

		$agent_ids            = get_post_meta( $post_id, '_inx_team_agent_primary' );
		$additional_agent_ids = get_post_meta( $post_id, '_inx_team_agents', true );
		if (
			is_array( $additional_agent_ids ) &&
			! empty( $additional_agent_ids )
		) {
			$agent_ids = array_merge(
				$agent_ids,
				$additional_agent_ids
			);
		}

		$agency_ids = array();
		if ( count( $agent_ids ) > 0 ) {
			foreach ( $agent_ids as $agent_id ) {
				$agency_id = get_post_meta( $agent_id, '_inx_team_agency_id', true );
				if ( $agency_id && ! in_array( $agency_id, $agency_ids ) ) {
					$agency_ids[] = $agency_id;
				}
			}
		}

		if ( count( $agency_ids ) > 0 ) {
			foreach ( $agency_ids as $id ) {
				add_post_meta( $post_id, '_inx_team_agency_id', $id, false );
			}
		}
	} // update_property_agency_ids

	/**
	 * Delete related agency IDs of PROPERTY posts if an agency post has been
	 * deleted (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $agency_post_id ID of deleted agency post.
	 */
	public function remove_outdated_agency_ids( $agency_post_id ) {
		if ( get_post_type( $agency_post_id ) !== $this->post_type_name ) {
			return;
		}

		$args       = array(
			'post_type'                     => Kickstart::PROPERTY_POST_TYPE_NAME,
			'fields'                        => 'ids',
			'suppress_pre_get_posts_filter' => true,
			'numberposts'                   => -1,
			'meta_query'                    => array(
				array(
					'key'   => '_inx_team_agency_id',
					'value' => $agency_post_id,
				),
			),
		);
		$properties = get_posts( $args );

		$args['post_type'] = 'inx_agent';
		$agents            = get_posts( $args );

		$posts = array_merge( $properties, $agents );

		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $post_id ) {
				delete_post_meta( $post_id, '_inx_team_agency_id', $agency_post_id );
			}
		}
	} // remove_outdated_agency_ids

	/**
	 * Return a rendered agency single view (shortcode-based).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $atts Rendering Attributes.
	 *
	 * @return string Rendered shortcode contents.
	 */
	public function shortcode_agency( $atts = array() ) {
		if ( is_admin() ) {
			return '';
		}

		if ( empty( $atts ) ) {
			$atts = array();
		}

		$default_template = 'index';
		if (
			isset( $atts['type'] )
			&& 'widget' === strtolower( $atts['type'] )
		) {
			$default_template = 'widget';
		}

		if ( ! empty( $atts['template'] ) ) {
			$template = $atts['template'];
		} else {
			$template = "single-{$this->base_name}/{$default_template}";
		}

		$agency = false;

		if ( ! empty( $atts['id'] ) ) {
			$agency = $this->get_post_instance( $atts['id'] );
		}

		if ( ! $agency || empty( $agency->post ) ) {
			$property_id = apply_filters(
				'inx_current_property_post_id',
				$this->utils['general']->get_the_ID()
			);

			if ( ! $property_id ) {
				return '';
			}

			if ( ! empty( $atts['display_for'] ) ) {
				$display_for         = strtolower( trim( $atts['display_for'] ) );
				$display_for_options = $this->config['plugin']->get_display_for_options();

				if (
					'all' !== $display_for
					&& in_array( $display_for, array_keys( $display_for_options ) )
				) {
					if ( ! $this->config['plugin']->shall_be_displayed( $property_id, $display_for ) ) {
						return '';
					}
				}
			}

			// Retrieve all agent IDs for the current property (first = primary).
			$agent_ids = Agent::fetch_agent_ids( $property_id );
			if ( empty( $agent_ids ) ) {
				return '';
			}

			$agency_id = get_post_meta( $agent_ids[0], '_inx_team_agency_id', true );
			if ( ! $agency_id ) {
				return '';
			}

			$agency = $this->get_post_instance( $agency_id );
		}

		if ( ! $agency || empty( $agency->post ) ) {
			return '';
		}

		if ( ! isset( $atts['title'] ) ) {
			$atts['title'] = '';
		}

		return $this->render_single( $agency->post->ID, $template, $atts, false );
	} // shortcode_agency

} // Agency_Hooks
