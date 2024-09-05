<?php
/**
 * Class Agent
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

use \immonex\Kickstart\Kickstart;

/**
 * Agent CPT rendering
 */
class Agent extends Base_CPT_Post {

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
	 * ID of related agency post
	 *
	 * @var int|string|bool
	 */
	private $agency_id = false;

	/**
	 * "Public flag" of related agency
	 *
	 * @var bool
	 */
	private $is_public_agency = false;

	/**
	 * Element value cache
	 *
	 * @var mixed[]
	 */
	private $element_values = array();

	/**
	 * (Re)Set the current agent post ID/object and the related agency ID.
	 *
	 * @since 1.3.0
	 *
	 * @param \WP_Post|int|string|bool $post_or_id Agent post object or ID (false if undefined).
	 */
	public function set_post( $post_or_id ) {
		parent::set_post( $post_or_id );

		if ( $this->post ) {
			$this->agency_id = get_post_meta( $this->post->ID, '_inx_team_agency_id', true );
		}
	} // set_post

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

		$atts['template'] = $template;

		$template_data = $this->get_template_data( $atts );

		return parent::render( $template, $template_data );
	} // render

	/**
	 * Compile and return all relevant data for rendering an agent template.
	 *
	 * @since 1.3.0
	 *
	 * @param mixed[] $atts Rendering Attributes.
	 *
	 * @return mixed[] Agent and related meta data.
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

		if ( ! $this->post ) {
			return '';
		}

		$template = isset( $atts['template'] ) ? $atts['template'] : '';

		if ( empty( $atts['type'] ) ) {
			$atts['type'] = 'widget' === substr( $template, 0, 6 ) ? 'widget' : 'single_agent_page';
		}

		$this->link_type = ! empty( $atts['link_type'] ) ? $atts['link_type'] : 'internal';

		$post_types = get_post_types( array(), 'objects' );
		if ( isset( $post_types[ $this->post_type_name ] ) ) {
			// @codingStandardsIgnoreLine
			$this->is_public = apply_filters( "{$this->post_type_name}_has_single_view", $post_types[ $this->post_type_name ]->public );
		}
		if ( isset( $post_types['inx_agency'] ) ) {
			$this->is_public_agency = apply_filters( 'inx_agency_has_single_view', $post_types['inx_agency']->public );
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
		$default_filter   = in_array(
			$atts['type'],
			array(
				'single_agent_page',
				'default_contact_element_replacement',
			),
			true
		) ? 'all' : $atts['type'];
		$default_elements = array_keys( $this->get_elements( $default_filter ) );
		$convert_links    = ! empty( $atts['convert_links'] );
		$template_data    = array(
			'type'                          => $atts['type'],
			'before_title'                  => isset( $atts['before_title'] ) ? $atts['before_title'] : '',
			'title'                         => isset( $atts['title'] ) ? $atts['title'] : $this->post->post_title,
			'after_title'                   => isset( $atts['after_title'] ) ? $atts['after_title'] : '',
			'link_type'                     => $this->link_type,
			'convert_links'                 => $convert_links,
			'contact_form_scope'            => ! empty( $atts['contact_form_scope'] ) ? $atts['contact_form_scope'] : '',
			'agent_id'                      => $this->post->ID,
			'agent_gender'                  => $this->get_element_value( 'gender' ),
			'agency_id'                     => $this->agency_id,
			'is_public'                     => $this->is_public,
			'is_public_agency'              => $this->is_public_agency,
			'is_demo'                       => get_post_meta( $this->post->ID, '_immonex_is_demo', true ),
			'url'                           => $url,
			'property_count'                => $this->get_property_count(),
			'elements'                      => array(),
			'show_all_elements'             => ! empty( $atts['elements'] ),
			'single_view_optional_sections' => $this->get_single_view_optional_sections( $this->post->ID ),
		);

		$requested_elements = ! empty( $atts['elements'] ) ?
			$atts['elements'] :
			array();

		if ( ! is_array( $requested_elements ) ) {
			$requested_elements = array_map( 'trim', explode( ',', (string) $requested_elements ) );
		}

		$element_keys = ! empty( $requested_elements ) ? $requested_elements : $default_elements;
		$element_keys = array_unique( $element_keys );

		if ( count( $element_keys ) > 0 ) {
			foreach ( $valid_elements as $key => $element ) {
				if ( ! in_array( $key, $element_keys, true ) ) {
					continue;
				}

				$value = $this->get_element_value( $key );
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
	 * Update an agent post and the related meta data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $data Agent data.
	 */
	public function update( $data ) {
		if ( ! $this->post ) {
			return;
		}

		$elements  = $this->get_elements();
		$post_data = array();
		$meta      = array();

		$post_title_parts = array();
		if ( ! empty( $data['first_name'] ) ) {
			$post_title_parts[] = sanitize_text_field( $data['first_name'] );
			$first_name         = $data['first_name'];
		}
		if ( ! empty( $data['last_name'] ) ) {
			$post_title_parts[] = sanitize_text_field( $data['last_name'] );
		}

		if ( count( $post_title_parts ) > 0 ) {
			$post_data['post_title'] = implode( ' ', $post_title_parts );
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

		if (
			! isset( $meta[ "{$this->prefix}gender" ] )
			&& ! get_post_meta( $this->post->ID, "{$this->prefix}gender", true )
		) {
			if ( empty( $first_name ) ) {
				$first_name = get_post_meta( $this->post->ID, "{$this->prefix}first_name", true );
			}
			$meta[ "{$this->prefix}gender" ] = $this->get_gender( '', $first_name );
		}

		$update = apply_filters(
			'inx_team_update_agent_data',
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

		if ( ! empty( $data['photo'] ) ) {
			$name   = $this->post->post_title;
			$is_url = false !== strpos( $data['photo'], '://' );

			if ( $is_url ) {
				$file = $data['photo'];
				$dir  = '';
			} else {
				$file = pathinfo( $data['photo'], PATHINFO_BASENAME );
				$dir  = pathinfo( $data['photo'], PATHINFO_DIRNAME );
			}

			$photo_id = $this->replace_photo( $file, $name, $dir );

			if ( $photo_id && ! is_wp_error( $photo_id ) ) {
				set_post_thumbnail( $this->post->ID, $photo_id );
			}
		}

		update_post_meta( $this->post->ID, "{$this->prefix}update_checksum", 0 );
	} // update

	/**
	 * Update agent post and meta data based on related OpenImmo XML data.
	 *
	 * @since 1.0.0
	 *
	 * @param \SimpleXMLElement $immobilie Property XML object.
	 * @param string            $import_dir Full import directory path.
	 */
	public function update_by_openimmo_xml( $immobilie, $import_dir = '' ) {
		if ( ! $this->post ) {
			return;
		}

		$kontaktperson = $immobilie->kontaktperson;
		$first_name    = isset( $kontaktperson->vorname ) ?
			(string) $kontaktperson->vorname : '';
		$last_name     = isset( $kontaktperson->name ) ?
			(string) $kontaktperson->name : '';
		$company       = isset( $kontaktperson->firma ) ?
			(string) $kontaktperson->firma : '';

		$title = trim( "{$first_name} {$last_name}" );
		if ( ! $title && $company ) {
			$title = $company;
		}

		$post_data = array(
			'post_title' => sanitize_text_field( $title ),
		);
		$elements  = $this->get_elements();
		$meta      = array();

		if ( (string) $kontaktperson->freitextfeld ) {
			$post_data['post_content'] = sanitize_textarea_field( (string) $kontaktperson->freitextfeld );
		}

		$meta[ "{$this->prefix}address_publishing_approved" ] = in_array(
			(string) $kontaktperson->adressfreigabe,
			array( '0', 'false' ),
			true
		) ? '0' : '1';

		$meta[ "{$this->prefix}gender" ] = $this->get_gender(
			(string) $kontaktperson->anrede,
			(string) $kontaktperson->vorname
		);

		foreach ( $elements as $key => $element ) {
			if (
				empty( $element['xpath'] )
				|| empty( $element['meta_key'] )
			) {
				continue;
			}

			$value = isset( $immobilie->xpath( $element['xpath'] )[0] ) ?
				(string) $immobilie->xpath( $element['xpath'] )[0] :
				'';
			if ( ! empty( $value ) ) {
				$meta[ $element['meta_key'] ] = sanitize_textarea_field( $value );
			}
		}

		$update = apply_filters(
			'inx_team_update_agent_data',
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

		if ( $kontaktperson->foto ) {
			$name     = $post_data['post_title'];
			$photo_id = $this->replace_photo( $kontaktperson->foto, $name, $import_dir );

			if ( $photo_id && ! is_wp_error( $photo_id ) ) {
				set_post_thumbnail( $this->post->ID, $photo_id );
			}
		}

		$xml_checksum = strlen( $kontaktperson->asXML() );
		update_post_meta( $this->post->ID, "{$this->prefix}update_checksum", $xml_checksum );
	} // update_by_openimmo_xml

	/**
	 * Replace the agent's photo file (thumbnail/featured image) if different than
	 * the given (new) one.
	 *
	 * @since 1.0.0
	 *
	 * @param \SimpleXMLElement|string $foto Photo XML element, local path or URL
	 *                                       of a (possibly) new logo file.
	 * @param string                   $name Photo name/title.
	 * @param string                   $dir Directory (for local files, optional).
	 *
	 * @return int|bool|\WP_Error Attachment ID of new or existing photo, false or WP_Error on error.
	 */
	public function replace_photo( $foto, $name, $dir = '' ) {
		if ( ! $this->post ) {
			return false;
		}

		if ( is_object( $foto ) ) {
			$path_or_url = (string) $foto->daten->pfad;
			$is_remote   = 'REMOTE' === strtoupper( (string) $foto['location'] )
				|| false !== strpos( $path_or_url, '://' );
		} else {
			$path_or_url = $foto;
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

		$photo_id = get_post_thumbnail_id( $this->post->ID );

		if ( $photo_id ) {
			$att = wp_prepare_attachment_for_js( $photo_id );

			if (
				basename( $path_or_url ) === $att['filename']
				&& $att['filesizeInBytes'] === $filesize
			) {
				// Image already exists.
				return $photo_id;
			}

			wp_delete_attachment( $photo_id, true );
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

		$result = media_handle_sideload( $file_data, $this->post->ID, $name );

		if ( ! empty( $temp ) && is_string( $temp ) && file_exists( $temp ) ) {
			// @codingStandardsIgnoreLine
			@unlink( $temp );
		}

		return $result;
	} // replace_photo

	/**
	 * Generate an agent related meta query.
	 *
	 * @since 1.0.0
	 *
	 * @param int[]|string[]|bool $agent_ids Agent IDs for query.
	 * @param bool                $primary Indicate if only primary agents shall
	 *                                     be queried.
	 *
	 * @return mixed[]|bool Meta query array or false on invalid ID data.
	 */
	public function get_agent_meta_query( $agent_ids = false, $primary = false ) {
		if ( empty( $agent_ids ) ) {
			if ( is_a( $this->post, 'WP_Post' ) ) {
				$agent_ids = array( $this->post->ID );
			} else {
				return false;
			}
		}

		if ( ! is_array( $agent_ids ) ) {
			$agent_ids = array( $agent_ids );
		}

		if ( $primary ) {
			$agent_query = array(
				'key'   => "{$this->prefix}primary",
				'value' => (int) $agent_ids[0],
			);
		} else {
			$agent_query = array( 'relation' => 'OR' );

			foreach ( $agent_ids as $agent_id ) {
				$agent_query[] = array(
					'key'   => "{$this->prefix}primary",
					'value' => (int) $agent_id,
				);
				$agent_query[] = array(
					'key'     => rtrim( $this->prefix, '_' ) . 's',
					'value'   => '"' . (int) $agent_id . '"',
					'compare' => 'LIKE',
				);
			}
		}

		return $agent_query;
	} // get_agent_meta_query

	/**
	 * Generate a filtered list of agent element data for output and
	 * selection purposes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $filter Filter term (default: "all").
	 *
	 * @return mixed[] Filtered elements.
	 */
	public function get_elements( $filter = 'all' ) {
		$agency_prefix = '_' . $this->config['plugin_prefix'] . 'agency_';
		$icon_mail     = '<span uk-icon="mail"></span>';
		$icon_receiver = '<span uk-icon="receiver"></span>';
		$icon_mobile   = '<span uk-icon="phone"></span>';
		$icon_print    = '<span uk-icon="print"></span>';
		$icon_location = '<span uk-icon="location"></span>';

		$elements = array(
			'photo'                       => array(
				'label'                 => __( 'Photo', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_featured_image' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget' ),
				'section_order'         => 10,
			),
			'gender'                      => array(
				'label'                 => __( 'Gender', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}gender",
				'selectable_for_output' => false,
				'default_show'          => array(),
				'default'               => '',
			),
			'title'                       => array(
				'label'                 => __( 'Title', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}title",
				'xpath'                 => '//kontaktperson/titel',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'first_name'                  => array(
				'label'                 => __( 'First Name', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}first_name",
				'xpath'                 => '//kontaktperson/vorname',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'last_name'                   => array(
				'label'                 => __( 'Last Name', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}last_name",
				'xpath'                 => '//kontaktperson/name',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'full_name'                   => array(
				'label'                 => __( 'Full Name', 'immonex-kickstart-team' ),
				'consists_of'           => array( 'first_name', 'last_name' ),
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 10,
			),
			'full_name_incl_title'        => array(
				'label'                      => __( 'Full Name incl. Title', 'immonex-kickstart-team' ),
				'description'                => __( 'Disables &quot;Full Name&quot; if set.', 'immonex-kickstart-team' ),
				'consists_of'                => array( 'title', 'first_name', 'last_name' ),
				'agency_fallback_post_field' => 'post_title',
				'selectable_for_output'      => true,
				'default_show'               => array( 'widget' ),
				'section_order'              => 10,
			),
			'position'                    => array(
				'label'                 => __( 'Position', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}position",
				'xpath'                 => '//kontaktperson/position',
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 10,
			),
			'position_incl_company'       => array(
				'label'                 => __( 'Position incl. Company', 'immonex-kickstart-team' ),
				'description'           => __( 'Position plus Company link <strong>if more than one agency exists</strong>. Disables &quot;Position&quot; if set.', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_position_incl_company' ),
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 10,
			),
			'bio'                         => array(
				'label'                 => __( 'Short Biography', 'immonex-kickstart-team' ),
				'post_data'             => 'post_content',
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 15,
			),
			'email_auto_select'           => array(
				'label'                 => __( 'Email', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_mail,
				'compose_cb'            => array( $this, 'get_email_auto_select' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget', 'default_contact_element_replacement', 'single_agent_page', 'list_item' ),
				'section_order'         => 20,
			),
			'email'                       => array(
				'label'                 => __( 'Email (direct)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_mail,
				'meta_key'              => "{$this->prefix}email",
				'xpath'                 => '//kontaktperson/email_direkt',
				'selectable_for_output' => false,
				'default_show'          => array( 'single_agent_page' ),
				'section_order'         => 20,
			),
			'email_main_office'           => array(
				'label'                    => __( 'Email (Main Office)', 'immonex-kickstart-team' ),
				'show_label'               => true,
				'icon'                     => $icon_mail,
				'meta_key'                 => "{$this->prefix}email_main_office",
				'xpath'                    => '//kontaktperson/email_zentrale',
				'agency_fallback_meta_key' => "{$agency_prefix}email",
				'selectable_for_output'    => false,
				'default_show'             => array(),
				'section_order'            => 20,
			),
			'email_feedback'              => array(
				'label'                 => __( 'Email (Feedback)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_mail,
				'meta_key'              => "{$this->prefix}email_feedback",
				'xpath'                 => '//kontaktperson/email_feedback',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 20,
			),
			'email_private'               => array(
				'label'                 => __( 'Email (private)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_mail,
				'meta_key'              => "{$this->prefix}email_private",
				'xpath'                 => '//kontaktperson/email_privat',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 20,
			),
			'phone_auto_select'           => array(
				'label'                 => __( 'Phone', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_receiver,
				'compose_cb'            => array( $this, 'get_phone_auto_select' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget', 'default_contact_element_replacement', 'single_agent_page', 'list_item' ),
				'section_order'         => 30,
			),
			'phone'                       => array(
				'label'                 => __( 'Phone (call-through)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_receiver,
				'meta_key'              => "{$this->prefix}phone",
				'xpath'                 => '//kontaktperson/tel_durchw',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 30,
			),
			'phone_main_office'           => array(
				'label'                    => __( 'Phone (Main Office)', 'immonex-kickstart-team' ),
				'show_label'               => true,
				'icon'                     => $icon_receiver,
				'meta_key'                 => "{$this->prefix}phone_main_office",
				'xpath'                    => '//kontaktperson/tel_zentrale',
				'agency_fallback_meta_key' => "{$agency_prefix}phone",
				'selectable_for_output'    => false,
				'default_show'             => array(),
				'section_order'            => 30,
			),
			'phone_mobile'                => array(
				'label'                 => __( 'Phone (mobile)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_mobile,
				'meta_key'              => "{$this->prefix}phone_mobile",
				'xpath'                 => '//kontaktperson/tel_handy',
				'selectable_for_output' => true,
				'default_show'          => array( 'default_contact_element_replacement', 'single_agent_page', 'list_item' ),
				'section_order'         => 30,
			),
			'phone_private'               => array(
				'label'                 => __( 'Phone (private)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_receiver,
				'meta_key'              => "{$this->prefix}phone_private",
				'xpath'                 => '//kontaktperson/tel_privat',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 30,
			),
			'phone_other'                 => array(
				'label'                 => __( 'Phone (other)', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'icon'                  => $icon_receiver,
				'meta_key'              => "{$this->prefix}phone_other",
				'xpath'                 => '//kontaktperson/tel_sonstige',
				'selectable_for_output' => false,
				'default_show'          => array( 'single_agent_page' ),
				'section_order'         => 30,
			),
			'fax'                         => array(
				'label'                    => __( 'Fax', 'immonex-kickstart-team' ),
				'show_label'               => true,
				'icon'                     => $icon_print,
				'meta_key'                 => "{$this->prefix}fax",
				'xpath'                    => '//kontaktperson/tel_fax',
				'agency_fallback_meta_key' => "{$agency_prefix}fax",
				'selectable_for_output'    => false,
				'default_show'             => array( 'single_agent_page' ),
				'section_order'            => 30,
			),
			'url'                         => array(
				'label'                    => __( 'URL (Company)', 'immonex-kickstart-team' ),
				'meta_key'                 => "{$this->prefix}url",
				'xpath'                    => '//kontaktperson/url',
				'agency_fallback_meta_key' => "{$agency_prefix}url",
				'selectable_for_output'    => false,
				'default_show'             => array(),
			),
			'company'                     => array(
				'label'                      => __( 'Company', 'immonex-kickstart-team' ),
				'meta_key'                   => "{$this->prefix}company",
				'xpath'                      => '//kontaktperson/firma',
				'agency_fallback_post_field' => 'post_title',
				'selectable_for_output'      => false,
				'default_show'               => array(),
				'section_order'              => 40,
			),
			'company_link'                => array(
				'label'                 => __( 'Company (Link)', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_company_link' ),
				'selectable_for_output' => true,
				'default_show'          => array(),
				'section_order'         => 40,
			),
			'street'                      => array(
				'label'                    => __( 'Street', 'immonex-kickstart-team' ),
				'meta_key'                 => "{$this->prefix}street",
				'xpath'                    => '//kontaktperson/strasse',
				'agency_fallback_meta_key' => "{$agency_prefix}street",
				'selectable_for_output'    => false,
				'default_show'             => array(),
				'section_order'            => 40,
			),
			'house_number'                => array(
				'label'                    => __( 'House Number', 'immonex-kickstart-team' ),
				'meta_key'                 => "{$this->prefix}house_number",
				'xpath'                    => '//kontaktperson/hausnummer',
				'agency_fallback_meta_key' => "{$agency_prefix}house_number",
				'selectable_for_output'    => false,
				'default_show'             => array(),
				'section_order'            => 40,
			),
			'zip_code'                    => array(
				'label'                    => __( 'ZIP Code', 'immonex-kickstart-team' ),
				'meta_key'                 => "{$this->prefix}zip_code",
				'xpath'                    => '//kontaktperson/plz',
				'agency_fallback_meta_key' => "{$agency_prefix}zip_code",
				'selectable_for_output'    => false,
				'default_show'             => array(),
				'section_order'            => 40,
			),
			'city'                        => array(
				'label'                    => __( 'City', 'immonex-kickstart-team' ),
				'meta_key'                 => "{$this->prefix}city",
				'xpath'                    => '//kontaktperson/ort',
				'agency_fallback_meta_key' => "{$agency_prefix}city",
				'selectable_for_output'    => true,
				'default_show'             => array(),
				'section_order'            => 40,
			),
			'po_box'                      => array(
				'label'                 => __( 'P.O. Box', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}po_box",
				'xpath'                 => '//kontaktperson/postfach',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 40,
			),
			'po_box_zip_code'             => array(
				'label'                 => __( 'P.O. Box ZIP Code', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}po_box_zip_code",
				'xpath'                 => '//kontaktperson/postf_plz',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 40,
			),
			'po_box_city'                 => array(
				'label'                 => __( 'P.O. Box City', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}po_box_city",
				'xpath'                 => '//kontaktperson/postf_ort',
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 40,
			),
			'country_iso'                 => array(
				'label'                    => __( 'Country (ISO3)', 'immonex-kickstart-team' ),
				'meta_key'                 => "{$this->prefix}country_iso",
				'xpath'                    => '//kontaktperson/land/@iso_land',
				'agency_fallback_meta_key' => "{$agency_prefix}country_iso",
				'selectable_for_output'    => false,
				'default_show'             => array(),
				'section_order'            => 40,
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
				'default_show'          => array( 'single_agent_page' ),
				'section_order'         => 40,
			),
			'address_single_line'         => array(
				'label'                 => __( 'Address (single line)', 'immonex-kickstart-team' ),
				'icon'                  => $icon_location,
				'compose_cb'            => array( $this, 'get_address_single_line' ),
				'selectable_for_output' => false,
				'default_show'          => array(),
				'section_order'         => 40,
			),
			'personal_number'             => array(
				'label'                 => __( 'Personal Number', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}personal_number",
				'xpath'                 => '//kontaktperson/personnennummer',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'property_trustee_id'         => array(
				'label'                 => __( 'Property Trustee ID', 'immonex-kickstart-team' ),
				'show_label'            => true,
				'meta_key'              => "{$this->prefix}property_trustee_id",
				'xpath'                 => '//kontaktperson/immobilientreuhaenderid',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'reference_id'                => array(
				'label'                 => __( 'Reference ID', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}reference_id",
				'xpath'                 => '//kontaktperson/referenz_id',
				'selectable_for_output' => false,
				'default_show'          => array(),
			),
			'misc'                        => array(
				'label'                 => __( 'Miscellaneous', 'immonex-kickstart-team' ),
				'meta_key'              => "{$this->prefix}misc",
				'xpath'                 => '//kontaktperson/zusatzfeld',
				'selectable_for_output' => false,
				'default_show'          => array(),
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
				'default_show'          => array( 'widget', 'default_contact_element_replacement', 'single_agent_page' ),
				'section_order'         => 50,
			),
			'contact_form'                => array(
				'label'                 => __( 'Contact Form', 'immonex-kickstart-team' ),
				'compose_cb'            => array( $this, 'get_contact_form' ),
				'selectable_for_output' => true,
				'default_show'          => array( 'widget', 'default_contact_element_replacement', 'single_agent_page' ),
				'section_order'         => 60,
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
	 * Determine the value of a single agent element.
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

		if ( ! empty( $element['consists_of'] ) ) {
			$temp = array();

			foreach ( $element['consists_of'] as $sub_key ) {
				$temp[] = (string) $this->get_element_value( $sub_key );
				$value  = trim( implode( ' ', $temp ) );
			}
		} elseif (
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
			! $value
			&& $this->agency_id
		) {
			if ( ! empty( $element['agency_fallback_meta_key'] ) ) {
				$value = get_post_meta( $this->agency_id, $element['agency_fallback_meta_key'], true );
			} elseif ( ! empty( $element['agency_fallback_post_field'] ) ) {
				$value = get_post_field( $element['agency_fallback_post_field'], $this->agency_id );
			}
		}

		if ( ! $value && ! empty( $element['default'] ) ) {
			$value = $element['default'];
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

		return apply_filters( 'inx_team_agent_networks', $networks );
	} // get_networks

	/**
	 * Special getter method for the agent's business/social network URLs.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[] Array containing name/URL pairs.
	 */
	private function get_network_urls( $value_getter ) {
		if ( ! is_a( $this->post, 'WP_Post' ) || ! $this->post->ID ) {
			return array();
		}

		$prefix   = '_' . $this->config['plugin_prefix'] . 'agent_';
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
	 * Special getter method for generating the agent's business/social
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

		return apply_filters( 'inx_team_agent_network_icons_output', $html );
	} // get_network_icons

	/**
	 * Special getter method for the agent's position incl. company name.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[] Array containing raw value and (eventually) a link tag.
	 */
	private function get_position_incl_company( $value_getter ) {
		$position = call_user_func( $value_getter, 'position' );
		if ( ! $position ) {
			return false;
		}

		$agency = get_post( $this->agency_id );

		if (
			! get_query_var( Kickstart::PUBLIC_PREFIX . 'agency' )
			&& $this->get_agency_count() > 1
			&& $this->agency_id
			&& $agency
		) {
			$position_link = false;

			if ( 'external' === $this->link_type ) {
				$url = call_user_func( $value_getter, 'url' );

				if ( $url ) {
					$position_link = $position . ' ' . __( 'at', 'immonex-kickstart-team' ) . ' '
						. wp_sprintf(
							'<a href="%s" target="_blank">%s</a>',
							$url,
							$agency->post_title
						);
				}
			} elseif (
				'internal' === $this->link_type
				&& $this->is_public_agency
			) {
				$position_link = $position . ' ' . __( 'at', 'immonex-kickstart-team' ) . ' '
					. wp_sprintf(
						'<a href="%s">%s</a>',
						get_permalink( $agency->ID ),
						$agency->post_title
					);
			}

			$position_plain = $position . ' ' . __( 'at', 'immonex-kickstart-team' ) . ' '
				. $agency->post_title;

			if ( ! $position_link ) {
				$position_link = $position_plain;
			}
		}

		return array(
			'raw'  => ! empty( $position_plain ) ? $position_plain : $position,
			'link' => ! empty( $position_link ) ? $position_link : $position,
		);
	} // get_position_incl_company

	/**
	 * Special getter method for automatically selecting the agent's
	 * most suitable mail address.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string|bool Mail address or false if indeterminable.
	 */
	private function get_email_auto_select( $value_getter ) {
		$email_type_keys = array(
			'email',
			'email_main_office',
		);

		foreach ( $email_type_keys as $key ) {
			$value = call_user_func( $value_getter, $key );
			if ( $value ) {
				break;
			}
		}

		return $value;
	} // get_email_auto_select

	/**
	 * Special getter method for automatically selecting the agent's
	 * most suitable phone number.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return string|bool Phone number or false if indeterminable.
	 */
	private function get_phone_auto_select( $value_getter ) {
		$phone_type_keys = array(
			'phone',
			'phone_main_office',
			'phone_mobile',
			'phone_other',
		);

		foreach ( $phone_type_keys as $key ) {
			$value = call_user_func( $value_getter, $key );
			if ( $value ) {
				break;
			}
		}

		if ( isset( $value['raw'] ) ) {
			$value = $value['raw'];
		}

		return $value;
	} // get_phone_auto_select

	/**
	 * Special getter method for the agent's company name incl. link.
	 * most suitable phone number.
	 *
	 * @since 1.0.0
	 *
	 * @param callable $value_getter Main value getter method.
	 *
	 * @return mixed[] Array containing raw value and (eventually) a link tag.
	 */
	private function get_company_link( $value_getter ) {
		$company = call_user_func( $value_getter, 'company' );
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
			&& $this->agency_id
			&& $this->is_public_agency
		) {
			$url = get_permalink( $this->agency_id );

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
	} // get_company_link

	/**
	 * Special getter method for the agent's contact form.
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
	 * Query the number of properties linked to the agent.
	 *
	 * @since 1.0.0
	 *
	 * @return int|bool Property count or false if no agent post is assigned yet.
	 */
	private function get_property_count() {
		if ( ! is_a( $this->post, 'WP_Post' ) || ! $this->post->ID ) {
			return 0;
		}

		return apply_filters(
			'inx_get_properties',
			array(),
			array(
				'inx-agent' => $this->post->ID,
				'count'     => true,
			)
		);
	} // get_property_count

	/**
	 * Determine the number of ALL agencies.
	 *
	 * @since 1.0.0
	 *
	 * @return int Agency count.
	 */
	private function get_agency_count() {
		$agency_list = new Agency_List( $this->config, $this->utils );

		return count(
			$agency_list->get_items(
				array(
					'fields'                        => 'ids',
					'suppress_pre_get_posts_filter' => false,
				)
			)
		);
	} // get_agency_count

	/**
	 * Determine the agent's gender by salutation and first name,
	 * utilizing genderize.io.
	 *
	 * @since 1.0.0
	 *
	 * @param string $salutation Salutation (in German).
	 * @param string $first_name First name.
	 *
	 * @return string Gender character (m/f).
	 */
	private function get_gender( $salutation, $first_name ) {
		if ( $salutation ) {
			if ( 'herr' === strtolower( substr( $salutation, 0, 4 ) ) ) {
				return 'm';
			} elseif ( 'frau' === strtolower( substr( $salutation, 0, 4 ) ) ) {
				return 'f';
			}
		}

		if ( $first_name && strlen( $first_name ) > 2 ) {
			$genderize_request_url = wp_sprintf(
				'https://api.genderize.io?name=%s&country_id=DE',
				rawurlencode( $first_name )
			);
			$response              = $this->utils['general']->get_url_contents( $genderize_request_url );

			if ( $response ) {
				$response_json = json_decode( $response, true );

				if ( ! empty( $response_json['gender'] ) ) {
					return $response_json['gender'][0];
				}
			}
		}

		return 'm';
	} // get_gender

	/**
	 * Fetch all agent IDs related to the given/current property post.
	 *
	 * @since 1.0.0
	 *
	 * @param int|bool       $property_post_id Property post ID (optional,
	 *                                         false = use current).
	 * @param int[]|string[] $agent_ids User-defined IDs (optional).
	 *
	 * @return string[]|int[] Agent IDs - primary first.
	 */
	public static function fetch_agent_ids( $property_post_id = false, $agent_ids = array() ) {
		global $immonex_kickstart_team;

		if ( ! $property_post_id ) {
			$property_post_id = apply_filters(
				'inx_current_property_post_id',
				$immonex_kickstart_team->utils['general']->get_the_ID()
			);
		}
		$prefix = '_' . Kickstart_Team::PLUGIN_PREFIX . 'agent';

		$primary_agent_id = get_post_meta( $property_post_id, "{$prefix}_primary", true );
		if ( $primary_agent_id && ! in_array( $primary_agent_id, $agent_ids, true ) ) {
			$agent_ids[] = $primary_agent_id;
		}

		$additional_agent_ids = get_post_meta( $property_post_id, "{$prefix}s", true );
		if ( ! empty( $additional_agent_ids ) ) {
			$agent_ids = array_unique(
				array_merge(
					$agent_ids,
					$additional_agent_ids
				)
			);
		}

		return $agent_ids;
	} // fetch_agent_ids


	/**
	 * Evaluate optional section display in single views.
	 *
	 * @since 1.3.7
	 *
	 * @param int|string $post_id Agency post ID.
	 *
	 * @return string[] Keys of optional sections to display.
	 */
	private function get_single_view_optional_sections( $post_id ) {
		$sections = $this->config['agent_single_view_optional_sections'];

		// Custom field name (meta key excl. prefix) => section key.
		$option_mapping = array(
			'show_property_list' => 'properties',
			'show_agency_link'   => 'agency_link',
		);

		foreach ( $option_mapping as $cf_name => $section ) {
			$meta_key   = '_' . $this->config['plugin_prefix'] . $this->base_name . '_' . $cf_name;
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			if ( 'yes' === $meta_value && ! in_array( $section, $sections, true ) ) {
				$sections[] = $section;
			} elseif ( 'no' === $meta_value && in_array( $section, $sections, true ) ) {
				$sections = array_diff( $sections, array( $section ) );
			}
		}

		return $sections;
	} // get_single_view_optional_sections

} // Agent
