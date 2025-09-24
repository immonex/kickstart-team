<?php
/**
 * Class Agency
 *
 * @package immonex\KickstartTeam
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
		if ( ! $this->post && empty( $atts['is_preview'] ) ) {
			return '';
		}

		$atts['template'] = $template;

		$template_data = $this->get_template_data( $atts );

		return parent::render( $template, $template_data );
	} // render

	/**
	 * Compile and return all relevant data for rendering an agency template.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $atts Rendering Attributes.
	 *
	 * @return mixed[] Agency and related meta data.
	 */
	public function get_template_data( $atts = array() ) {
		if ( ! $this->post ) {
			$post_id = ! empty( $atts['post_id'] ) ? $atts['post_id'] : false;
			if ( ! $post_id ) {
				$post_id = ! empty( $atts['id'] ) ? $atts['id'] : false;
			}
			if ( $post_id ) {
				$this->set_post( $post_id );
			}
		}

		if ( ! $this->post && empty( $atts['is_preview'] ) ) {
			return '';
		}

		$template = isset( $atts['template'] ) ? $atts['template'] : '';

		if ( empty( $atts['type'] ) ) {
			$atts['type'] = 'widget' === substr( $template, 0, 6 ) ? 'widget' : 'single_agency_page';
		}

		$this->link_type = ! empty( $atts['link_type'] ) ? $atts['link_type'] : 'internal';

		$post_types = get_post_types( array( 'name' => $this->post_type_name ), 'objects' );
		if ( isset( $post_types[ $this->post_type_name ] ) ) {
			// @codingStandardsIgnoreLine
			$this->is_public = apply_filters( "{$this->post_type_name}_has_single_view", $post_types[ $this->post_type_name ]->public );
		}

		$permalink_url = empty( $atts['is_preview'] ) ?
			get_permalink( $this->post->ID ) :
			$this->get_preview_value( 'url', $atts );
		$url           = false;

		if ( 'none' !== $this->link_type ) {
			if ( ! empty( $atts['is_preview'] ) ) {
				$url = $this->get_preview_value( 'url', $atts );
			} elseif ( 'external' === $this->link_type ) {
				$url = $this->get_element_value( 'url', $atts );
			} elseif ( $this->post && $this->is_public ) {
				$url = $permalink_url;
			}
		}

		$valid_elements   = $this->get_elements();
		$default_filter   = 'single_agency_page' === $atts['type'] ? 'all' : $atts['type'];
		$default_elements = array_keys( $this->get_elements( $default_filter ) );
		$convert_links    = ! empty( $atts['convert_links'] );
		$template_data    = array(
			'before_title'                  => isset( $atts['before_title'] ) ? html_entity_decode( $atts['before_title'] ) : '',
			'title'                         => isset( $atts['title'] ) ? $atts['title'] : $this->post->post_title,
			'after_title'                   => isset( $atts['after_title'] ) ? html_entity_decode( $atts['after_title'] ) : '',
			'link_type'                     => $this->link_type,
			'convert_links'                 => $convert_links,
			'contact_form_scope'            => ! empty( $atts['contact_form_scope'] ) ? $atts['contact_form_scope'] : '',
			'agency_id'                     => $this->post ? $this->post->ID : 0,
			'is_public'                     => $this->is_public,
			'is_demo'                       => $this->post ? get_post_meta( $this->post->ID, '_immonex_is_demo', true ) : true,
			'permalink_url'                 => $permalink_url,
			'url'                           => $url,
			'agent_count'                   => $this->get_agent_count(),
			'property_count'                => $this->get_property_count(),
			'elements'                      => array(),
			'show_all_elements'             => ! empty( $atts['elements'] ),
			'single_view_optional_sections' => $this->get_single_view_optional_sections( $this->post ? $this->post->ID : false, $atts ),
			'is_preview'                    => ! empty( $atts['is_preview'] ),
		);

		$requested_elements = ! empty( $atts['elements'] ) ?
			$atts['elements'] :
			array();

		if ( ! is_array( $requested_elements ) ) {
			$requested_elements = array_map( 'trim', explode( ',', (string) $requested_elements ) );
		}

		$element_keys = array_unique( ! empty( $requested_elements ) ? $requested_elements : $default_elements );

		if ( count( $element_keys ) > 0 ) {
			foreach ( $valid_elements as $key => $element ) {
				if ( ! in_array( $key, $element_keys, true ) ) {
					continue;
				}

				$value = $this->get_element_value( $key, $atts );
				if ( $convert_links ) {
					$value = $this->maybe_add_link( $value );
				}

				$template_data['elements'][ $key ] = array(
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

		return $template_data;
	} // get_template_data

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
			$is_url = false !== strpos( $data['logo'], '://' );

			if ( $is_url ) {
				$file = $data['logo'];
				$dir  = '';
			} else {
				$file = pathinfo( $data['logo'], PATHINFO_BASENAME );
				$dir  = pathinfo( $data['logo'], PATHINFO_DIRNAME );
			}

			$logo_id = $this->replace_logo( $file, $dir );

			if ( $logo_id && ! is_wp_error( $logo_id ) ) {
				set_post_thumbnail( $this->post->ID, $logo_id );
			}
		}

		update_post_meta( $this->post->ID, "{$this->prefix}update_checksum", 0 );
	} // update

	/**
	 * Update agency post and the meta data based on related OpenImmo XML data.
	 *
	 * @since 1.0.0
	 *
	 * @param \SimpleXMLElement $anbieter Offerer XML object.
	 * @param \SimpleXMLElement $immobilie Property XML object.
	 * @param string            $import_dir Full import directory path.
	 */
	public function update_by_openimmo_xml( $anbieter, $immobilie, $import_dir ) {
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
				|| '//impressum' === substr( $element['xpath'], 0, 11 )
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
		$textarea_element_keys = array(
			'legal_notice',
			'legal_address',
			'legal_misc',
		);

		foreach ( $elements as $key => $element ) {
			if (
				empty( $element['meta_key'] )
				|| ! isset( $element['xpath'] )
				|| '//impressum' !== substr( $element['xpath'], 0, 11 )
			) {
				continue;
			}

			$value = ! empty( $anbieter->xpath( $element['xpath'] ) ) ?
				(string) $anbieter->xpath( $element['xpath'] )[0] : false;

			if ( ! empty( $value ) ) {
				$meta[ $element['meta_key'] ] = in_array( $key, $textarea_element_keys, true ) ?
					sanitize_textarea_field( $value ) :
					sanitize_text_field( $value );
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

		$agency_logo = $anbieter->xpath( 'anhang[@gruppe="ANBIETERLOGO"]' );

		if ( $agency_logo ) {
			$logo_id = $this->replace_logo( $agency_logo[0], $import_dir );

			if ( $logo_id && ! is_wp_error( $logo_id ) ) {
				set_post_thumbnail( $this->post->ID, $logo_id );
			}
		}

		$xml_checksum = $this->get_checksum( $anbieter );
		update_post_meta( $this->post->ID, "{$this->prefix}update_checksum", $xml_checksum );
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
	public function get_company_from_xml( $anbieter, $immobilie ) {
		$company = (string) $immobilie->kontaktperson->firma;
		if ( ! empty( $anbieter ) ) {
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
			if ( is_a( $this->post, 'WP_Post' ) ) {
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
		if ( ! empty( $this->cache['elements'] ) ) {
			$elements = $this->cache['elements'];
		} else {
			$icon_mail     = '<span uk-icon="mail"></span>';
			$icon_receiver = '<span uk-icon="receiver"></span>';
			$icon_print    = '<span uk-icon="print"></span>';
			$icon_location = '<span uk-icon="location"></span>';

			$elements = array(
				'logo'                        => array(
					'label'                 => __( 'Logo', 'immonex-kickstart-team' ),
					'compose_cb'            => array( '$this', 'get_featured_image' ),
					'selectable_for_output' => true,
					'default_show'          => array( 'widget' ),
					'section_order'         => 10,
				),
				'company'                     => array(
					'label'                 => __( 'Company', 'immonex-kickstart-team' ),
					'compose_cb'            => array( '$this', 'get_company' ),
					'selectable_for_output' => true,
					'default_show'          => array( 'widget' ),
					'section_order'         => 20,
				),
				'about'                       => array(
					'label'                 => __( 'About', 'immonex-kickstart-team' ),
					'post_data'             => 'post_content',
					'selectable_for_output' => true,
					'default_show'          => array( 'single_agency_page' ),
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
					'compose_cb'            => array( '$this', 'get_address_multi_line' ),
					'selectable_for_output' => true,
					'default_show'          => array( 'single_agency_page' ),
					'section_order'         => 90,
				),
				'address_single_line'         => array(
					'label'                 => __( 'Address (single line)', 'immonex-kickstart-team' ),
					'icon'                  => $icon_location,
					'compose_cb'            => array( '$this', 'get_address_single_line' ),
					'selectable_for_output' => false,
					'default_show'          => array(),
					'section_order'         => 90,
				),
				'legal_notice_auto_select'    => array(
					'label'                 => __( 'Legal Notice', 'immonex-kickstart-team' ),
					'compose_cb'            => array( '$this', 'get_legal_notice_auto_select' ),
					'selectable_for_output' => false,
					'default_show'          => array(),
					'section_order'         => 100,
				),
				'legal_notice'                => array(
					'label'                 => __( 'Legal Notice', 'immonex-kickstart-team' ),
					'meta_key'              => "{$this->prefix}legal_notice",
					'xpath'                 => '//impressum',
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
				'coords'                      => array(
					'label'                 => __( 'Geo Coordinates', 'immonex-kickstart-team' ),
					'compose_cb'            => array( $this, 'get_coords' ),
					'selectable_for_output' => false,
					'default_show'          => array(),
					'section_order'         => 200,
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

			$elements = apply_filters(
				'inx_team_agency_elements',
				$elements,
				is_a( $this->post, 'WP_Post' ) ? $this->post->ID : 0
			);

			array_multisort(
				array_column( $elements, 'section_order' ),
				SORT_ASC,
				array_column( $elements, 'order' ),
				SORT_ASC,
				$elements
			);

			$elements                = $this->convert_callables( $elements );
			$this->cache['elements'] = $elements;
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

		return $elements;
	} // get_elements

	/**
	 * Determine the value of a single agency element.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $key Element key (name).
	 * @param mixed[] $atts Rendering Attributes (optional).
	 *
	 * @return mixed Element value or false if indeterminable.
	 */
	public function get_element_value( $key, $atts = array() ) {
		if ( ! $this->post && empty( $atts['is_preview'] ) ) {
			return false;
		}

		if ( ! empty( $this->cache['element_values'][ $key ] ) ) {
			return $this->cache['element_values'][ $key ];
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
		} elseif ( ! empty( $element['post_data'] ) && $this->post ) {
			$value = apply_filters( 'inx_the_content', $this->post->{$element['post_data']} );
		} elseif ( ! empty( $element['meta_key'] ) && $this->post ) {
			$value = get_post_meta( $this->post->ID, $element['meta_key'], true );
		}

		if ( ! $value && empty( $atts['id'] ) && ! empty( $atts['is_preview'] ) ) {
			$value = $this->get_preview_value( $key, $atts );
		}

		if (
			$value
			&& 'url' === $key
			&& 'http' !== strtolower( substr( $value, 0, 4 ) )
		) {
			$value = "https://{$value}";
		}

		if ( empty( $atts['is_preview'] ) ) {
			$value = apply_filters(
				'inx_team_agency_element_value',
				$value,
				$key,
				$this->post ? $this->post->ID : 0,
				$atts
			);
		}

		if ( $value ) {
			$this->cache['element_values'][ $key ] = $value;
		}

		return $value;
	} // get_element_value

	/**
	 * Replace the agency's logo file (thumbnail/featured image) if different than
	 * the given (new) one.
	 *
	 * @since 1.4.7-beta
	 *
	 * @param \SimpleXMLElement|string $logo Agency Logo XML element, local path or URL
	 *                                       of a (possibly) new logo file.
	 * @param string                   $dir Directory (for local files, optional).
	 *
	 * @return int|bool|\WP_Error Attachment ID of new or existing photo, false or WP_Error on error.
	 */
	public function replace_logo( $logo, $dir = '' ) {
		if ( ! $this->post ) {
			return false;
		}

		if ( is_object( $logo ) ) {
			$path_or_url = (string) $logo->daten->pfad;
			$is_remote   = 'REMOTE' === strtoupper( (string) $logo['location'] )
				|| false !== strpos( $path_or_url, '://' );
		} else {
			$path_or_url = $logo;
			$is_remote   = false !== strpos( $path_or_url, '://' );
		}

		if ( ! $is_remote && ! $dir ) {
			return false;
		}
		if ( $is_remote ) {
			if ( ! $this->utils['general']->remote_file_exists( $path_or_url ) ) {
				// Remote image file not found or accessible.
				return false;
			}

			$filesize = (int) $this->utils['general']->get_remote_filesize( $path_or_url );
		} else {
			$path_or_url = "{$dir}/{$path_or_url}";
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
			$temp = download_url( $path_or_url );
			if ( is_wp_error( $temp ) ) {
				return $temp;
			}

			$file_data = array(
				'name'     => basename( $path_or_url ),
				'tmp_name' => $temp,
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

		$desc   = $this->post->post_title . ' ' . __( 'Logo', 'immonex-kickstart-team' );
		$result = media_handle_sideload( $file_data, $this->post->ID, $desc );

		if ( ! empty( $temp ) && is_string( $temp ) && file_exists( $temp ) ) {
			// @codingStandardsIgnoreLine
			@unlink( $temp );
		}

		return $result;
	} // replace_logo

	/**
	 * Maybe create a set of demo data and get an example value for the
	 * given key (preview purposes).
	 *
	 * @since 1.5.7-beta
	 *
	 * @param string  $key Element key (name).
	 * @param mixed[] $atts Rendering Attributes (optional).
	 *
	 * @return mixed Example value or false if indeterminable.
	 */
	protected function get_preview_value( $key, $atts = array() ) {
		if ( empty( $this->demo_data ) ) {
			$this->preview_data = array(
				'company'                     => array(
					'raw'  => _x( 'ONE Realty Group', 'Sample data', 'immonex-kickstart-team' ),
					'link' => 'none' !== $this->link_type ? wp_sprintf(
						'<a href="https://immonex.one/" target="_blank">%s</a>',
						_x( 'ONE Realty Group', 'Sample data', 'immonex-kickstart-team' )
					) : '',
				),
				'about'                       => _x(
					'ONE Realty Group is a leading real estate company in the region. We offer a wide range of services for buying, selling and renting properties.',
					'Sample data',
					'immonex-kickstart-team'
				),
				'email'                       => _x( 'hello@immonex.one', 'Sample data', 'immonex-kickstart-team' ),
				'phone'                       => _x( '+999 123 4567890', 'Sample data', 'immonex-kickstart-team' ),
				'url'                         => '#',
				'street'                      => _x( 'Fake Street', 'Sample data', 'immonex-kickstart-team' ),
				'house_number'                => '123',
				'zip_code'                    => '99999',
				'city'                        => _x( 'Demotown', 'Sample data', 'immonex-kickstart-team' ),
				'address_publishing_approved' => true,
				'address'                     => _x( 'Fake Street 123<br>99999 Demotown', 'Sample data', 'immonex-kickstart-team' ),
				'address_single_line'         => _x( 'Fake Street 123, 99999 Demotown', 'Sample data', 'immonex-kickstart-team' ),
				'network_urls'                => array(
					array(
						'name' => 'X',
						'url'  => 'https://x.com/immonexhq',
					),
					array(
						'name' => 'Facebook',
						'url'  => 'https://facebook.com/immonex',
					),
				),
				'network_icons'               => PHP_EOL
					. '<ul class="inx-team-network-icons">' . PHP_EOL
					. '<li><a href="https://x.com/immonexhq" title="X" target="_blank"><span uk-icon="x"></span></a></li>' . PHP_EOL
					. '<li><a href="https://facebook.com/immonex" title="Facebook" target="_blank"><span uk-icon="facebook"></span></a></li>' . PHP_EOL
					. '</ul>',
				'legal_notice_auto_select'    => array(
					'raw'  => __(
						'Publisher

ONE Realty Group
123 Fake Street
Demotown, 99999

Email: hello@immonex.one
Phone: +999 123 4567890

Represented by Elena Example and Dave Demotown

Supervisory Authority (§ 34c GewO)

Demotown Chamber of Commerce and Industry',
						'immonex-kickstart-team'
					),
					'html' => __(
						'<strong>Publisher</strong>
<p>
	ONE Realty Group<br>
	123 Fake Street<br>
	Demotown, 99999
</p>

<p>
	Email: <a href="mailto:hello@immonex.one">hello@immonex.one</a><br>
	Phone: +999 123 4567890
</p>

<p>Represented by Elena Example and Dave Demotown</p>

<strong>Supervisory Authority (§ 34c GewO)</strong>
<p>
	Demotown Chamber of Commerce and Industry
</p>',
						'immonex-kickstart-team'
					),
				),
			);
		}

		return parent::get_preview_value( $key, $atts );
	} // get_preview_value

	/**
	 * Special getter method for the agency's company name.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[]|string Array containing raw company name and (eventually) a link tag
	 *                        or empty string if indeterminable.
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
	 * Special getter method for automatically selecting/composing the agency's
	 * legal notice.
	 *
	 * @since 1.6.5-beta
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[]|string Array containing plain text (raw) and HTML version
	 *                        or empty string if indeterminable.
	 */
	private function get_legal_notice_auto_select( $value_getter ) {
		$legal_notice = call_user_func( $value_getter, 'legal_notice' );

		if ( $legal_notice ) {
			return array(
				'raw'  => trim( $legal_notice ) . PHP_EOL,
				'html' => wpautop( trim( $this->utils['string']->convert_urls( $legal_notice ) ) ) . PHP_EOL,
			);
		}

		$sections = array(
			'company_address'       => array(),
			'contact_data'          => array(),
			'representative'        => array(),
			'trade_register_vatin'  => array(),
			'supervisory_authority' => array(),
			'misc'                  => array(),
		);

		$section_headlines = array(
			'company_address'       => __( 'Publisher', 'immonex-kickstart-team' ),
			'supervisory_authority' => __( 'Supervisory Authority', 'immonex-kickstart-team' ) . ' (§ 34c GewO)',
		);

		$company = call_user_func( $value_getter, 'legal_company' );
		if ( ! $company ) {
			$company_data = call_user_func( $value_getter, 'company' );

			if ( $company_data ) {
				$company = $company_data['raw'];
			}
		}
		if ( $company ) {
			$sections['company_address'][] = $company;
		}

		$address = call_user_func( $value_getter, 'legal_address' );
		if ( ! $address ) {
			$address = $this->get_address( $value_getter, PHP_EOL );
		}
		if ( $address ) {
			$sections['company_address'][] = $address;
		}

		$phone = call_user_func( $value_getter, 'legal_phone' );
		if ( ! $phone ) {
			$phone = call_user_func( $value_getter, 'phone' );
		}
		if ( $phone ) {
			$sections['contact_data'][] = wp_sprintf( '%s: %s', __( 'Phone', 'immonex-kickstart-team' ), $phone );
		}

		$email = call_user_func( $value_getter, 'email' );
		if ( $email ) {
			$sections['contact_data'][] = wp_sprintf( '%s: %s', __( 'Email', 'immonex-kickstart-team' ), $email );
		}

		$representative = call_user_func( $value_getter, 'representative' );
		if ( $representative ) {
			$sections['representative'][] = wp_sprintf( '%s %s', __( 'Represented by', 'immonex-kickstart-team' ), $representative );
		}

		$trade_register = array();
		$registry_court = call_user_func( $value_getter, 'registry_court' );
		if ( $registry_court ) {
			$trade_register[] = $registry_court;
		}
		$trade_register_number = call_user_func( $value_getter, 'trade_register_number' );
		if ( $trade_register_number ) {
			$trade_register[] = $trade_register_number;
		}
		if ( ! empty( $trade_register ) ) {
			$sections['trade_register_vatin'][] = wp_sprintf(
				'%s: %s',
				__( 'Trade Register', 'immonex-kickstart-team' ),
				implode( ', ', $trade_register )
			);
		}

		$vatin = call_user_func( $value_getter, 'vatin' );
		if ( $vatin ) {
			$sections['trade_register_vatin'][] = wp_sprintf( '%s: %s', __( 'VATIN', 'immonex-kickstart-team' ), $vatin );
		}

		$supervisory_authority = call_user_func( $value_getter, 'supervisory_authority' );
		if ( $supervisory_authority ) {
			$sections['supervisory_authority'][] = $supervisory_authority;
		}

		$misc = call_user_func( $value_getter, 'legal_misc' );
		if ( $misc ) {
			$sections['misc'][] = $misc;
		}

		$raw  = '';
		$html = '';

		foreach ( $sections as $section => $contents ) {
			if ( empty( $contents ) ) {
				continue;
			}

			if ( ! empty( $section_headlines[ $section ] ) ) {
				$raw  .= $section_headlines[ $section ] . PHP_EOL . PHP_EOL;
				$html .= wp_sprintf( '<strong>%s</strong>', $section_headlines[ $section ] ) . PHP_EOL;
			}

			$joint_contents = implode( PHP_EOL, $contents );
			$raw           .= $joint_contents . PHP_EOL . PHP_EOL;
			$html          .= wpautop( $this->utils['string']->convert_urls( $joint_contents ) ) . PHP_EOL;
		}

		return trim( $raw ) ?
			array(
				'raw'  => $raw,
				'html' => $html,
			) :
			'';
	} // get_legal_notice_auto_select

	/**
	 * Calculate a simple checksum based on the length of serialized core
	 * agency XML data.
	 *
	 * @since 1.4.7-beta
	 *
	 * @param \SimpleXMLElement $anbieter Offerer XML object.
	 *
	 * @return int Checksum.
	 */
	public function get_checksum( $anbieter ) {
		$elements = array(
			'anbieternr',
			'firma',
			'openimmo_anid',
			'anhang',
			'impressum',
			'impressum_strukt',
		);

		$checksum = 0;
		foreach ( $elements as $element ) {
			if ( $anbieter->{$element} ) {
				$checksum += strlen( $anbieter->{$element}->asXML() );
			}
		}

		return $checksum;
	} // get_checksum

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
		if ( ! is_a( $this->post, 'WP_Post' ) || ! $this->post->ID ) {
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
		if ( ! is_a( $this->post, 'WP_Post' ) || ! $this->post->ID ) {
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

	/**
	 * Evaluate optional section display in single views.
	 *
	 * @since 1.3.7
	 *
	 * @param int|string $post_id Agency post ID.
	 * @param mixed[]    $atts Rendering attributes (optional).
	 *
	 * @return string[] Keys of optional sections to display.
	 */
	private function get_single_view_optional_sections( $post_id, $atts = array() ) {
		$sections = $this->config['agency_single_view_optional_sections'];

		if ( ! $post_id && empty( $atts['is_preview'] ) ) {
			return $sections;
		}

		// Custom field name (meta key excl. prefix)/shortcode attribute => section key.
		$option_mapping = array(
			'show_agent_list'    => 'agents',
			'show_property_list' => 'properties',
			'show_legal_notice'  => 'legal_notice',
		);

		foreach ( $option_mapping as $cf_name => $section ) {
			$meta_value = '';

			if ( isset( $atts[ $cf_name ] ) ) {
				// Shortcode attributes may override eponymous custom field values.
				if ( in_array( $atts[ $cf_name ], array( 'yes', '1' ), true ) ) {
					$meta_value = 'yes';
				} elseif ( in_array( $atts[ $cf_name ], array( 'no', '0' ), true ) ) {
					$meta_value = 'no';
				}
			}

			if ( $post_id && ! $meta_value ) {
				$meta_key   = '_' . $this->config['plugin_prefix'] . $this->base_name . '_' . $cf_name;
				$meta_value = get_post_meta( $post_id, $meta_key, true );
			}

			if ( 'yes' === $meta_value && ! in_array( $section, $sections, true ) ) {
				$sections[] = $section;
			} elseif ( 'no' === $meta_value && in_array( $section, $sections, true ) ) {
				$sections = array_diff( $sections, array( $section ) );
			}
		}

		return $sections;
	} // get_single_view_optional_sections

} // Agency
