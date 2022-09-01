<?php
/**
 * CMB2-based agency edit form
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Agency CPT edit form (WP backend)
 */
class Agency_Backend_Form {

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
	 * Array of bootstrap data
	 *
	 * @var mixed[]
	 */
	private $data;

	/**
	 * Main plugin object
	 *
	 * @var \immonex\Kickstart\Team\Kickstart_Team
	 */
	private $plugin;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]                                $bootstrap_data Plugin bootstrap data.
	 * @param \immonex\Kickstart\Team\Kickstart_Team $plugin Main plugin object.
	 */
	public function __construct( $bootstrap_data, $plugin ) {
		$this->data   = is_array( $bootstrap_data ) ? $bootstrap_data : array();
		$this->plugin = $plugin;

		// Setup CMB2 meta boxes.
		add_action( 'cmb2_admin_init', array( $this, 'setup_meta_boxes' ) );
	} // __construct

	/**
	 * Set up CMB2 meta boxes used in the backend form.
	 *
	 * @since 1.0.0
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types
	 */
	public function setup_meta_boxes() {
		$prefix = '_' . $this->data['plugin_prefix'] . $this->base_name . '_';

		$contact_data = new_cmb2_box(
			array(
				'id'           => "{$prefix}contact_data",
				'title'        => __( 'Contact Data', 'immonex-kickstart-team' ),
				'object_types' => array( $this->post_type_name ),
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
				'query_args'   => array(
					'type' => array(
						'image/gif',
						'image/jpeg',
						'image/png',
					),
				),
			)
		);

		$contact_data_fields = array(
			array(
				'name' => __( 'Email', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}email",
				'type' => 'text_email',
			),
			array(
				'name' => __( 'Phone', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}phone",
				'type' => 'text',
			),
			array(
				'name' => __( 'Fax', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}fax",
				'type' => 'text',
			),
			array(
				'name' => 'URL',
				'desc' => '',
				'id'   => "{$prefix}url",
				'type' => 'text',
			),
			array(
				'name' => __( 'Street', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}street",
				'type' => 'text',
			),
			array(
				'name' => __( 'House Number', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}house_number",
				'type' => 'text_small',
			),
			array(
				'name' => __( 'ZIP Code', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}zip_code",
				'type' => 'text_small',
			),
			array(
				'name' => __( 'City', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}city",
				'type' => 'text',
			),
			array(
				'name' => __( 'P.O. Box', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}po_box",
				'type' => 'text',
			),
			array(
				'name' => __( 'P.O. Box ZIP Code', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}po_box_zip_code",
				'type' => 'text_small',
			),
			array(
				'name' => __( 'P.O. Box City', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}po_box_city",
				'type' => 'text',
			),
			array(
				'name' => __( 'Country code (ISO3)', 'immonex-kickstart-team' ),
				'desc' => __( 'Code according to <a href="https://en.wikipedia.org/wiki/ISO_3166-1" target="_blank">ISO-3166-1 Alpha-3</a>', 'immonex-kickstart-team' ),
				'id'   => "{$prefix}country_iso",
				'type' => 'text_small',
			),
		);

		foreach ( $contact_data_fields as $field ) {
			$contact_data->add_field( $field );
		}

		$networks = ! empty( $this->plugin->cpt_hooks['Agency_Hooks'] ) ?
			$this->plugin->cpt_hooks['Agency_Hooks']->get_post_instance()->get_networks() :
			false;

		if ( ! empty( $networks ) ) {
			$contact_data->add_field(
				array(
					'name' => __( 'Business/Social Networks', 'immonex-kickstart-team' ),
					'id'   => "{$prefix}business_social_networks",
					'type' => 'title',
				)
			);

			foreach ( $networks as $key => $network ) {
				$contact_data->add_field(
					array(
						'name' => $network . ' URL',
						'desc' => '',
						'id'   => "{$prefix}{$key}_url",
						'type' => 'text',
					)
				);
			}
		}

		$legal = new_cmb2_box(
			array(
				'id'           => "{$prefix}legal",
				'title'        => __( 'Legal Information', 'immonex-kickstart-team' ),
				'object_types' => array( $this->post_type_name ),
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
			)
		);

		$legal_fields = array(
			array(
				'name' => __( 'Company', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}legal_company",
				'type' => 'text',
			),
			array(
				'name' => __( 'Address', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}legal_address",
				'type' => 'text',
			),
			array(
				'name' => __( 'Phone', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}legal_phone",
				'type' => 'text',
			),
			array(
				'name' => __( 'Representative', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}representative",
				'type' => 'text',
			),
			array(
				'name' => __( 'Supervisory Authority', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}supervisory_authority",
				'type' => 'textarea',
			),
			array(
				'name' => __( 'Registry Court', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}registry_court",
				'type' => 'text',
			),
			array(
				'name' => __( 'Trade Register Number', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}trade_register_number",
				'type' => 'text',
			),
			array(
				'name' => __( 'VATIN', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}vatin",
				'type' => 'text',
			),
			array(
				'name' => __( 'Other', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}legal_misc",
				'type' => 'text',
			),
			array(
				'name' => __( 'Legal Notice', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}legal_notice",
				'type' => 'textarea',
			),
		);

		foreach ( $legal_fields as $field ) {
			$legal->add_field( $field );
		}

		$misc = new_cmb2_box(
			array(
				'id'           => "{$prefix}misc",
				'title'        => __( 'Miscellaneous', 'immonex-kickstart-team' ),
				'object_types' => array( $this->post_type_name ),
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
			)
		);

		$misc_fields = array(
			array(
				'name' => __( 'Auto Update', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}auto_update",
				'type' => 'checkbox',
			),
			array(
				'name' => __( 'Address Publishing Approval', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}address_publishing_approved",
				'type' => 'checkbox',
			),
			array(
				'name'    => __( 'Import Folder', 'immonex-kickstart-team' ),
				'desc'    => '',
				'id'      => '_immonex_import_folder',
				'type'    => 'text',
				'default' => 'global',
			),
			array(
				'name' => __( 'OpenImmo ANID', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => '_openimmo_anid',
				'type' => 'text',
			),
			array(
				'name' => __( 'Demo Agency', 'immonex-kickstart-team' ),
				'desc' => __( 'Activate if this agency record only consists of example data. Related contact form data will <strong>not</strong> be submitted in this case.', 'immonex-kickstart-team' ),
				'id'   => '_immonex_is_demo',
				'type' => 'checkbox',
			),
		);

		foreach ( $misc_fields as $field ) {
			$misc->add_field( $field );
		}
	} // setup_meta_boxes

} // Agency_Backend_Form
