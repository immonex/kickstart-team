<?php
/**
 * Default unified contact form template
 *
 * @package immonex\KickstartTeam
 */

if ( defined( 'INX_SKIN_MARK_REQUIRED_FORM_FIELDS' ) ) {
	$inx_skin_mark_req_fields = INX_SKIN_MARK_REQUIRED_FORM_FIELDS;
} else {
	$inx_skin_mark_req_fields = false;
}

$inx_skin_remote_addr          = isset( $_SERVER['REMOTE_ADDR'] ) ?
	sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : false;
$inx_skin_is_localhost         = in_array( $inx_skin_remote_addr, array( '127.0.0.1', '::1' ), true );
$inx_skin_action               = $template_data['plugin_prefix'] . 'submit_contact_form';
$inx_skin_scope                = ! empty( $template_data['scope'] ) ?
	$template_data['scope'] : 'basic';
$inx_skin_send_button_disabled = ! empty( $template_data['cancellation_consent_text'] ) && empty( $template_data['is_preview'] );

$inx_skin_required = array();
foreach ( $template_data['fields'] as $inx_skin_field_name => $inx_skin_field ) {
	$inx_skin_required[ $inx_skin_field_name ] = ! empty( $inx_skin_field['required'] ) ? ' required' : '';
}
?>
<div class="inx-team-contact-form-wrap">
	<form class="inx-team-contact-form uk-form-stacked uk-inline" method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
		<input type="hidden" name="action" value="<?php echo $inx_skin_action; ?>">
		<input type="hidden" name="scope" value="<?php echo $inx_skin_scope; ?>">
		<input type="hidden" name="post_type" value="<?php echo $template_data['post_type']; ?>">
		<input type="hidden" name="origin_post_id" value="<?php echo $template_data['origin_post_id']; ?>">
		<input type="hidden" name="post_id" value="<?php echo $template_data['post_id']; ?>">
		<input type="hidden" name="property_post_id" value="<?php echo $template_data['property_post_id']; ?>">
		<input type="hidden" name="recipients_enc" value="<?php echo $template_data['recipients_enc']; ?>">
		<input type="hidden" name="cc_enc" value="<?php echo $template_data['cc_enc']; ?>">
		<input type="hidden" name="<?php echo $template_data['ts_check_field_name']; ?>" value="<?php echo $template_data['obfuscated_timestamp']; ?>">
		<?php wp_nonce_field( $inx_skin_action, "{$inx_skin_action}_nonce" ); ?>

		<?php // Honeypot. ?>
		<div class="inx-team-contact-form__input inx-team-contact-form__input--name--<?php echo $template_data['honeypot_field_name']; ?>" aria-hidden="true">
			<input type="text" name="<?php echo $template_data['honeypot_field_name']; ?>" placeholder="First Name" tabindex="-1" autocomplete="off" aria-label="First Name" class="uk-input">
			<div class="inx-team-contact-form__input-error"></div>
		</div>
		<?php // /Honeypot. ?>

		<?php
		foreach ( $template_data['fields'] as $inx_skin_field_name => $inx_skin_field ) :
			if ( 'consent' === $inx_skin_field_name ) {
				continue;
			}

			if ( ! empty( $inx_skin_field['type'] ) ) {
				$inx_skin_field_type = $inx_skin_field['type'];
			} elseif ( 'email' === $inx_skin_field_name ) {
				$inx_skin_field_type = 'email';
			} elseif ( 'message' === $inx_skin_field_name ) {
				$inx_skin_field_type = 'textarea';
			} else {
				$inx_skin_field_type = 'text';
			}

			if ( 'checkbox' === $inx_skin_field_type && empty( $inx_skin_field['caption'] ) ) {
				continue;
			}

			$inx_skin_field_required = ! empty( $inx_skin_field['required'] )
				|| ! empty( $inx_skin_field['required_or'] );
			$inx_skin_placeholder    = ! empty( $inx_skin_field['placeholder'] ) ?
				$inx_skin_field['placeholder'] : '';
			if ( $inx_skin_mark_req_fields && $inx_skin_placeholder && $inx_skin_field_required ) {
				$inx_skin_placeholder .= '*';
			}
			$inx_skin_default_value = isset( $inx_skin_field['default_value'] ) ?
				$inx_skin_field['default_value'] : '';
			$inx_skin_add_classes   = isset( $inx_skin_field['layout_type'] ) ?
				' inx-team-contact-form__input--type--' . $inx_skin_field['layout_type'] : '';
			?>

		<div class="inx-team-contact-form__input<?php echo $inx_skin_add_classes; ?> inx-team-contact-form__input--name--<?php echo $inx_skin_field_name; ?>">

			<?php if ( in_array( $inx_skin_field_type, array( 'text', 'email' ), true ) ) : ?>

			<input
				type="<?php echo $inx_skin_field_type; ?>"
				name="<?php echo $inx_skin_field_name; ?>"
				placeholder="<?php echo $inx_skin_placeholder; ?>"
				aria-label="<?php echo $inx_skin_placeholder; ?>"
				class="uk-input"<?php echo $inx_skin_required[ $inx_skin_field_name ]; ?>
			>

			<?php elseif ( 'textarea' === $inx_skin_field_type ) : ?>

			<textarea
				name="<?php echo $inx_skin_field_name; ?>"
				rows="4"
				placeholder="<?php echo $inx_skin_placeholder; ?>"
				aria-label="<?php echo $inx_skin_placeholder; ?>"
				class="uk-textarea"<?php echo $inx_skin_required[ $inx_skin_field_name ]; ?>
			><?php echo $inx_skin_default_value; ?></textarea>

			<?php elseif ( 'checkbox' === $inx_skin_field_type ) : ?>

			<label>
				<input
					type="checkbox"
					name="<?php echo $inx_skin_field_name; ?>"
					value="<?php echo ! empty( $inx_skin_field['value'] ) ? $inx_skin_field['value'] : 'X'; ?>"
					class="uk-checkbox"<?php echo $inx_skin_required[ $inx_skin_field_name ]; ?>
				>
				<?php echo $inx_skin_field['caption'] . ( $inx_skin_mark_req_fields && $inx_skin_field_required ? '*' : '' ); ?>
			</label>

				<?php
			elseif ( 'radio' === $inx_skin_field_type && ! empty( $inx_skin_field['options'] ) ) :
				foreach ( $inx_skin_field['options'] as $inx_skin_option => $inx_skin_option_label ) :
					?>

					<label>
						<input
							type="radio"
							name="<?php echo $inx_skin_field_name; ?>"
							value="<?php echo $inx_skin_option; ?>"
							class="uk-radio"<?php echo $inx_skin_required[ $inx_skin_field_name ]; ?>
							<?php echo $inx_skin_option === $inx_skin_default_value ? 'checked' : ''; ?>
						>
						<?php echo $inx_skin_option_label; ?>
					</label>&nbsp;

					<?php
				endforeach;
				if ( $inx_skin_mark_req_fields && $inx_skin_field_required ) :
					echo '*';
				endif;
			elseif ( 'select' === $inx_skin_field_type && ! empty( $inx_skin_field['options'] ) ) :
				if ( $inx_skin_mark_req_fields && $inx_skin_field_required ) :
					$inx_skin_field['options'][ array_keys( $inx_skin_field['options'] )[0] ] .= '*';
				endif;
				?>

				<select name="<?php echo $inx_skin_field_name; ?>" class="uk-select"<?php echo $inx_skin_required[ $inx_skin_field_name ]; ?>>
					<?php foreach ( $inx_skin_field['options'] as $inx_skin_option => $inx_skin_option_label ) : ?>
					<option value="<?php echo $inx_skin_option; ?>"><?php echo $inx_skin_option_label; ?></option>
					<?php endforeach; ?>
				</select>

				<?php
			endif;
			?>

			<div class="inx-team-contact-form__input-error"></div>
		</div><!-- .inx-team-contact-form__input -->

			<?php
		endforeach;
		?>

		<?php // Honeypot. ?>
		<div class="inx-team-contact-form__input inx-team-contact-form__input--name--<?php echo $template_data['honeypot_field_name2']; ?>" aria-hidden="true">
			<input name="<?php echo $template_data['honeypot_field_name2']; ?>" placeholder="Alternative E-Mail Address" tabindex="-1" autocomplete="off" aria-label="Alternative E-Mail Address" class="uk-input">
			<div class="inx-team-contact-form__input-error"></div>
		</div>
		<?php // /Honeypot. ?>

		<?php if ( $template_data['cancellation_consent_text'] ) : ?>
		<div class="inx-team-contact-form__input inx-team-contact-form__input--type--full inx-team-contact-form__input--name--consent">
			<div class="inx-team-contact-form__consent-checkbox">
				<label class="inx-switch">
					<input type="checkbox" name="consent" aria-label="<?php esc_attr_e( 'consent', 'immonex-kickstart-team' ); ?>"<?php echo $inx_skin_required['consent']; ?>>
					<div class="inx-switch-slider"></div>
					<span hidden><?php esc_attr_e( 'consent', 'immonex-kickstart-team' ); ?></span>
				</label>
			</div>

			<div class="inx-team-contact-form__consent-text">
				<?php echo $template_data['cancellation_consent_text']; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( $template_data['privacy_consent_text'] ) : ?>
		<div class="inx-team-contact-form__input inx-team-contact-form__input--type--privacy-consent">
			<div class="inx-team-contact-form__consent-text">
				<?php echo $template_data['privacy_consent_text']; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php // phpcs:disable ?>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--type--full inx-team-contact-form__result-wrap">
			<div class="inx-team-contact-form__result"></div>

			<?php if ( $template_data['is_demo'] ) : ?>
			<div class="inx-team-contact-form__demo-notice">
				<span uk-icon="warning"></span>
				<span><?php _e( 'Heads up! This is only sample data, the form data will <strong>not</strong> be submitted.', 'immonex-kickstart-team' ); ?></span>
			</div>
			<?php endif; ?>
		</div>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--type--full inx-team-contact-form__input--name--submit">
			<div>
				<?php if ( $inx_skin_is_localhost || is_ssl() ) : ?>
				<span uk-icon="lock" title="<?php _e( 'Secure submission', 'immonex-kickstart-team' ); ?>"></span> <?php _e( 'Secure!', 'immonex-kickstart-team' ); ?>
				<?php endif; ?>
				&nbsp;
			</div>
			<div>
				<button	class="inx-team-contact-form__submit inx-button inx-button--action uk-button uk-button-primary"<?php echo $inx_skin_send_button_disabled ? ' disabled' : ''; ?>>
					<?php _e( 'Send', 'immonex-kickstart-team' ); ?>
				</button>
			</div>
		</div>

		<?php // phpcs:enable ?>

		<div class="inx-team-contact-form__spinner uk-overlay-default uk-position-cover">
			<div class="uk-position-center" uk-spinner="ratio: 2"></div>
		</div>
	</form>
</div>
