<?php
/**
 * CMB2-based inquiry edit form
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Inquiry CPT edit form (WP backend)
 */
class Inquiry_Backend_Form {

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
	 * @since 1.9.0
	 *
	 * @param mixed[]                                $bootstrap_data Plugin bootstrap data.
	 * @param \immonex\Kickstart\Team\Kickstart_Team $plugin Main plugin object.
	 */
	public function __construct( $bootstrap_data, $plugin ) {
		$this->data   = is_array( $bootstrap_data ) ? $bootstrap_data : [];
		$this->plugin = $plugin;

		// Setup CMB2 meta boxes.
		add_action( 'cmb2_admin_init', [ $this, 'setup_meta_boxes' ] );
	} // __construct

	/**
	 * Set up CMB2 meta boxes used in the backend form.
	 *
	 * @since 1.9.0
	 *
	 * @link https://github.com/CMB2/CMB2/wiki/Field-Types
	 */
	public function setup_meta_boxes() {
		$prefix = '_' . $this->data['plugin_prefix'] . Inquiry::BASE_NAME . '_';

		$contact_data = new_cmb2_box(
			[
				'id'           => "{$prefix}contact_data",
				'title'        => __( 'Contact Data', 'immonex-kickstart-team' ),
				'object_types' => [ INQUIRY::POST_TYPE_NAME ],
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
				'closed'       => true,
			]
		);

		$contact_data_fields = [
			[
				'name'             => __( 'Salutation', 'immonex-kickstart-team' ),
				'desc'             => '',
				'id'               => "{$prefix}salutation",
				'type'             => 'select',
				'show_option_none' => __( 'not specified', 'immonex-kickstart-team' ),
				'options'          => [
					__( 'Ms.', 'immonex-kickstart-team' ) => __( 'Ms.', 'immonex-kickstart-team' ),
					__( 'Mr.', 'immonex-kickstart-team' ) => __( 'Mr.', 'immonex-kickstart-team' ),
				],
			],
			[
				'name' => __( 'First Name', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}first_name",
				'type' => 'text',
			],
			[
				'name' => __( 'Last Name', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}last_name",
				'type' => 'text',
			],
			[
				'name' => _x( 'Full Name', 'combined first and last name', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}full_name",
				'type' => 'text',
			],
			[
				'name' => __( 'Street', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}street",
				'type' => 'text',
			],
			[
				'name' => __( 'Postal Code', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}postal_code",
				'type' => 'text_small',
			],
			[
				'name' => __( 'City', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}city",
				'type' => 'text',
			],
			[
				'name' => __( 'Phone', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}phone",
				'type' => 'text',
			],
			[
				'name' => __( 'Email', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}email",
				'type' => 'text_email',
			],
		];

		foreach ( $contact_data_fields as $field ) {
			$contact_data->add_field( $field );
		}

		$property_data = new_cmb2_box(
			[
				'id'           => "{$prefix}property_data",
				'title'        => _x( 'Property', 'real estate object', 'immonex-kickstart-team' ),
				'object_types' => [ INQUIRY::POST_TYPE_NAME ],
				'context'      => 'normal',
				'priority'     => 'core',
				'show_names'   => true,
				'closed'       => true,
			]
		);

		$property_data_fields = [
			[
				'name' => _x( 'Title', 'real estate property title', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}property_title",
				'type' => 'text',
			],
			[
				'name' => __( 'Object Number', 'immonex-kickstart-team' ) .
					' (' . __( 'external', 'immonex-kickstart-team' ) . ')',
				'desc' => '',
				'id'   => "{$prefix}property_id_ext",
				'type' => 'text',
			],
			[
				'name' => __( 'Object Number', 'immonex-kickstart-team' ) .
					' (' . __( 'internal', 'immonex-kickstart-team' ) . ')',
				'desc' => '',
				'id'   => "{$prefix}property_id_int",
				'type' => 'text',
			],
			[
				'name' => __( 'Post ID', 'immonex-kickstart-team' ),
				'desc' => '',
				'id'   => "{$prefix}property_post_id",
				'type' => 'text_small',
			],
		];

		foreach ( $property_data_fields as $field ) {
			$property_data->add_field( $field );
		}
	} // setup_meta_boxes

} // Inquiry_Backend_Form
