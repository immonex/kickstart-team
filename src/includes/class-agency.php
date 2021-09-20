<?php
/**
 * Class Agency
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Agency CPT rendering
 */
class Agency extends Base_CPT_Post {

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
	 * Element value cache
	 *
	 * @var mixed[]
	 */
	private $element_values = array();

	/**
	 * Render post details (PHP template).
	 *
	 * @since 1.0.0
	 *
	 * @param string  $template Template file name (without suffix; optional).
	 * @param mixed[] $atts Rendering attributes.
	 *
	 * @return string Rendered contents (HTML).
	 */
	public function render( $template = '', $atts = array() ) {
		if ( ! $this->post ) {
			return '';
		}

		if ( empty( $atts['type'] ) ) {
			$atts['type'] = 'widget' === substr( $template, 0, 6 ) ? 'widget' : 'single_agency_page';
		}

		$this->link_type = ! empty( $atts['link_type'] ) ? $atts['link_type'] : 'internal';

		$post_types = get_post_types( array( 'name' => $this->post_type_name ), 'objects' );
		if ( isset( $post_types[ $this->post_type_name ] ) ) {
			$this->is_public = $post_types[ $this->post_type_name ]->public;
		}

		$url = false;
		if ( 'none' !== $this->link_type ) {
			if ( 'external' === $this->link_type ) {
				$url = $this->get_element_value( 'url' );
			} elseif ( $this->is_public ) {
				$url = get_permalink( $this->post->ID );
			}
		}

		$valid_elements   = $this->get_elements();
		$default_filter   = 'single_agency_page' === $atts['type'] ? 'all' : $atts['type'];
		$default_elements = array_keys( $this->get_elements( $default_filter ) );
		$convert_links    = ! empty( $atts['convert_links'] );
		$contents         = array(
			'before_title'   => isset( $atts['before_title'] ) ? $atts['before_title'] : '',
			'title'          => isset( $atts['title'] ) ? $atts['title'] : $this->post->post_title,
			'after_title'    => isset( $atts['after_title'] ) ? $atts['after_title'] : '',
			'convert_links'  => $convert_links,
			'link_type'      => $this->link_type,
			'agency_id'      => $this->post->ID,
			'is_public'      => $this->is_public,
			'is_demo'        => get_post_meta( $this->post->ID, '_immonex_is_demo', true ),
			'url'            => $url,
			'agent_count'    => $this->get_agent_count(),
			'property_count' => $this->get_property_count(),
			'elements'       => array(),
		);

		$requested_elements = ! empty( $atts['elements'] ) ?
			$atts['elements'] :
			array();

		if ( ! is_array( $requested_elements ) ) {
			$requested_elements = array_map( 'trim', explode( ',', (string) $requested_elements ) );
		}

		$element_keys = ! empty( $requested_elements ) ? $requested_elements : $default_elements;

		if ( count( $element_keys ) > 0 ) {
			foreach ( $valid_elements as $key => $element ) {
				if ( ! in_array( $key, $element_keys, true ) ) {
					continue;
				}

				$value = $this->get_element_value( $key );
				if ( $convert_links ) {
					$value = $this->maybe_add_link( $value );
				}

				$contents['elements'][ $key ] = array(
					'label'         => ! empty( $valid_elements[ $key ]['show_label'] ) ?
						$valid_elements[ $key ]['label'] :
						'',
					'icon'          => ! empty( $valid_elements[ $key ]['icon'] ) ?
						$valid_elements[ $key ]['icon'] :
						'',
					'value'         => $value,
					'default_show'  => ! empty( $valid_elements[ $key ]['default_show'] ) ?
						$valid_elements[ $key ]['default_show'] :
						array(),
					'section_order' => ! empty( $valid_elements[ $key ]['section_order'] ) ?
						$valid_elements[ $key ]['section_order'] :
						90,
				);
			}
		}

		return parent::render( $template, $contents );
	} // render

	/**
	 * Update an agency post and the related meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $data Agency data.
	 */
	public function update( $data ) {
		if ( ! $this->post ) {
			return;
		}

		$prefix    = '_' . $this->config['plugin_prefix'] . 'agency_';
		$elements  = $this->get_elements();
		$post_data = array();
		$meta      = array();

		if ( ! empty( $data['openimmo_anid'] ) ) {
			$meta['_openimmo_anid'] = sanitize_text_field( $data['openimmo_anid'] );
		}

		if (
			! empty( $data['company'] )
			&& $this->post->post_title !== $data['company']
		) {
			$post_data['post_title'] = sanitize_text_field( $data['company'] );
		}

		foreach ( $elements as $key => $element ) {
			if ( ! isset( $data[ $key ] ) ) {
				continue;
			}

			$value = is_string( $data[ $key ] ) ?
				sanitize_textarea_field( $data[ $key ] ) :
				$data[ $key ];

			if ( ! empty( $element['post_data'] ) ) {
				$post_data[ $element['post_data'] ] = $value;
			} elseif ( ! empty( $element['meta_key'] ) ) {
				$meta[ $element['meta_key'] ] = $value;
			}
		}

		$update = apply_filters(
			'inx_team_update_agency_data',
			array(
				'post_data' => $post_data,
				'meta'      => $meta,
			)
		);

		if ( count( $update['post_data'] ) > 0 ) {
			foreach ( $update['post_data'] as $key => $value ) {
				$this->post->$key = $value;
			}
			wp_update_post( $this->post );
		}

		if ( count( $update['meta'] ) > 0 ) {
			foreach ( $update['meta'] as $meta_key => $value ) {
				update_post_meta( $this->post->ID, $meta_key, $value );
			}
		}

		if ( ! empty( $data['logo'] ) ) {
			$logo_id = $this->replace_logo( $data['logo'] );

			if ( $logo_id && ! is_wp_error( $logo_id ) ) {
				set_post_thumbnail( $this->post->ID, $logo_id );
			}
		}
	} // update

	/**
	 * Update agency post and the meta data based on related OpenImmo XML data.
	 *
	 * @since 1.0.0
	 *
	 * @param \SimpleXMLElement $anbieter Offerer XML object.
	 * @param \SimpleXMLElement $immobilie Property XML object.
	 */
	public function update_by_openimmo_xml( $anbieter, $immobilie ) {
		if ( ! $this->post ) {
			return;
		}

		$prefix    = '_' . $this->config['plugin_prefix'] . 'agency_';
		$elements  = $this->get_elements();
		$post_data = array();
		$meta      = array(
			'_openimmo_anid' => sanitize_text_field( (string) $anbieter->openimmo_anid ),
		);

		$company = $this->get_company_from_xml( $anbieter, $immobilie );
		if ( $company && $this->post->post_title !== $company ) {
			$post_data['post_title'] = $company;
		}

		$meta[ "{$this->prefix}address_publishing_approved" ] = in_array(
			(string) $immobilie->kontaktperson->adressfreigabe,
			array( '0', 'false' ),
			true
		) ? '0' : '1';

		/**
		 * Compile property based values (contact person).
		 */
		foreach ( $elements as $key => $element ) {
			if (
				empty( $element['meta_key'] )
				|| ! isset( $element['xpath'] )
				|| '//impressum_strukt' === substr( $element['xpath'], 0, 18 )
			) {
				continue;
			}

			$value = isset( $immobilie->xpath( $element['xpath'] )[0] ) ?
				(string) $immobilie->xpath( $element['xpath'] )[0] : '';

			if ( 'phone' === $key && ! $value && $anbieter ) {
				$value = (string) $anbieter->impressum_strukt->telefon;
			}

			if ( ! empty( $value ) ) {
				$meta[ $element['meta_key'] ] = sanitize_text_field( $value );
			}
		}

		/**
		 * Compile offerer based values (legal data).
		 */
		foreach ( $elements as $key => $element ) {
			if (
				empty( $element['meta_key'] )
				|| ! isset( $element['xpath'] )
				|| '//impressum_strukt' !== substr( $element['xpath'], 0, 18 )
			) {
				continue;
			}

			$value = ! empty( $anbieter->xpath( $element['xpath'] ) ) ?
				(string) $anbieter->xpath( $element['xpath'] )[0] : false;

			if ( ! empty( $value ) ) {
				if (
					'legal_notice' === $key
					|| 'legal_address' === $key
				) {
					$meta[ $element['meta_key'] ] = sanitize_textarea_field( $value );
				} else {
					$meta[ $element['meta_key'] ] = sanitize_text_field( $value );
				}
			}
		}

		$update = apply_filters(
			'inx_team_update_agency_data',
			array(
				'post_data' => $post_data,
				'meta'      => $meta,
			)
		);

		if ( count( $update['post_data'] ) > 0 ) {
			foreach ( $update['post_data'] as $key => $value ) {
				$this->post->$key = $value;
			}
			wp_update_post( $this->post );
		}

		if ( count( $update['meta'] ) > 0 ) {
			foreach ( $update['meta'] as $meta_key => $value ) {
				update_post_meta( $this->post->ID, $meta_key, $value );
			}
		}
	} // update_by_openimmo_xml

	/**
	 * Determine the company name from different XML elements.
	 *
	 * @since 1.0.0
	 *
	 * @param \SimpleXMLElement|null $anbieter Offerer XML object.
	 * @param \SimpleXMLElement      $immobilie Property XML object.
	 *
	 * @return string Company name.
	 */
	public function get_company_from_xml( $anbieter = null, $immobilie ) {
		$company = (string) $immobilie->kontaktperson->firma;
		if ( $anbieter ) {
			$company = (string) $anbieter->firma;
			if ( ! $company ) {
				$company = (string) $anbieter->impressum_strukt->firmenname;
			}
		}

		return sanitize_text_field( $company );
	} // get_company_from_xml

	/**
	 * Generate an agency related meta query.
	 *
	 * @since 1.0.0
	 *
	 * @param int[]|string[]|bool $agency_ids Agency IDs for query.
	 *
	 * @return mixed[]|bool Meta query array or false on invalid ID data.
	 */
	public function get_agency_meta_query( $agency_ids = false ) {
		if ( empty( $agency_ids ) ) {
			if ( is_object( $this->post ) ) {
				$agency_ids = array( $this->post->ID );
			} else {
				return false;
			}
		}

		if ( ! is_array( $agency_ids ) ) {
			$agency_ids = array( $agency_ids );
		}

		$agency_query = array( 'relation' => 'OR' );

		foreach ( $agency_ids as $agency_id ) {
			$agency_query[] = array(
				'key'   => "{$this->prefix}id",
				'value' => (int) $agency_id,
			);
		}

		return $agency_query;
	} // get_agency_meta_query

	/**
	 * Generate a filtered list of agency element data for output and
	 * selection purposes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filter Filter term (default: "all").
	 *
	 * @return mixed[] Filtered elements.
	 */
	public function get_elements( $filter = 'all' ) {
		$icon_mail     = '<span uk-icon="mail"></span>';
		$icon_receiver = '<span uk-icon="receiver"></span>';
		$icon_print    = '<span uk-icon="print"></span>';
		$icon_location = '<span uk-icon="location"></span>';

		$elements = array(
			'logo'                        => array(
				'label'                 => __( 'Logo', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_featured_image' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget' ),
				'section_order'         => 10,
			),
			'company'                     => array(
				'label'                 => __( 'Company', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_company' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget' ),
				'section_order'         => 20,
			),
			'about'                       => array(
				'label'                 => __( 'About', 'immonex-kickstart-team' ),
				'post_data'             => 'post_content',
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 30,
			),
			'email'                       => array(
				'label'                 => __( 'Email', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_mail,
				'meta_key'              => "{$this->prefix}email",
				'xpath'                 => '//kontaktperson/email_zentrale',
				'selectable_for_output' => true,
				'default_show'          => array( 'widget', 'single_agency_page', 'list_item' ),
				'section_order'         => 40,
			),
			'phone'                       => array(
				'label'                 => __( 'Phone', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_receiver,
				'meta_key'              => "{$this->prefix}phone",
				'xpath'                 => '//kontaktperson/tel_zentrale',
				'selectable_for_output' => true,
				'default_show'          => array( 'widget', 'single_agency_page', 'list_item' ),
				'section_order'         => 50,
			),
			'fax'                         => array(
				'label'                 => __( 'Fax', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_print,
				'meta_key'              => "{$this->prefix}fax",
				'xpath'                 => '//kontaktperson/tel_fax',
				'selectable_for_output' => true,
				'default_show'          => array( 'single_agency_page' ),
				'section_order'         => 50,
			),
			'url'                         => array(
				'label'                 => __( 'URL (Company)', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}url",
				'xpath'                 => '//kontaktperson/url',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'street'                      => array(
				'label'                 => __( 'Street', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}street",
				'xpath'                 => '//kontaktperson/strasse',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 60,
			),
			'house_number'                => array(
				'label'                 => __( 'House Number', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}house_number",
				'xpath'                 => '//kontaktperson/hausnummer',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 60,
			),
			'zip_code'                    => array(
				'label'                 => __( 'ZIP Code', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}zip_code",
				'xpath'                 => '//kontaktperson/plz',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 60,
			),
			'city'                        => array(
				'label'                 => __( 'City', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}city",
				'xpath'                 => '//kontaktperson/ort',
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 20,
			),
			'po_box'                      => array(
				'label'                 => __( 'P.O. Box', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}po_box",
				'xpath'                 => '//kontaktperson/postfach',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 70,
			),
			'po_box_zip_code'             => array(
				'label'                 => __( 'P.O. Box ZIP Code', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}po_box_zip_code",
				'xpath'                 => '//kontaktperson/postf_plz',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 70,
			),
			'po_box_city'                 => array(
				'label'                 => __( 'P.O. Box City', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}po_box_city",
				'xpath'                 => '//kontaktperson/postf_ort',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 70,
			),
			'country_iso'                 => array(
				'label'                 => __( 'Country (ISO3)', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}country_iso",
				'xpath'                 => '//kontaktperson/land/@iso_land',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 80,
			),
			'address_publishing_approved' => array(
				'label'                 => __( 'Address Publishing Approval', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}address_publishing_approved",
				'xpath'                 => '//kontaktperson/adressfreigabe',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'address'                     => array(
				'label'                 => __( 'Full Address', 'immonex-kickstart-team' ),
				'icon'                  => $icon_location,
				'description'           => __( 'Disables &quot;City&quot; if set.', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_address_multi_line' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'single_agency_page' ),
				'section_order'         => 90,
			),
			'address_single_line'         => array(
				'label'                 => __( 'Address (single line)', 'immonex-kickstart-team' ),
				'icon'                  => $icon_location,
				'compose_cb'            => array( $this, 'get_address_single_line' ),
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 90,
			),
			'legal_notice'                => array(
				'label'                 => __( 'Legal Notice', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}legal_notice",
				'xpath'                 => '//impressum_strukt/impressum',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 100,
			),
			'legal_company'               => array(
				'label'                 => __( 'Company (Legal)', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}legal_company",
				'xpath'                 => '//impressum_strukt/firmenname',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 100,
			),
			'legal_address'               => array(
				'label'                 => __( 'Address (Legal)', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}legal_address",
				'xpath'                 => '//impressum_strukt/firmenanschrift',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 100,
			),
			'legal_phone'                 => array(
				'label'                 => __( 'Phone (Legal)', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}legal_phone",
				'xpath'                 => '//impressum_strukt/telefon',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 100,
			),
			'representative'              => array(
				'label'                 => __( 'Representative', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}representative",
				'xpath'                 => '//impressum_strukt/vertretungsberechtigter',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 100,
			),
			'supervisory_authority'       => array(
				'label'                 => __( 'Supervisory Authority', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}supervisory_authority",
				'xpath'                 => '//impressum_strukt/berufsaufsichtsbehoerde',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 100,
			),
			'registry_court'              => array(
				'label'                 => __( 'Registry Court', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}registry_court",
				'xpath'                 => '//impressum_strukt/handelsregister',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 110,
			),
			'trade_register_number'       => array(
				'label'                 => __( 'Trade Register Number', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}trade_register_number",
				'xpath'                 => '//impressum_strukt/handelsregister_nr',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 110,
			),
			'vatin'                       => array(
				'label'                 => __( 'VATIN', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}vatin",
				'xpath'                 => '//impressum_strukt/umsst-id',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 120,
			),
			'legal_misc'                  => array(
				'label'                 => __( 'Legal (Miscellaneous)', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}legal_misc",
				'xpath'                 => '//impressum_strukt/weiteres',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 120,
			),
			'network_urls'                => array(
				'label'                 => __( 'Business/Social Networks', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_network_urls' ),
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'network_icons'               => array(
				'label'                 => __( 'Business/Social Network Icons', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_network_icons' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget', 'single_agency_page' ),
				'section_order'         => 130,
			),
			'contact_form'                => array(
				'label'                 => __( 'Contact Form', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_contact_form' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget' ),
				'section_order'         => 140,
			),
		);

		$order = 0;
		foreach ( $elements as $key => $element ) {
			if ( ! isset( $element['section_order'] ) ) {
				$elements[ $key ]['section_order'] = 90;
			}

			$order                    += 10;
			$elements[ $key ]['order'] = $order;
		}

		if (
			! empty( $filter )
			&& 'all' !== $filter
		) {
			$elements = array_filter(
				$elements,
				function ( $element ) use ( &$filter ) {
					return ! empty( $element['default_show'] )
						&& in_array( $filter, $element['default_show'], true );
				}
			);
		}

		array_multisort(
			array_column( $elements, 'section_order' ),
			SORT_ASC,
			array_column( $elements, 'order' ),
			SORT_ASC,
			$elements
		);

		return $elements;
	} // get_elements

	/**
	 * Determine the value of a single agency element.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Element key (name).
	 *
	 * @return mixed Element value or false if indeterminable.
	 */
	public function get_element_value( $key ) {
		if ( ! $this->post ) {
			return false;
		}

		if ( ! empty( $this->element_values[ $key ] ) ) {
			return $this->element_values[ $key ];
		}

		$elements = $this->get_elements();
		if ( ! isset( $elements[ $key ] ) ) {
			return false;
		}

		$element = $elements[ $key ];
		$value   = false;

		if (
			isset( $element['compose_cb'] )
			&& is_callable( $element['compose_cb'] )
		) {
			$value = call_user_func( $element['compose_cb'], array( $this, 'get_element_value' ) );
		} elseif ( ! empty( $element['post_data'] ) ) {
			$value = apply_filters( 'inx_the_content', $this->post->{$element['post_data']} );
		} elseif ( ! empty( $element['meta_key'] ) ) {
			$value = get_post_meta( $this->post->ID, $element['meta_key'], true );
		}

		if (
			$value
			&& 'url' === $key
			&& 'http' !== strtolower( substr( $value, 0, 4 ) )
		) {
			$value = "https://{$value}";
		}

		if ( $value ) {
			$this->element_values[ $key ] = $value;
		}

		return $value;
	} // get_element_value

	/**
	 * Replace the agency's logo file (thumbnail/featured image) if different
	 * than the given (new) one.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path_or_url Local path or URL of a (possibly) new logo file.
	 *
	 * @return int|bool Attachment ID of new or existing logo, false on error.
	 */
	public function replace_logo( $path_or_url ) {
		if ( ! $this->post ) {
			return false;
		}

		$is_remote = false !== strpos( $path_or_url, '://' );

		if ( $is_remote ) {
			if ( ! $this->utils['general']->remote_file_exists( $path_or_url ) ) {
				// Remote image file or URL not found or accessible.
				return false;
			}

			$filesize = (int) $this->utils['general']->get_remote_filesize( $path_or_url );
		} else {
			if ( ! file_exists( $path_or_url ) ) {
				// Local image file not found or accessible.
				return false;
			}

			$filesize = filesize( $path_or_url );
		}

		$image_id = get_post_thumbnail_id( $this->post->ID );

		if ( $image_id ) {
			$att = wp_prepare_attachment_for_js( $image_id );

			if (
				basename( $path_or_url ) === $att['filename']
				&& $att['filesizeInBytes'] === $filesize
			) {
				// Image already exists.
				return $image_id;
			}

			wp_delete_attachment( $image_id, true );
		}

		if ( $is_remote ) {
			$file_data = array(
				'name'     => basename( $path_or_url ),
				'tmp_name' => $path_or_url,
			);
		} else {
			$temp = tmpfile();
			// @codingStandardsIgnoreLine
			fwrite( $temp, file_get_contents( $path_or_url ) );

			$file_data = array(
				'name'     => basename( $path_or_url ),
				'tmp_name' => stream_get_meta_data( $temp )['uri'],
			);
		}

		return media_handle_sideload( $file_data, $this->post->ID, $this->post->post_title );
	} // replace_logo

	/**
	 * Special getter method for the agency's company name.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[] Array containing raw company name and (eventually) a link tag.
	 */
	private function get_company( $value_getter ) {
		if ( ! $this->post ) {
			return '';
		}

		$company = $this->post->post_title;
		if ( ! $company ) {
			return '';
		}

		$link = false;

		if ( 'external' === $this->link_type ) {
			$url = call_user_func( $value_getter, 'url' );

			if ( $url ) {
				$link = wp_sprintf(
					'<a href="%s" target="_blank">%s</a>',
					$url,
					$company
				);
			}
		} elseif (
			'internal' === $this->link_type
			&& $this->is_public
		) {
			$url = get_permalink( $this->post->ID );

			if ( $url ) {
				$link = wp_sprintf(
					'<a href="%s">%s</a>',
					$url,
					$company
				);
			}
		}

		return array(
			'raw'  => $company,
			'link' => $link ? $link : $company,
		);
	} // get_company

	/**
	 * Get a list of supported business/social networks.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Key:Name list of networks.
	 */
	public function get_networks() {
		$networks = array(
			'xing'      => 'XING',
			'linkedin'  => 'LinkedIn',
			'twitter'   => 'Twitter',
			'facebook'  => 'Facebook',
			'instagram' => 'Instagram',
		);

		return apply_filters( 'inx_team_agency_networks', $networks );
	} // get_networks

	/**
	 * Special getter method for the agency's business/social network URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[] Array containing name/URL pairs.
	 */
	private function get_network_urls( $value_getter ) {
		if ( ! is_object( $this->post ) || ! $this->post->ID ) {
			return array();
		}

		$prefix   = '_' . $this->config['plugin_prefix'] . 'agency_';
		$urls     = array();
		$networks = $this->get_networks();

		if ( count( $networks ) > 0 ) {
			foreach ( $networks as $key => $name ) {
				$url = get_post_meta( $this->post->ID, "{$prefix}{$key}_url", true );

				if ( $url ) {
					$urls[ $key ] = array(
						'name' => $name,
						'url'  => $url,
					);
				}
			}
		}

		return $urls;
	} // get_contact_form

	/**
	 * Special getter method for generating the agency's business/social
	 * network icons HTML code.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string Network icons HTML code.
	 */
	private function get_network_icons( $value_getter ) {
		$network_urls = $value_getter( 'network_urls' );
		$items        = array();

		if ( 0 === count( $network_urls ) ) {
			return '';
		}

		foreach ( $network_urls as $key => $network ) {
			$items[] = wp_sprintf(
				'<li><a href="%s" title="%s" target="_blank"><span uk-icon="%s"></span></a></li>',
				$network['url'],
				$network['name'],
				$this->get_network_icon_key( $key )
			);
		}

		$html = wp_sprintf(
			'<ul class="inx-team-network-icons">%1$s%2$s%1$s</ul>',
			PHP_EOL,
			implode( PHP_EOL, $items )
		);

		return apply_filters( 'inx_team_agency_network_icons_output', $html );
	} // get_network_icons

	/**
	 * Special getter method for the agency's contact form.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string Special "do_action" string for use within the output template.
	 */
	private function get_contact_form( $value_getter ) {
		return 'do_action:inx_team_render_contact_form';
	} // get_contact_form

	/**
	 * Query the number of agents linked to the agency.
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool Agent count or false if no agency post is assigned yet.
	 */
	private function get_agent_count() {
		if ( ! is_object( $this->post ) || ! $this->post->ID ) {
			return false;
		}

		$args       = array(
			'fields'     => 'ids',
			'meta_query' => array(
				array(
					'key'   => '_inx_team_agency_id',
					'value' => $this->post->ID,
				),
			),
		);
		$agent_list = new Agent_List( $this->config, $this->utils );

		return count( $agent_list->get_items( $args ) );
	} // get_agent_count

	/**
	 * Query the number of properties linked to the agency.
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool Property count or false if no agency post is assigned yet.
	 */
	private function get_property_count() {
		if ( ! is_object( $this->post ) || ! $this->post->ID ) {
			return 0;
		}

		return apply_filters(
			'inx_get_properties',
			array(),
			array(
				'inx-agency' => $this->post->ID,
				'count'      => true,
			)
		);
	} // get_property_count

} // Agency
