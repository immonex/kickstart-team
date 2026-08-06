<?php
/**
 * Class Inquiry
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

use immonex\Kickstart\Kickstart;

/**
 * Inquiry CPT (DB inquiry storage)
 */
class Inquiry {

	const BASE_NAME      = 'inquiry';
	const POST_TYPE_NAME = 'inx_inquiry';

	/**
	 * ID of related inquiry post
	 *
	 * @var int|string|bool
	 */
	private $inquiry_id = false;

	/**
	 * Various component configuration data
	 *
	 * @var mixed[]
	 */
	private $config;

	/**
	 * Related form data
	 *
	 * @var mixed[]
	 */
	private $form_data = [];

	/**
	 * Constructor
	 *
	 * @since 1.9.0
	 *
	 * @param mixed[] $config    Various component configuration data.
	 * @param mixed[] $form_data Form data to save (optional).
	 */
	public function __construct( $config, $form_data = [] ) {
		$this->config = $config;

		if ( ! empty( $form_data ) ) {
			$this->set_form_data( $form_data );
		}
	} // __construct

	/**
	 * Extract form data to be saved with the inquiry and add related meta data.
	 *
	 * @since 1.9.0
	 *
	 * @param mixed[] $form_data Form data to save.
	 */
	public function set_form_data( $form_data ) {
		$field_mapping = [
			'salutation'       => 'salutation',
			'first_name'       => 'first_name',
			'last_name'        => 'last_name',
			'name'             => 'full_name',
			'street'           => 'street',
			'postal_code'      => 'postal_code',
			'city'             => 'city',
			'phone'            => 'phone',
			'email'            => 'email',
			'property_post_id' => 'property_post_id',
			'message'          => 'message',
		];

		foreach ( $field_mapping as $form_key => $store_key ) {
			$this->form_data[ $store_key ] = ! empty( $form_data[ $form_key ] ) ? $form_data[ $form_key ] : '';
		}

		if ( ! $this->form_data['first_name'] && ! $this->form_data['last_name'] ) {
			$name_split                    = explode( ' ', $this->form_data['full_name'] );
			$this->form_data['first_name'] = $name_split[0];
			$this->form_data['last_name']  = count( $name_split ) > 1 ? implode( ' ', array_slice( $name_split, 1 ) ) : '';
		}

		if ( ! empty( $this->form_data['property_post_id'] ) ) {
			$property_id_ext = get_post_meta( $this->form_data['property_post_id'], '_inx_property_id_ext', true );
			if ( ! $property_id_ext ) {
				$property_id_ext = get_post_meta( $this->form_data['property_post_id'], '_inx_property_id', true );
			}

			$this->form_data['property_id_ext'] = $property_id_ext;
			$this->form_data['property_id_int'] = get_post_meta( $this->form_data['property_post_id'], '_inx_property_id_int', true );
			$this->form_data['property_title']  = get_the_title( $this->form_data['property_post_id'] );
		}
	} // set_form_data

	/**
	 * Save the inquiry as a post with meta data.
	 *
	 * @since 1.9.0
	 *
	 * @param bool $mail_send_result Result of the email sending process.
	 *
	 * @return int|bool ID of the created inquiry post or false on failure.
	 */
	public function save( $mail_send_result ) {
		if ( empty( $this->form_data ) ) {
			return false;
		}

		$title  = $mail_send_result ? '📨 ' : '⚠️ ';
		$title .= ! empty( $this->form_data['full_name'] ) ? $this->form_data['full_name'] . ', ' : '';
		$title .= __( 'Inquiry', 'immonex-kickstart-team' );

		if ( ! empty( $this->form_data['property_id_ext'] ) ) {
			$title .= ' ' . _x( 'for property', '…inquiry for property 123…', 'immonex-kickstart-team' ) . ' ' . $this->form_data['property_id_ext'];
		}

		$post_data = [
			'post_type'    => self::POST_TYPE_NAME,
			'post_status'  => 'publish',
			'post_title'   => trim( $title, ', ' ),
			'post_content' => $this->form_data['message'],
		];

		$this->inquiry_id = wp_insert_post( $post_data );

		if ( $this->inquiry_id ) {
			$prefix = '_' . $this->config['plugin_prefix'] . self::BASE_NAME . '_';

			foreach ( $this->form_data as $key => $value ) {
				if ( 'message' === $key ) {
					continue;
				}

				add_post_meta( $this->inquiry_id, "{$prefix}{$key}", $value );
			}
		}

		return $this->inquiry_id ? $this->inquiry_id : false;
	} // save

} // Inquiry
