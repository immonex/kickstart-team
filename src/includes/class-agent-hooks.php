<?php
/**
 * Class Agent_Hooks
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

use \immonex\Kickstart\Kickstart;

/**
 * Agent CPT related actions and filters
 */
class Agent_Hooks extends Base_CPT_Hooks {

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
	 * Related offerer (agency) XML object
	 *
	 * @var \SimpleXMLElement
	 */
	private $anbieter;

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

		// Hook save_post instead of save_post_inx_agent due to CMB2 priority issue.
		add_action( 'save_post', array( $this, 'maybe_update_post_title' ), 90, 3 );
		add_action( 'save_post', array( $this, 'update_property_agency_ids' ), 90, 3 );
		add_action( 'deleted_post', array( $this, 'remove_outdated_agent_ids' ) );
		add_action( 'template_redirect', array( $this, 'prevent_page_param_redirect' ), 0 );

		add_filter( 'immonex_oi2wp_import_agency_xml_before_import', array( $this, 'set_xml_agency' ) );
		add_filter( 'immonex_oi2wp_create_agent', array( $this, 'create_agent_by_xml' ), 10, 4 );
		add_filter( 'immonex_oi2wp_assign_agent', array( $this, 'maybe_update_agent' ), 10, 5 );

		add_filter( 'inx_special_query_vars', array( $this, 'add_agent_query_vars' ), 10, 2 );
		add_filter( 'inx_search_tax_and_meta_queries', array( $this, 'maybe_add_agent_query' ), 10, 3 );
		add_filter( 'inx_detail_page_elements', array( $this, 'replace_default_contact_section' ) );

		// Filter for "manually" creating and updating agents.
		add_filter( 'inx_team_create_agent', array( $this, 'create_agent' ), 10, 2 );
		add_filter( 'inx_team_update_agent', array( $this, 'update_agent' ), 10, 3 );

		/**
		 * Shortcodes
		 */

		add_shortcode( 'inx-team-agent', array( $this, 'shortcode_agent' ) );
	} // __construct

	/**
	 * Set the related offerer (agency) XML object (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param \SimpleXMLElement $anbieter Offerer XML object.
	 *
	 * @return Unchanged offerer XML object.
	 */
	public function set_xml_agency( $anbieter ) {
		$this->anbieter = $anbieter;

		return $anbieter;
	} // set_xml_agency

	/**
	 * Create a new agent and return its ID (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param null    $agent_id Agent ID (return value only).
	 * @param mixed[] $data Agent data.
	 *
	 * @return int|string|\WP_Error ID of created agent post or WP_Error object
	 *                              on failure.
	 */
	public function create_agent( $agent_id, $data ) {
		$agent_prefix  = '_' . $this->config['plugin_prefix'] . 'agent_';
		$agency_hooks  = $this->config['plugin']->cpt_hooks['Agency_Hooks'];
		$agent         = $this->get_post_instance();
		$import_folder = ! empty( $data['import_folder'] ) ? $data['import_folder'] : 'global';

		if ( ! empty( $data['post_title'] ) ) {
			$title = sanitize_text_field( $data['post_title'] );
		} else {
			$title = trim(
				( isset( $data['first_name'] ) ? $data['first_name'] : '' )
				. ' '
				. ( isset( $data['last_name'] ) ? $data['last_name'] : '' )
			);
		}

		$initial_agent_post_data = array(
			'post_title' => $title,
		);

		$post_author = false;
		if ( ! empty( $data['post_author'] ) ) {
			$post_author = $data['post_author'];
		} elseif ( ! empty( $data['user_id'] ) ) {
			$post_author = $data['user_id'];
		}

		if ( $post_author && get_user_by( 'id', (int) $post_author ) ) {
			$initial_agent_post_data['post_author'] = (int) $post_author;
		}

		$initial_agent_meta = array(
			'_immonex_import_folder'     => $import_folder,
			"{$agent_prefix}auto_update" => isset( $data['auto_update'] ) ? (bool) $data['auto_update'] : true,
		);

		$agent_id = $agent->create(
			$initial_agent_post_data,
			$initial_agent_meta
		);

		if ( $agent_id ) {
			if ( ! isset( $data['address_publishing_approved'] ) ) {
				$data['address_publishing_approved'] = '1';
			}

			$agent->update( $data );

			if (
				! empty( $data['agency_id'] )
				&& $agency_hooks->is_valid_agency_id( $data['agency_id'] )
			) {
				update_post_meta( $agent_id, '_inx_team_agency_id', $data['agency_id'] );
			}
		}

		return $agent_id;
	} // create_agent

	/**
	 * Create a new agent based on the related XML object plus import meta data
	 * and return its ID (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param null              $agent_id Agent ID (return value only).
	 * @param mixed[]           $core_data Import meta data.
	 * @param \SimpleXMLElement $immobilie Property XML object.
	 * @param string            $import_dir Full import directory path.
	 *
	 * @return int|string|\WP_Error ID of created agent post or WP_Error object
	 *                              on failure.
	 */
	public function create_agent_by_xml( $agent_id, $core_data, $immobilie, $import_dir ) {
		$agent_prefix = '_' . $this->config['plugin_prefix'] . 'agent_';
		$agent        = $this->get_post_instance();
		$title        = trim(
			(string) $immobilie->kontaktperson->vorname
			. ' ' . (string) $immobilie->kontaktperson->name
		);

		$initial_agent_post_data = array(
			'post_title'  => $title,
			'post_author' => $core_data['user_id'],
		);

		$initial_agent_meta = array(
			'_immonex_import_folder'     => $core_data['import_folder'],
			'_immonex_is_demo'           => $core_data['is_demo'],
			"{$agent_prefix}auto_update" => true,
		);

		$agent_id = $agent->create(
			$initial_agent_post_data,
			$initial_agent_meta
		);

		if ( $agent_id ) {
			$agent->update_by_openimmo_xml( $immobilie, $import_dir );

			$agency_id = $this->determine_or_create_agency( $agent_id, $core_data, $immobilie );
			if ( $agency_id ) {
				update_post_meta( $agent_id, '_inx_team_agency_id', $agency_id );
			}
		}

		return $agent_id;
	} // create_agent_by_xml

	/**
	 * Update an agent (to be used directly AND as filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $agent_id ID of the agent post to be updated.
	 * @param mixed[]    $data Agent data.
	 * @param bool       $is_auto_update Flag for determining if the current
	 *                                   operation is an auto-update (optional,
	 *                                   false by default).
	 *
	 * @return int|string|bool ID of the updated agent post, false if not
	 *                         existent or -1 on auto-updates if these are
	 *                         disabled for the given post.
	 */
	public function update_agent( $agent_id, $data, $is_auto_update = false ) {
		$agent_prefix = '_' . $this->config['plugin_prefix'] . 'agent_';
		$agent        = $this->get_post_instance( $agent_id );

		if ( ! isset( $agent->post->ID ) ) {
			return false;
		}

		if (
			$is_auto_update
			&& ! get_post_meta( $agent_id, "{$agent_prefix}auto_update", true )
		) {
			return -1;
		}

		$agent->update( $data );

		return $agent_id;
	} // update_agent

	/**
	 * Check if an agent update is required during import processing and
	 * perform it if so (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string        $agent_id ID of the agent post to be checked/updated.
	 * @param mixed[]           $core_data Import meta data.
	 * @param \SimpleXMLElement $immobilie Property XML object.
	 * @param string            $import_dir Full import directory path.
	 * @param bool              $is_auto_update Flag for determining if the current
	 *                                          operation is an auto-update.
	 *
	 * @return int|string|bool Agent post ID, false if not existent.
	 */
	public function maybe_update_agent( $agent_id, $core_data, $immobilie, $import_dir, $is_auto_update ) {
		if ( ! $is_auto_update ) {
			return $agent_id;
		}

		$agent_prefix = '_' . $this->config['plugin_prefix'] . 'agent_';
		$agency_id    = get_post_meta( $agent_id, '_inx_team_agency_id', true );
		$agency_hooks = $this->config['plugin']->cpt_hooks['Agency_Hooks'];
		$xml_checksum = strlen( $immobilie->kontaktperson->asXML() );

		if (
			$agency_id
			&& ! $agency_hooks->is_valid_agency_id( $agency_id )
		) {
			/**
			 * Agency with given ID does not exist (anymore): delete respective
			 * agent custom field.
			 */
			delete_post_meta( $agent_id, '_inx_team_agency_id' );
			$agency_id = false;
		}

		if (
			! get_post_meta( $agent_id, "{$agent_prefix}auto_update", true )
			|| (
				$agency_id
				&& (int) get_post_meta( $agent_id, "{$agent_prefix}update_checksum", true ) === $xml_checksum
			)
		) {
			return $agent_id;
		}

		/**
		 * Auto update agent data based on submitted XML.
		 */
		$agent = $this->get_post_instance( $agent_id );

		if ( $agent->post ) {
			$agent->update_by_openimmo_xml( $immobilie, $import_dir );
		}

		$agency_id = $this->determine_or_create_agency( $agent_id, $core_data, $immobilie );

		if ( $agency_id ) {
			update_post_meta( $agent_id, '_inx_team_agency_id', $agency_id );
		}

		return $agent_id;
	} // maybe_update_agent

	/**
	 * Add special query variables related to agents (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $special_query_vars Prefixed variable names.
	 * @param string   $inx_prefix Kickstart prefix (normally "inx").
	 *
	 * @return string[] Query variables.
	 */
	public function add_agent_query_vars( $special_query_vars, $inx_prefix ) {
		$special_query_vars[] = "{$inx_prefix}agent";
		$special_query_vars[] = "{$inx_prefix}primary-agent";

		return $special_query_vars;
	} // add_agent_query_vars

	/**
	 * Add an agent related meta query if a related variable/parameter is set
	 * (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $queries Current queries.
	 * @param mixed[] $params Extended query parameters.
	 * @param string  $inx_prefix Kickstart prefix (normally "inx").
	 *
	 * @return string[] Extended queries or original value if no agent parameters
	 *                  are set.
	 */
	public function maybe_add_agent_query( $queries, $params, $inx_prefix ) {
		if (
			empty( $params[ "{$inx_prefix}agent" ] )
			&& empty( $params[ "{$inx_prefix}primary-agent" ] )
		) {
			return $queries;
		}

		$agent_prefix            = $this->get_post_instance()->prefix;
		$primary                 = ! empty( $params[ "{$inx_prefix}primary-agent" ] );
		$agent_query             = $this->get_post_instance()->get_agent_meta_query(
			$primary ? $params[ "{$inx_prefix}primary-agent" ] : $params[ "{$inx_prefix}agent" ],
			$primary
		);
		$queries['meta_query']   = is_array( $queries['meta_query'] ) ?
			$queries['meta_query'] :
			array( 'relation' => 'AND' );
		$queries['meta_query'][] = $agent_query;

		return $queries;
	} // maybe_add_agent_query

	/**
	 * Replace the default Kickstart property details contact section element by
	 * the respective element provided this plugin, if enabled (filter callback).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $elements Current/Default elements.
	 *
	 * @return mixed[] Possibly modified elements.
	 */
	public function replace_default_contact_section( $elements ) {
		if ( isset( $elements['contact_person'] ) ) {
			$agent         = $this->get_primary_agent_for_current_property();
			$elements_temp = $elements;

			if ( $agent ) {
				if ( 'disable' === $this->config['default_contact_section_adaptation'] ) {
					unset( $elements['contact_person'] );
				} elseif ( 'replace' === $this->config['default_contact_section_adaptation'] ) {
					$params = array(
						'inx_team_render_single_agent',
						$agent->post->ID,
						'single-agent/default-contact-element-replacement',
						array(
							'type'          => 'default_contact_element_replacement',
							'convert_links' => true,
						),
					);

					$elements['contact_person'] = array(
						'do_action' => $params,
						'template'  => '',
					);
				}
			}
		}

		return $elements;
	} // replace_default_contact_section

	/**
	 * Determine an existing agency to be assigned to the agent or create a new
	 * one if not found.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string        $agent_id Agent post ID.
	 * @param mixed[]           $core_data Import meta data.
	 * @param \SimpleXMLElement $immobilie Property XML object.
	 *
	 * @return int|string|bool Agency post ID, false on failure.
	 */
	private function determine_or_create_agency( $agent_id, $core_data, $immobilie ) {
		$agency_hooks = $this->config['plugin']->cpt_hooks['Agency_Hooks'];

		$forced_agency_id = apply_filters( 'inx_team_force_agency_id_on_agent_update', false );
		if (
			$forced_agency_id
			&& $agency_hooks->is_valid_agency_id( $forced_agency_id )
		) {
			return $forced_agency_id;
		}

		$agent_prefix  = '_' . $this->config['plugin_prefix'] . 'agent_';
		$agency_prefix = '_' . $this->config['plugin_prefix'] . 'agency_';
		$agency_id     = false;
		$agency        = $agency_hooks->get_post_instance();
		$company       = $agency->get_company_from_xml( $this->anbieter, $immobilie );

		if ( $agent_id ) {
			$agency_id = get_post_meta( $agent_id, '_inx_team_agency_id', true );

			if (
				$agency_id
				&& ! $agency_hooks->is_valid_agency_id( $agency_id )
			) {
				/**
				 * Agency with given ID does not exist (anymore): delete respective
				 * agent custom field.
				 */
				delete_post_meta( $agent_id, '_inx_team_agency_id' );
				$agency_id = false;
			}
		}

		if (
			! $agency_id
			&& $core_data['user_agency_id']
			&& $agency_hooks->is_valid_agency_id( $core_data['user_agency_id'] )
		) {
			// Take over a user based agency ID if given and valid.
			$agency_id = $core_data['user_agency_id'];
		}

		if ( ! $agency_id ) {
			/**
			 * Try to determine a suitable existing agency and assign it
			 * (below) if found.
			 */
			$best_matching_agency_post = $agency_hooks->find(
				$core_data['user_id'],
				$core_data['import_folder'],
				$company,
				true
			);

			if ( $best_matching_agency_post ) {
				$agency_id = $best_matching_agency_post->ID;
			}
		}

		$agency->set_post( $agency_id );

		if (
			$agency->post
			&& ! get_post_meta( $agency->post->ID, "{$agency_prefix}auto_update", true )
		) {
			return $agency_id;
		}

		if ( ! $agency->post ) {
			/**
			 * Create a new agency post.
			 */
			$initial_agency_post_data = array(
				'post_title'  => $company,
				'post_author' => $core_data['user_id'],
			);

			$initial_agency_meta = array(
				'_immonex_import_folder'      => $core_data['import_folder'],
				'_immonex_is_demo'            => $core_data['is_demo'],
				"{$agency_prefix}auto_update" => true,
			);

			$agency_id = $agency->create(
				$initial_agency_post_data,
				$initial_agency_meta
			);

			if ( $agency_id ) {
				$agency->set_post( $agency_id );
			}
		}

		if ( $agency->post ) {
			// Update an existing or newly created agency post.
			$agency->update_by_openimmo_xml( $this->anbieter, $immobilie );
		}

		return $agency_id;
	} // determine_or_create_agency

	/**
	 * Maybe update an agent post title after the post has been edited
	 * manually in the WP backend (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $post_id Agent post ID.
	 * @param \WP_Post   $post Updated agent post object.
	 * @param bool       $update Whether this is an existing post being
	 *                           updated or not.
	 */
	public function maybe_update_post_title( $post_id, $post, $update ) {
		if ( get_post_type( $post_id ) !== $this->post_type_name ) {
			return;
		}

		if ( empty( $post->post_title ) ) {
			$agent = $this->get_post_instance( $post_id );
			$name  = $agent->get_element_value( 'full_name_incl_title' );

			if ( $name ) {
				$post->post_title = $name;
				wp_update_post( $post );
			}
		}
	} // maybe_update_post_title

	/**
	 * Maybe update the linked PROPERTY agency ID after an agent post has been
	 * edited manually in the WP backend (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $agent_id Agent post ID.
	 * @param \WP_Post   $post Updated agent post object.
	 * @param bool       $update Whether this is an existing post being
	 *                           updated or not.
	 */
	public function update_property_agency_ids( $agent_id, $post, $update ) {
		if (
			! $update
			|| get_post_type( $agent_id ) !== $this->post_type_name
		) {
			return;
		}

		$property_ids = apply_filters(
			'inx_get_properties',
			array(),
			array(
				'inx-primary-agent' => $agent_id,
				'fields'            => 'ids',
			)
		);

		if ( count( $property_ids ) > 0 ) {
			foreach ( $property_ids as $property_id ) {
				delete_post_meta( $property_id, '_inx_team_agency_id' );

				$agent_ids            = array( $agent_id );
				$additional_agent_ids = get_post_meta( $property_id, '_inx_team_agents', true );
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
						add_post_meta( $property_id, '_inx_team_agency_id', $id, false );
					}
				}
			}
		}
	} // update_property_agency_ids

	/**
	 * Delete related agent IDs of PROPERTY posts if an agent post has been
	 * deleted (action callback).
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $agent_post_id ID of deleted agency post.
	 */
	public function remove_outdated_agent_ids( $agent_post_id ) {
		if ( get_post_type( $agent_post_id ) !== $this->post_type_name ) {
			return;
		}

		$primary_agent_meta_query = $this->get_post_instance()->get_agent_meta_query( $agent_post_id, true );
		$agent_meta_query         = $this->get_post_instance()->get_agent_meta_query( $agent_post_id, false );

		/**
		 * Retrieve and update properties with the given agent ID
		 * as primary contact.
		 */

		$args         = array(
			'post_type'                     => Kickstart::PROPERTY_POST_TYPE_NAME,
			'fields'                        => 'ids',
			'suppress_pre_get_posts_filter' => true,
			'numberposts'                   => -1,
			'meta_query'                    => array(
				$primary_agent_meta_query,
			),
		);
		$property_ids = get_posts( $args );

		if ( count( $property_ids ) > 0 ) {
			foreach ( $property_ids as $property_id ) {
				delete_post_meta( $property_id, '_inx_team_agent_primary' );
			}
		}

		/**
		 * Retrieve and update properties with the given agent ID
		 * within the further (secondary) contacts.
		 */

		$args['meta_query'] = array( $agent_meta_query );
		$property_ids       = get_posts( $args );

		if ( count( $property_ids ) > 0 ) {
			foreach ( $property_ids as $property_id ) {
				$agents = get_post_meta( $property_id, '_inx_team_agents', true );
				$key    = array_search( $agent_post_id, $agents );

				if ( ! empty( $agents ) && false !== $key ) {
					unset( $agents[ $key ] );

					if ( 0 === count( $agents ) ) {
						delete_post_meta( $property_id, '_inx_team_agents' );
					} else {
						update_post_meta( $property_id, '_inx_team_agents', $agents );
					}
				}
			}
		}
	} // remove_outdated_agent_ids

	/**
	 * Return a rendered agent single view (shortcode-based).
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $atts Rendering Attributes.
	 *
	 * @return string Rendered shortcode contents.
	 */
	public function shortcode_agent( $atts = array() ) {
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

		$agent  = false;
		$prefix = $this->get_post_instance()->prefix;

		if ( ! empty( $atts['id'] ) ) {
			$agent = $this->get_post_instance( $atts['id'] );
		}

		if ( ! $agent || empty( $agent->post ) ) {
			$agent = $this->get_primary_agent_for_current_property();
		}

		if ( ! $agent || empty( $agent->post ) ) {
			return '';
		}

		if ( ! empty( $atts['display_for'] ) ) {
			$display_for         = strtolower( trim( $atts['display_for'] ) );
			$display_for_options = $this->config['plugin']->get_display_for_options();

			if (
				'all' !== $display_for
				&& in_array( $display_for, array_keys( $display_for_options ) )
			) {
				$property_id = apply_filters(
					'inx_current_property_post_id',
					$this->utils['general']->get_the_ID()
				);

				if ( ! $this->config['plugin']->shall_be_displayed( $property_id, $display_for ) ) {
					return '';
				}
			}
		}

		if ( ! isset( $atts['title'] ) ) {
			$atts['title'] = '';
		}

		return $this->render_single( $agent->post->ID, $template, $atts, false );
	} // shortcode_agent

	/**
	 * Retrieve the primary agent object of the current property.
	 *
	 * @since 1.0.0
	 *
	 * @return Agent|bool Agent object or false if inexistent/indeterminable.
	 */
	private function get_primary_agent_for_current_property() {
		$agent       = false;
		$prefix      = $this->get_post_instance()->prefix;
		$property_id = apply_filters(
			'inx_current_property_post_id',
			$this->utils['general']->get_the_ID()
		);

		$agent_id = get_post_meta( $property_id, "{$prefix}primary", true );

		if ( $agent_id ) {
			$agent = $this->get_post_instance( $agent_id );

			if ( $agent && ! empty( $agent->post ) ) {
				return $agent;
			}
		}

		$agent_ids = get_post_meta( $property_id, rtrim( $prefix, '_' ) . 's', true );

		if ( ! empty( $agent_ids ) ) {
			return $this->get_post_instance( $agent_ids[0] );
		}

		return false;
	} // get_primary_agent_for_current_property

} // Agent_Hooks
