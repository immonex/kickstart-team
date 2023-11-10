<?php
/**
 * CMB2-based agent edit form
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Agent CPT edit form (WP backend)
 */
class Agent_Backend_Form {

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

		$about = new_cmb2_box(
			array(
				'id'           => "{$prefix}about",
				'title'        => __( 'About...', 'immonex-kickstart-team' ),
				'object_types' => array( $this->post_type_name ),
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
				'closed'       => true,
			)
		);

		$about_fields = array(
			array(
				'name'             => __( 'Gender', 'immonex-kickstart-team' ),
				'desc'             => '',
				'id'               => "{$prefix}gender",
				'type'             => 'radio',
				'show_option_none' => __( 'not specified', 'immonex-kickstart-team' ),
				'options'          => array(
					'm' => __( 'male', 'immonex-kickstart-team' ),
					'f' => __( 'female', 'immonex-kickstart-team' ),
				),
			),
			array(
				'name' => __( 'Title', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}title",
				'type' => 'text',
			),
			array(
				'name' => __( 'First Name', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}first_name",
				'type' => 'text',
			),
			array(
				'name' => __( 'Last Name', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}last_name",
				'type' => 'text',
			),
			array(
				'name' => __( 'Position', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}position",
				'type' => 'text',
			),
		);

		foreach ( $about_fields as $field ) {
			$about->add_field( $field );
		}

		$contact_data = new_cmb2_box(
			array(
				'id'           => "{$prefix}contact_data",
				'title'        => __( 'Contact Data', 'immonex-kickstart-team' ),
				'object_types' => array( $this->post_type_name ),
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
				'closed'       => true,
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
				'name' => __( 'Email (main office)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}email_main_office",
				'type' => 'text_email',
			),
			array(
				'name' => __( 'Email (feedback)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}email_feedback",
				'type' => 'text_email',
			),
			array(
				'name' => __( 'Email (private)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}email_private",
				'type' => 'text_email',
			),
			array(
				'name' => __( 'Phone (call-through)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}phone",
				'type' => 'text',
			),
			array(
				'name' => __( 'Phone (mobile)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}phone_mobile",
				'type' => 'text',
			),
			array(
				'name' => __( 'Phone (main office)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}phone_main_office",
				'type' => 'text',
			),
			array(
				'name' => __( 'Phone (private)', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}phone_private",
				'type' => 'text',
			),
			array(
				'name' => __( 'Fax', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}fax",
				'type' => 'text',
			),
			array(
				'name' => __( 'Company', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}company",
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
			array(
				'name' => __( 'Business/Social Networks', 'immonex-kickstart-team' ),
				'id'   => "{$prefix}business_social_networks",
				'type' => 'title',
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

		$misc = new_cmb2_box(
			array(
				'id'           => "{$prefix}misc",
				'title'        => __( 'Miscellaneous', 'immonex-kickstart-team' ),
				'object_types' => array( $this->post_type_name ),
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
				'closed'       => true,
			)
		);

		$misc_fields = array(
			array(
				'name'             => __( 'Agency', 'immonex-kickstart-team' ),
				'desc'             => '',
				'id'               => '_inx_team_agency_id',
				'type'             => 'select',
				'show_option_none' => __( 'n/a', 'immonex-kickstart-team' ),
				'options_cb'       => array( $this, 'get_agency_options' ),
			),
			array(
				'name' => __( 'Auto Update', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}auto_update",
				'type' => 'checkbox',
			),
			array(
				'name' => __( 'Address Publishing', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}address_publishing_approved",
				'type' => 'checkbox',
			),
			array(
				'name'             => __( 'Show Property List', 'immonex-kickstart-team' ),
				'desc'             => '',
				'id'               => "{$prefix}show_property_list",
				'type'             => 'select',
				'show_option_none' => __( 'use default', 'immonex-kickstart-team' ),
				'options'          => array(
					'yes' => __( 'Yes', 'immonex-kickstart-team' ),
					'no'  => __( 'No', 'immonex-kickstart-team' ),
				),
				'description'      => wp_sprintf(
					/* translators: %1$s: plugin options URL */
					__( 'The default can be set in the <a href="%1$s">plugin options</a> (Optional Sections).', 'immonex-kickstart-team' ),
					admin_url( 'admin.php?page=immonex-kickstart_settings&tab=addon_team&section_tab=3' )
				),
			),
			array(
				'name'             => __( 'Show Agency Link', 'immonex-kickstart-team' ),
				'desc'             => '',
				'id'               => "{$prefix}show_agency_link",
				'type'             => 'select',
				'show_option_none' => __( 'use default', 'immonex-kickstart-team' ),
				'options'          => array(
					'yes' => __( 'Yes', 'immonex-kickstart-team' ),
					'no'  => __( 'No', 'immonex-kickstart-team' ),
				),
				'description'      => wp_sprintf(
					/* translators: %1$s: plugin options URL */
					__( 'The default can be set in the <a href="%1$s">plugin options</a> (Optional Sections).', 'immonex-kickstart-team' ),
					admin_url( 'admin.php?page=immonex-kickstart_settings&tab=addon_team&section_tab=3' )
				),
			),
			array(
				'name' => __( 'Import folder', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => '_immonex_import_folder',
				'type' => 'text',
			),
			array(
				'name' => __( 'Personal Number', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}personal_number",
				'type' => 'text',
			),
			array(
				'name' => __( 'Property Trustee ID', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}property_trustee_id",
				'type' => 'text',
			),
			array(
				'name' => __( 'Reference ID', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}reference_id",
				'type' => 'text',
			),
			array(
				'name' => __( 'Other', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}misc",
				'type' => 'text',
			),
			array(
				'name' => __( 'Demo Agent', 'immonex-kickstart-team' ),
				'desc' => __( 'Activate if this agent record only consists of example data. Related contact form data will <strong>not</strong> be submitted in this case.', 'immonex-kickstart-team' ),
				'id'   => '_immonex_is_demo',
				'type' => 'checkbox',
			),
		);

		foreach ( $misc_fields as $field ) {
			$misc->add_field( $field );
		}

		$property_contact = new_cmb2_box(
			array(
				'id'           => "{$prefix}property_contact",
				'title'        => __( 'Contact', 'immonex-kickstart-team' ),
				'object_types' => array( 'inx_property' ),
				'context'      => 'normal',
				'priority'     => 'default',
				'show_names'   => true,
				'closed'       => true,
			)
		);

		$property_contact_fields = array(
			array(
				'name'             => __( 'Agent (primary)', 'immonex-kickstart-team' ),
				'desc'             => '',
				'id'               => "{$prefix}primary",
				'type'             => 'select',
				'show_option_none' => __( 'n/a', 'immonex-kickstart-team' ),
				'options_cb'       => array( $this, 'get_agent_options' ),
			),
			array(
				'name'       => __( 'Agents', 'immonex-kickstart-team' ),
				'desc'       => '',
				'id'         => rtrim( $prefix, '_' ) . 's',
				'type'       => 'multicheck',
				'options_cb' => array( $this, 'get_agent_options' ),
			),
		);

		foreach ( $property_contact_fields as $field ) {
			$property_contact->add_field( $field );
		}
	} // setup_meta_boxes

	/**
	 * Generate an option list for agent selection (callback).
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[] ID -> Name list.
	 */
	public function get_agent_options() {
		$args = array(
			'post_type'   => $this->post_type_name,
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => 'ASC',
		);

		$agents = get_posts( $args );
		if ( empty( $agents ) ) {
			return array();
		}

		$agent_list = array();

		foreach ( $agents as $agent ) {
			$agent_list[ $agent->ID ] = $agent->post_title;
		}

		return $agent_list;
	} // get_agent_options

	/**
	 * Generate an option list for agency selection (callback).
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[] ID -> Name list.
	 */
	public function get_agency_options() {
		if ( empty( $this->data['custom_post_types'] ) ) {
			return array();
		}

		$agency_post_type_name = isset( $this->data['custom_post_types']['agency'] ) ?
			$this->data['custom_post_types']['agency']['post_type_name'] :
			'';

		if ( ! $agency_post_type_name ) {
			return array();
		}

		$args = array(
			'post_type'   => $agency_post_type_name,
			'numberposts' => -1,
			'orderby'     => 'title',
			'order'       => 'ASC',
		);

		$agencies = get_posts( $args );
		if ( empty( $agencies ) ) {
			return array();
		}

		$agency_list = array();

		foreach ( $agencies as $agency ) {
			$agency_list[ $agency->ID ] = $agency->post_title;
		}

		return $agency_list;
	} // get_agency_options

} // Agent_Backend_Form
