<?php
/**
 * Class Contact_Form
 *
 * @package immonex-kickstart-team
 */

namespace immonex\Kickstart\Team;

/**
 * Contact form rendering
 */
class Contact_Form {

	const OBFUSCATION_KEY     = 'yu9$K';
	const HONEYPOT_FIELD_NAME = 'fname';

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
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[]  $config Various component configuration data.
	 * @param object[] $utils Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config = $config;
		$this->utils  = $utils;
	} // __construct

	/**
	 * Render a contact form (PHP template).
	 *
	 * @since 1.0.0
	 *
	 * @param string  $template Template file name (without suffix; optional).
	 * @param mixed[] $atts Rendering attributes.
	 *
	 * @return string Rendered contents (HTML).
	 */
	public function render( $template = '', $atts = array() ) {
		if ( ! $template ) {
			$template = 'contact-form';
		}

		$origin_post_id   = ! empty( $atts['origin_post_id'] ) ? $atts['origin_post_id'] : false;
		$post_id          = $this->utils['general']->get_the_ID();
		$property_post_id = apply_filters(
			'inx_current_property_post_id',
			$post_id
		);
		$is_demo          = $this->is_demo_context( $origin_post_id, $property_post_id );
		$fields           = $this->get_fields();
		$property         = get_post( $property_post_id );

		if ( $property_post_id && $property ) {
			$external_id                        = get_post_meta( $property_post_id, '_inx_property_id', true );
			$fields['message']['default_value'] = wp_sprintf(
				/* translators: %1$s = property title, %2$s = property ID */
				__( 'I am interested in the property &quot;%1$s&quot; (%2$s).', 'immonex-kickstart-team' ),
				$property->post_title,
				$external_id
			);
		}

		$recipients_enc = $this->get_obfuscated_recipients( $atts );

		$template_data = array_merge(
			$this->config,
			$atts,
			array(
				'instance'                  => $this,
				'is_demo'                   => $is_demo,
				'post_type'                 => get_post_type(),
				'origin_post_id'            => $origin_post_id,
				'post_id'                   => $post_id,
				'property_post_id'          => $property_post_id ? $property_post_id : 0,
				'fields'                    => $fields,
				'consent_text'              => $this->get_consent_text(),
				'privacy_consent_text'      => $this->get_consent_text( 'privacy' ),
				'cancellation_consent_text' => $this->get_consent_text( 'cancellation' ),
				'recipients_enc'            => $recipients_enc['recipients'],
				'cc_enc'                    => $recipients_enc['cc'],
				'honeypot_field_name'       => self::HONEYPOT_FIELD_NAME,
			)
		);

		$template_content = $this->utils['template']->render_php_template(
			$template,
			$template_data,
			$this->utils
		);

		return $template_content;
	} // render

	/**
	 * Send the frontend form data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed[] $form_data Frontend form data (user inputs, meta data).
	 *
	 * @return mixed[] Send Result (status, user message).
	 */
	public function send( $form_data ) {
		$result = $this->validate( $form_data );
		if ( ! $result['valid'] ) {
			return $result;
		}

		$origin_post_id   = isset( $form_data['origin_post_id'] ) ?
			(int) $form_data['origin_post_id'] :
			false;
		$property_post_id = isset( $form_data['property_post_id'] ) ?
			(int) $form_data['property_post_id'] :
			false;

		if (
			$this->is_demo_context( $origin_post_id, $property_post_id )
			|| ! empty( $form_data[ self::HONEYPOT_FIELD_NAME ] )
		) {
			// Don't submit data when in demo context or if honeypot field is filled.
			return $result;
		}

		$site_title    = get_bloginfo( 'name' );
		$fields        = $this->get_fields();
		$template_data = array(
			'site_title' => $site_title,
			'form_data'  => array(),
		);

		if ( count( $fields ) > 0 ) {
			foreach ( $fields as $field_name => $field ) {
				if ( isset( $form_data[ $field_name ] ) ) {
					$field['value']                            = $form_data[ $field_name ];
					$template_data['form_data'][ $field_name ] = $field;
				}
			}
		}

		$property_data = $property_post_id ?
			$this->get_property_data( $form_data['property_post_id'] ) :
			false;

		$template_data['property'] = $property_data;

		$recipient_lists = $this->compose_recipient_lists(
			$form_data['post_type'],
			$form_data['post_id'],
			$form_data['recipients_enc'],
			$form_data['cc_enc'],
			$property_data
		);

		$send_result = false;
		$sender      = get_option( 'admin_email' );

		$subject = "[{$site_title}] " . __( 'Inquiry', 'immonex-kickstart-team' );
		if ( $property_data ) {
			$subject .= ' ' . __( 'for', 'immonex-kickstart-team' ) . ' ' . $property_data['title'];
			if ( $property_data['external_id'] ) {
				$subject .= ' (' . $property_data['external_id'] . ')';
			}
		}
		$subject = apply_filters(
			'inx_team_contact_form_notification_subject',
			filter_var( $subject, FILTER_SANITIZE_STRING ),
			'admin',
			$template_data
		);

		$subject = $this->replace_vars( $subject, 'admin', $template_data );

		$headers = array( "From: {$sender}" );

		if ( ! empty( $form_data['email'] ) ) {
			$receipt_conf_recipient = wp_sprintf(
				'%s <%s>',
				$form_data['name'],
				$form_data['email']
			);

			$headers[] = "Reply-To: {$receipt_conf_recipient}";
		}

		if ( count( $recipient_lists['cc'] ) > 0 ) {
			$headers[] = wp_sprintf(
				'CC: %s',
				implode( ', ', $recipient_lists['cc'] )
			);
		}

		$headers = apply_filters(
			'inx_team_contact_form_mail_headers',
			$headers,
			'admin'
		);

		$this->add_prerendered_data( $template_data );

		$body = $this->utils['template']->render_php_template(
			'mail/contact-form-to-admin',
			$template_data,
			$this->utils
		);

		$attachments = array();

		if ( $this->config['oi_feedback_type'] && ! empty( $property_data ) ) {
			$qoi_feedback = new Quick_Openimmo_Feedback( $this->config, $this->utils );
			$qoi_feedback->set_property_post_id( $property_data['post_id'] );
			$qoi_feedback->set_prospect_data( $form_data );
			$oi_feedback_xml_source = $qoi_feedback->get_oi_feedback_xml_source();

			if ( 'body' === $this->config['oi_feedback_type'] ) {
				$body .= PHP_EOL . PHP_EOL . '--- OpenImmo-Feedback ---' . PHP_EOL . PHP_EOL;
				$body .= $oi_feedback_xml_source;
			} else {
				$oi_feedback_file = $qoi_feedback->create_temp_file( $oi_feedback_xml_source );
				if ( $oi_feedback_file ) {
					$attachments[] = $oi_feedback_file;
				}
			}
		}

		if ( $body ) {
			$send_result = wp_mail(
				$recipient_lists['recipients'],
				$subject,
				stripslashes( $body ),
				$headers,
				$attachments
			);
		}

		if ( ! $send_result ) {
			$result['valid']   = false;
			$result['message'] = __( 'Uh-oh! A problem occured while sending your data. Please try again later!', 'immonex-kickstart-team' );
		} elseif (
			$this->config['send_receipt_confirmation']
			&& ! empty( $receipt_conf_recipient )
		) {
			$this->send_receipt_confirmation( $sender, $recipient_lists['receipt_conf_sender_info'], $receipt_conf_recipient, $template_data );
		}

		if ( isset( $qoi_feedback ) && ! empty( $oi_feedback_file ) ) {
			$qoi_feedback->delete_temp_file( $oi_feedback_file );
		}

		return $result;
	} // send

	/**
	 * Get the contact form field elements.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $names_only Indicate if only the element names shall be returned.
	 *
	 * @return mixed[] Full element data or names only.
	 */
	public function get_fields( $names_only = false ) {
		$fields = array(
			'name'    => array(
				'type'         => 'text',
				'required'     => true,
				'caption'      => __( 'Name', 'immonex-kickstart-team' ),
				'caption_mail' => __( 'Prospect', 'immonex-kickstart-team' ),
				'placeholder'  => __( 'Name', 'immonex-kickstart-team' ),
			),
			'phone'   => array(
				'type'        => 'text',
				'required'    => false,
				'required_or' => 'email',
				'caption'     => __( 'Phone', 'immonex-kickstart-team' ),
				'placeholder' => __( 'Phone', 'immonex-kickstart-team' ),
			),
			'email'   => array(
				'type'        => 'email',
				'required'    => false,
				'required_or' => 'phone',
				'caption'     => __( 'Email Address', 'immonex-kickstart-team' ),
				'placeholder' => __( 'Email Address', 'immonex-kickstart-team' ),
			),
			'message' => array(
				'type'        => 'textarea',
				'required'    => true,
				'caption'     => __( 'Message', 'immonex-kickstart-team' ),
				'placeholder' => __( 'Message', 'immonex-kickstart-team' ),
			),
			'consent' => array(
				'type'     => 'checkbox',
				'required' => (int) $this->config['cancellation_page_id'] ? true : false,
			),
		);

		return apply_filters(
			'inx_team_contact_form_fields',
			$names_only ? array_keys( $fields ) : $fields,
			$names_only
		);
	} // get_fields

	/**
	 * Validate the submitted frontend form data.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $form_data Frontend form data (user inputs, meta data).
	 *
	 * @return mixed[] Validation result (status, user messages, errors).
	 */
	public function validate( $form_data ) {
		$fields = $this->get_fields();
		$result = array(
			'valid'        => true,
			'message'      => $this->config['form_confirmation_message'],
			'field_errors' => array(),
		);

		if ( count( $fields ) > 0 ) {
			foreach ( $fields as $field_name => $field ) {
				if (
					! empty( $field['required'] )
					&& empty( $form_data[ $field_name ] )
				) {
					$result['field_errors'][ $field_name ] = __( 'This is a required field!', 'immonex-kickstart-team' );
				} elseif (
					! empty( $field['required_or'] )
					&& empty( $form_data[ $field_name ] )
					&& empty( $form_data[ $field['required_or'] ] )
				) {
					$result['field_errors'][ $field_name ] = wp_sprintf(
						/* translators: %s = caption of a required field */
						__( 'Please provide this information <strong>or: %s</strong>', 'immonex-kickstart-team' ),
						$fields[ $field['required_or'] ]['caption']
					);
				}
			}
		}

		if ( count( $result['field_errors'] ) > 0 ) {
			$result['valid']   = false;
			$result['message'] = __( 'Please check your inputs!', 'immonex-kickstart-team' );
		} elseif (
			empty( $form_data['nonce']['value'] )
			|| ! wp_verify_nonce( $form_data['nonce']['value'], $form_data['nonce']['context'] )
		) {
			$result['valid']   = false;
			$result['message'] = __( 'Your data could not be submitted. Please reload the page and try again!', 'immonex-kickstart-team' );
		}

		return $result;
	} // validate

	/**
	 * Collect and sanitize the user inputs from the POST data.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed[] User form data.
	 */
	public function get_user_form_data() {
		$form_data   = array();
		$field_names = $this->get_fields( true );

		if ( ! in_array( self::HONEYPOT_FIELD_NAME, $field_names ) ) {
			// Add a honeypot field name.
			$field_names[] = self::HONEYPOT_FIELD_NAME;
		}

		if ( count( $field_names ) > 0 ) {
			foreach ( $field_names as $field ) {
				$value = isset( $_POST[ $field ] ) ? $_POST[ $field ] : '';

				if ( self::HONEYPOT_FIELD_NAME !== $field ) {
					if ( false !== strpos( $value, PHP_EOL ) ) {
						$value = sanitize_textarea_field( $value );
					} elseif ( 'email' === $field ) {
						$value = sanitize_email( $value );
					} else {
						$value = sanitize_text_field( $value );
					}
				}

				$form_data[ $field ] = $value;
			}
		}

		return apply_filters( 'inx_team_contact_form_user_data', $form_data );
	} // get_user_form_data

	/**
	 * Send a receipt confirmation mail.
	 *
	 * @since 1.1.0
	 *
	 * @param string   $sender Real Sender mail address.
	 * @param string[] $sender_info Sender type, name/company and reply-to address.
	 * @param string   $recipient Recipient name and mail address.
	 * @param mixed[]  $template_data Property and form data.
	 *
	 * @return bool Send Result.
	 */
	private function send_receipt_confirmation( $sender, $sender_info, $recipient, $template_data ) {
		$template_data['sender_info'] = $sender_info;

		if ( ! empty( $template_data['property'] ) ) {
			$subject = wp_sprintf(
				'[%s] %s %s',
				$template_data['prerendered']['site_title'],
				__( 'Your inquiry for the property', 'immonex-kickstart-team' ),
				$template_data['prerendered']['property_title_ext_id']
			);
		} else {
			$subject = wp_sprintf(
				'[%s] %s',
				$template_data['prerendered']['site_title'],
				__( 'Confirmation of receipt', 'immonex-kickstart-team' )
			);
		}

		$subject = apply_filters(
			'inx_team_contact_form_notification_subject',
			filter_var( $subject, FILTER_SANITIZE_STRING ),
			'prospect',
			$template_data
		);

		$subject = $this->replace_vars( $subject, 'prospect', $template_data );
		$headers = array( "From: {$sender}" );

		if ( ! empty( $sender_info['email'] ) ) {
			$replyto_recipient = trim(
				wp_sprintf(
					'%s <%s>',
					$sender_info['name'],
					$sender_info['email']
				)
			);

			$headers[] = "Reply-To: {$replyto_recipient}";
		}

		$headers = apply_filters(
			'inx_team_contact_form_mail_headers',
			$headers,
			'prospect'
		);

		$body = $this->utils['template']->render_php_template(
			'mail/receipt-confirmation',
			$template_data,
			$this->utils
		);

		if ( $body ) {
			return wp_mail(
				$recipient,
				$subject,
				stripslashes( $body ),
				$headers
			);
		}

		return false;
	} // send_receipt_confirmation

	/**
	 * Assemble the form consent text (cancellation/privacy policies).
	 *
	 * @since 1.0.0
	 *
	 * @param string|bool $type Consent text type (privacy, cancellation or false
	 *                          for a combined version due to compatibility).
	 *
	 * @return string Requested consent text.
	 */
	private function get_consent_text( $type = false ) {
		$privacy_text      = '';
		$cancellation_text = '';

		if ( 'cancellation' !== $type ) {
			$privacy_url = get_privacy_policy_url();

			if ( $privacy_url ) {
				$privacy_link = wp_sprintf(
					'<a href="%s" target="_blank">%s</a>',
					$privacy_url,
					__( 'Privacy Policy', 'immonex-kickstart-team' )
				);
			} else {
				$privacy_link = __( 'Privacy Policy', 'immonex-kickstart-team' );
			}

			$privacy_text = wp_sprintf(
				'<p>%s</p>',
				str_replace(
					'[privacy_policy]',
					$privacy_link,
					$this->config['consent_text_privacy']
				)
			);

			if ( 'privacy' === $type ) {
				return $privacy_text;
			}
		}

		if (
			'privacy' !== $type
			&& (int) $this->config['cancellation_page_id']
		) {
			$lang = isset( $_GET['inx-force-lang'] ) ?
				strtolower( substr( $_GET['inx-force-lang'], 0, 2 ) ) :
				false;

			$cancellation_page_id = apply_filters(
				'inx_element_translation_id',
				(int) $this->config['cancellation_page_id'],
				'page',
				$lang
			);

			$cancellation_link = wp_sprintf(
				'<a href="%s" target="_blank">%s</a>',
				get_permalink( $cancellation_page_id ),
				__( 'Cancellation Policy', 'immonex-kickstart-team' )
			);

			$cancellation_text = wp_sprintf(
				'<p>%s</p>',
				str_replace(
					'[cancellation_policy]',
					$cancellation_link,
					$this->config['consent_text_cancellation']
				)
			);

			if ( 'cancellation' === $type ) {
				return $cancellation_text;
			}
		}

		$combined = $privacy_text;
		if ( ! empty( $cancellation_text ) ) {
			$combined .= PHP_EOL . $cancellation_text;
		}

		return $combined;
	} // get_consent_text

	/**
	 * Generate an obfuscated list of recipients for use in a hidden form field.
	 *
	 * @since 1.0.0
	 *
	 * @param string[] $atts Recipient names/addresses (regular recipients and CC).
	 *
	 * @return string[] Encoded list strings.
	 */
	private function get_obfuscated_recipients( $atts ) {
		$recipients_enc = '';
		if ( ! empty( $atts['recipients'] ) ) {
			$recipients = $this->utils['string']->split_mail_address_string( $atts['recipients'] );
			if ( count( $recipients ) > 0 ) {
				$recipients_enc = base64_encode( $this->utils['string']->xor_string( implode( ',', $recipients ), self::OBFUSCATION_KEY ) );
			}
		}

		$cc_enc = '';
		if ( ! empty( $atts['cc'] ) ) {
			$cc_recipients = $this->utils['string']->split_mail_address_string( $atts['cc'] );
			if ( count( $cc_recipients ) > 0 ) {
				$cc_enc = base64_encode( $this->utils['string']->xor_string( implode( ',', $cc_recipients ), self::OBFUSCATION_KEY ) );
			}
		}

		return array(
			'recipients' => $recipients_enc,
			'cc'         => $cc_enc,
		);
	} // get_obfuscated_recipients

	/**
	 * Retrieve the data of the property with the given ID to be used in the
	 * form mails.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $property_id Property post ID.
	 *
	 * @return string[] Property data.
	 */
	private function get_property_data( $property_id ) {
		if ( ! $property_id ) {
			return false;
		}

		$property      = get_post( $property_id );
		$property_data = array(
			'post_id'               => $property->ID,
			'external_id'           => get_post_meta( $property->ID, '_inx_property_id', true ),
			'obid'                  => get_post_meta( $property->ID, '_openimmo_obid', true ),
			'title'                 => $property->post_title,
			'url'                   => get_permalink( $property->ID ),
			'primary_agent_name'    => '',
			'primary_agent_company' => '',
			'primary_agent_email'   => '',
		);

		/**
		 * Retrieve recipient mail addresses from related agent/agency records.
		 */

		$agent_ids = Agent::fetch_agent_ids( $property_id );
		if ( count( $agent_ids ) > 0 ) {
			foreach ( $agent_ids as $i => $agent_id ) {
				$agent = new Agent( $agent_id, $this->config, $this->utils );

				if ( 0 === $i ) {
					$property_data['primary_agent_name']    = $agent->get_element_value( 'full_name_incl_title' );
					$property_data['primary_agent_company'] = $agent->get_element_value( 'company' );
				}

				// Prioritize a dedicated feedback mail address if existent.
				$email = sanitize_email( $agent->get_element_value( 'email_feedback' ) );

				if ( ! $email ) {
					$email = sanitize_email( $agent->get_element_value( 'email_auto_select' ) );
				}

				if ( $email ) {
					if ( 0 === $i ) {
						$property_data['primary_agent_email'] = $email;
					}

					$mail_recipients[] = $email;
					continue;
				}
			}
		}

		/**
		 * Fallback: Retrieve recipient mail addresses from property details.
		 */

		if ( 0 === count( $mail_recipients ) ) {
			$email_mapping_names = array(
				'kontaktperson.email_direkt',
				'kontaktperson.email_zentrale',
				'kontaktperson.email_sonstige.emailart.em_direkt',
				'kontaktperson.email_sonstige.emailart.em_zentrale',
			);

			foreach ( $email_mapping_names as $mapping_name ) {
				$email = apply_filters(
					'inx_get_custom_field_value_by_name',
					$mapping_name,
					$property->ID
				);
				$email = sanitize_email( $email );

				if ( $email ) {
					$mail_recipients[] = $email;
					break;
				}
			}
		}

		$property_data['mail_recipients'] = array_unique( $mail_recipients );

		return $property_data;
	} // get_property_data

	/**
	 * Compose recipient lists based on the context the contact form is used in.
	 *
	 * @since 1.0.0
	 *
	 * @param post_type  $post_type Type of the post (output) the form is embedded in.
	 * @param int|string $post_id Post ID.
	 * @param string     $form_recipient_string Encoded recipient string submitted
	 *                                          with the form data.
	 * @param string     $form_cc_string Encoded CC recipients string submitted
	 *                                   with the form data.
	 * @param string[]   $property_data Related property data.
	 *
	 * @return string[] Two recipient arrays (regular recipients and CC).
	 */
	private function compose_recipient_lists( $post_type, $post_id, $form_recipient_string, $form_cc_string, $property_data ) {
		$default_addresses  = $this->get_default_recipients();
		$property_addresses = isset( $property_data['mail_recipients'] ) ? $property_data['mail_recipients'] : array();
		$mail_addresses     = array(
			'recipients' => array(),
			'cc'         => $default_addresses['cc'],
		);

		if ( $form_recipient_string || $form_cc_string ) {
			/**
			 * Recipient/CC addresses submitted in the form data
			 * (via render attribute) have priority.
			 */
			$override_addresses = $this->get_override_recipients( $form_recipient_string, $form_cc_string );

			if ( count( $override_addresses['recipients'] ) > 0 ) {
				$mail_addresses['recipients'] = $override_addresses['recipients'];
			}

			if ( count( $override_addresses['cc'] ) > 0 ) {
				$mail_addresses['cc'] = $override_addresses['cc'];
			}
		}

		switch ( $post_type ) {
			case 'inx_agent':
				$agent = new Agent( $post_id, $this->config, $this->utils );
				$email = sanitize_email( $agent->get_element_value( 'email_auto_select' ) );

				$receipt_conf_sender_info = array(
					'type'    => 'agent',
					'name'    => $agent->get_element_value( 'full_name_incl_title' ),
					'company' => $agent->get_element_value( 'company' ),
					'email'   => $email,
				);

				if ( $email ) {
					if ( empty( $mail_addresses['recipients'] ) ) {
						$mail_addresses['recipients'][] = $email;
					}
				}
				break;
			case 'inx_agency':
				$agency = new Agency( $post_id, $this->config, $this->utils );
				$email  = sanitize_email( $agency->get_element_value( 'email' ) );

				$receipt_conf_sender_info = array(
					'type'    => 'agency',
					'name'    => $agency->get_element_value( 'company' )['raw'],
					'company' => $agency->get_element_value( 'company' )['raw'],
					'email'   => $email,
				);

				if ( $email ) {
					if ( empty( $mail_addresses['recipients'] ) ) {
						$mail_addresses['recipients'][] = $email;
					}
				}
				break;
			default:
				$receipt_conf_sender_info = array(
					'type'    => 'agent',
					'name'    => ! empty( $property_data['primary_agent_name'] ) ? $property_data['primary_agent_name'] : '',
					'company' => ! empty( $property_data['primary_agent_company'] ) ? $property_data['primary_agent_company'] : '',
					'email'   => ! empty( $property_data['primary_agent_email'] ) ? $property_data['primary_agent_email'] : '',
				);

				if (
					empty( $mail_addresses['recipients'] )
					&& ! empty( $property_addresses )
				) {
					/**
					 * Use the property related address of the primary agent
					 * if no override address is given.
					 */
					$primary_agent_address        = array_shift( $property_addresses );
					$mail_addresses['recipients'] = array( $primary_agent_address );
				}
		}

		if (
			empty( $override_addresses['cc'] )
			&& count( $property_addresses ) > 0
		) {
			/**
			 * Add the remaining property related addresses (probably
			 * additional assigned agents) as CC recipients,
			 * if no overwrite CCs are given.
			 */
			$mail_addresses['cc'] = array_merge(
				$mail_addresses['cc'],
				$property_addresses
			);
		}

		if ( empty( $mail_addresses['recipients'] ) ) {
			// Use default address(es) as fallback option.
			$mail_addresses['recipients'] = $default_addresses['recipients'];
		}

		if ( count( $mail_addresses['cc'] ) > 0 ) {
			$mail_addresses['cc'] = array_diff( $mail_addresses['cc'], $mail_addresses['recipients'] );
		}

		return array(
			'recipients'               => array_unique( $mail_addresses['recipients'] ),
			'cc'                       => array_unique( $mail_addresses['cc'] ),
			'receipt_conf_sender_info' => $receipt_conf_sender_info,
		);
	} // compose_recipient_lists

	/**
	 * Get recipients that may override other data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $form_recipient_string Encoded recipient string submitted
	 *                                      with the form data.
	 * @param string $form_cc_string Encoded CC recipients string submitted
	 *                               with the form data.
	 *
	 * @return string[] Two recipient arrays (regular recipients and CC).
	 */
	private function get_override_recipients( $form_recipient_string, $form_cc_string ) {
		$override_recipients    = array();
		$override_cc_recipients = array();

		if ( $form_recipient_string ) {
			$form_recipients_string_decode = $this->utils['string']->xor_string(
				base64_decode( $form_recipient_string ),
				self::OBFUSCATION_KEY
			);
			$override_recipients           = $this->utils['string']->split_mail_address_string(
				$form_recipients_string_decode
			);
		}

		if ( $form_cc_string ) {
			$form_cc_string_decode  = $this->utils['string']->xor_string(
				base64_decode( $form_cc_string ),
				self::OBFUSCATION_KEY
			);
			$override_cc_recipients = $this->utils['string']->split_mail_address_string(
				$form_cc_string_decode
			);
		}

		return array(
			'recipients' => $override_recipients,
			'cc'         => $override_cc_recipients,
		);
	} // get_override_recipients

	/**
	 * Get default/fallback recipients.
	 *
	 * @since 1.0.0
	 *
	 * @return string[] Two recipient arrays (regular recipients and CC).
	 */
	private function get_default_recipients() {
		$fallback_recipients = array();
		$cc_recipients       = array();

		if ( ! empty( $this->config['fallback_form_mail_recipients'] ) ) {
			$fallback_recipients = $this->utils['string']->split_mail_address_string(
				$this->config['fallback_form_mail_recipients']
			);
		}

		if ( ! empty( $this->config['form_mail_cc_recipients'] ) ) {
			$cc_recipients = $this->utils['string']->split_mail_address_string(
				$this->config['form_mail_cc_recipients']
			);
		}

		if ( empty( $fallback_recipients ) ) {
			$fallback_recipients[] = apply_filters(
				'inx_team_fallback_recipient_admin_email',
				get_option( 'admin_email' )
			);
		}

		return array(
			'recipients' => $fallback_recipients,
			'cc'         => $cc_recipients,
		);
	} // get_default_recipients

	/**
	 * Replace variables/placeholders (e.g. {property_title}) in the given text
	 * by the corresponding template data values.
	 *
	 * @since 1.1.0
	 *
	 * @param string  $text Source text.
	 * @param string  $context Mail context/recipient (admin or prospect).
	 * @param mixed[] $template_data Template data.
	 *
	 * @return string Modified or original content.
	 */
	private function replace_vars( $text, $context, $template_data ) {
		if ( false === strpos( $text, '{' ) ) {
			return $text;
		}

		$replace = apply_filters(
			'inx_team_contact_form_notification_subject_variables',
			array(
				'site_title'     => $template_data['site_title'],
				'post_id'        => $template_data['property']['post_id'],
				'obid'           => $template_data['property']['obid'],
				'external_id'    => $template_data['property']['external_id'],
				'property_title' => $template_data['property']['title'],
			),
			$context
		);

		if ( is_array( $replace ) && count( $replace ) > 0 ) {
			foreach ( $replace as $key => $value ) {
				$text = str_replace( '{' . $key . '}', $value, $text );
			}
		}

		return trim( $text );
	} // replace_vars

	/**
	 * Determine if the form has been embedded in a demo/example context.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string $origin_post_id Origin post ID.
	 * @param int|string $property_post_id Property post ID.
	 *
	 * @return bool True if in demo context.
	 */
	private function is_demo_context( $origin_post_id, $property_post_id ) {
		if ( $origin_post_id && get_post_meta( $origin_post_id, '_immonex_is_demo', true ) ) {
			return true;
		}

		if ( $property_post_id && $property_post_id !== $origin_post_id ) {
			return get_post_meta( $property_post_id, '_immonex_is_demo', true ) ? true : false;
		}

		return false;
	} // is_demo_context

	/**
	 * Prerender property/form data for output in PHP templates.
	 *
	 * @since 1.1.0
	 *
	 * @param mixed $template_data Template source data.
	 */
	private function add_prerendered_data( &$template_data ) {
		$data = array(
			'site_title'            => $template_data['site_title'],
			'property_title'        => '',
			'external_id'           => '',
			'property_title_ext_id' => '',
			'property_url'          => '',
			'merged_form_data'      => '',
		);

		if ( ! empty( $template_data['property'] ) ) {
			$data['property_title'] = $template_data['property']['title'];
			$data['property_url']   = $template_data['property']['url'];

			if ( ! empty( $template_data['property']['external_id'] ) ) {
				$data['external_id']           = $template_data['property']['external_id'];
				$data['property_title_ext_id'] = wp_sprintf(
					'%s (%s)',
					$data['property_title'],
					$data['external_id']
				);
			} else {
				$data['property_title_ext_id'] = $data['property_title'];
			}
		}

		if ( ! empty( $template_data['form_data'] ) ) {
			$max_caption_length = $this->get_max_field_caption_length( $template_data['form_data'] );
			$fields_inserted    = 0;

			foreach ( $template_data['form_data'] as $field_name => $field ) {
				if (
					'checkbox' === $field['type']
					|| empty( $field['value'] )
				) {
					continue;
				}

				$rendered_field = '';
				$caption        = $this->get_field_caption( $field );

				if ( 'textarea' === $field['type'] ) {
					$divider = PHP_EOL . str_repeat( '-', strlen( $field['caption'] ) + 1 );

					if ( $fields_inserted > 0 ) {
						$data['merged_form_data'] .= PHP_EOL;
					}
					if ( $caption ) {
						$rendered_field = $caption . ':';
					}
					$rendered_field .= wp_sprintf(
						'%2$s%1$s%2$s',
						PHP_EOL . $field['value'],
						$divider
					);
				} else {
					if ( $caption ) {
						$rendered_field = str_pad( $caption . ':', $max_caption_length );
					}
					$rendered_field .= $field['value'];
				}

				$data[ $field_name ]       = $rendered_field;
				$data['merged_form_data'] .= $rendered_field . PHP_EOL;

				$fields_inserted++;
			}

			$data['merged_form_data'] = trim( $data['merged_form_data'] );
		}

		$template_data['prerendered'] = $data;
	} // add_prerendered_data

	/**
	 * Get the most suitable caption for the given field (plain text mail rendering).
	 *
	 * @since 1.1.0
	 *
	 * @param string $field Field key (name).
	 *
	 * @return string Caption.
	 */
	private function get_field_caption( $field ) {
		$caption = '';

		if ( ! empty( $field['caption_mail'] ) ) {
			$caption = $field['caption_mail'];
		} elseif ( ! empty( $field['caption'] ) ) {
			$caption = $field['caption'];
		}

		return $caption;
	} // get_field_caption

	/**
	 * Get the maximum field caption length (plain text mail rendering).
	 *
	 * @since 1.1.0
	 *
	 * @param mixed[] $form_data Form data.
	 *
	 * @return int Max. caption length.
	 */
	private function get_max_field_caption_length( $form_data ) {
		$max_caption_length = 4;

		foreach ( $form_data as $field_name => $field ) {
			$caption = $this->get_field_caption( $field );

			if (
				$caption
				&& ! in_array( $field['type'], array( 'textarea', 'checkbox' ) )
			) {
				$max_caption_length = strlen( $caption ) + 2;
			}
		}

		return $max_caption_length;
	} // get_max_field_caption_length

} // Contact_Form
