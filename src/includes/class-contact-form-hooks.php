<?php
/**
 * Class Contact_Form_Hooks
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

use \immonex\Kickstart\Kickstart;

/**
 * Contact form related actions and filters
 */
class Contact_Form_Hooks {

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
	 * Current related form object
	 *
	 * @var \immonex\Kickstart\Team\Contact_Form
	 */
	protected $current_form;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config  = $config;
		$this->utils   = $utils;
		$plugin_slug   = $config['plugin_slug'];
		$plugin_prefix = $config['plugin_prefix'];

		/**
		 * Plugin-specific actions and filters
		 */

		add_action( "{$plugin_prefix}render_contact_form", array( $this, 'render_form' ), 10, 3 );

		add_action( "wp_ajax_{$plugin_prefix}submit_contact_form", array( $this, 'process_submission' ) );
		add_action( "wp_ajax_nopriv_{$plugin_prefix}submit_contact_form", array( $this, 'process_submission' ) );
	} // __construct

	/**
	 * Render and return or output the contents of a form template.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $template Template file (without suffix).
	 * @param mixed[] $atts Rendering Attributes (optional).
	 * @param bool    $output Flag for directly output the rendered contents (true by default).
	 *
	 * @return string Rendered template contents.
	 */
	public function render_form( $template = '', $atts = array(), $output = true ) {
		if ( ! $template ) {
			$template = 'contact-form';
		}

		$form     = $this->get_form_instance();
		$contents = $form->render( $template, $atts );

		if ( $output ) {
			echo $contents;
		}

		return $contents;
	} // render_form

	/**
	 * Process the submitted frontend form data and send a JSON response
	 * (action callback).
	 *
	 * @since 1.0.0
	 */
	public function process_submission() {
		$form          = $this->get_form_instance();
		$nonce_context = $this->config['plugin_prefix'] . 'submit_contact_form';
		// @codingStandardsIgnoreStart
		$form_data     = array_merge(
			array(
				'nonce'            => array(
					'context' => $nonce_context,
					'value'   => ! empty( $_POST[ "{$nonce_context}_nonce" ] ) ?
						sanitize_text_field( wp_unslash( $_POST[ "{$nonce_context}_nonce" ] ) ) :
						false,
				),
				'post_type'        => isset( $_POST['post_type'] ) ? sanitize_key( $_POST['post_type'] ) : '',
				'origin_post_id'   => isset( $_POST['origin_post_id'] ) ? (int) sanitize_key( $_POST['origin_post_id'] ) : 0,
				'post_id'          => isset( $_POST['post_id'] ) ? (int) sanitize_key( $_POST['post_id'] ) : 0,
				'property_post_id' => isset( $_POST['property_post_id'] ) ? (int) sanitize_key( $_POST['property_post_id'] ) : 0,
				'recipients_enc'   => isset( $_POST['recipients_enc'] ) ? sanitize_text_field( $_POST['recipients_enc'] ) : '',
				'cc_enc'           => isset( $_POST['cc_enc'] ) ? sanitize_text_field( $_POST['cc_enc'] ) : '',
			),
			$form->get_user_form_data()
		);
		// @codingStandardsIgnoreEnd

		$result = $form->send( $form_data );

		wp_send_json( $result, $result['valid'] ? 200 : 400 );
	} // process_submission

	/**
	 * Return the current form object instance, create if not existing yet.
	 *
	 * @since 1.0.0
	 *
	 * @return \immonex\Kickstart\Team\Contact_Form Current form object.
	 */
	public function get_form_instance() {
		if ( ! $this->current_form ) {
			$this->current_form = new Contact_Form( $this->config, $this->utils );
		}

		return $this->current_form;
	} // get_form_instance

} // Contact_Form_Hooks
