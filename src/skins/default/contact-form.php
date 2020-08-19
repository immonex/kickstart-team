<?php
/**
 * Default unified contact form template
 *
 * @package immonex-kickstart-team
 */

$inx_skin_is_localhost          = in_array( $_SERVER['REMOTE_ADDR'], array( '127.0.0.1', '::1' ) );
$inx_skin_action                = $template_data['plugin_prefix'] . 'submit_contact_form';
$inx_skin_message_default_value = isset( $template_data['fields']['message']['default_value'] ) ?
	$template_data['fields']['message']['default_value'] :
	'';

$inx_skin_required = array();
foreach ( $template_data['fields'] as $inx_skin_field_name => $inx_skin_field ) {
	$inx_skin_required[ $inx_skin_field_name ] = ! empty( $inx_skin_field['required'] ) ? ' required' : '';
}
?>
<div class="inx-team-contact-form-wrap">
	<form class="inx-team-contact-form uk-form-stacked uk-inline" method="post" action="<?php echo admin_url( 'admin-ajax.php' ); ?>">
		<input type="hidden" name="action" value="<?php echo $inx_skin_action; ?>">
		<input type="hidden" name="post_type" value="<?php echo $template_data['post_type']; ?>">
		<input type="hidden" name="origin_post_id" value="<?php echo $template_data['origin_post_id']; ?>">
		<input type="hidden" name="post_id" value="<?php echo $template_data['post_id']; ?>">
		<input type="hidden" name="property_post_id" value="<?php echo $template_data['property_post_id']; ?>">
		<input type="hidden" name="recipients_enc" value="<?php echo $template_data['recipients_enc']; ?>">
		<input type="hidden" name="cc_enc" value="<?php echo $template_data['cc_enc']; ?>">
		<?php wp_nonce_field( $inx_skin_action, "{$inx_skin_action}_nonce" ); ?>

		<?php // Honeypot. ?>
		<div class="inx-team-contact-form__input inx-team-contact-form__input--name--<?php echo $template_data['honeypot_field_name']; ?>">
			<input type="text" name="<?php echo $template_data['honeypot_field_name']; ?>" placeholder="First Name" class="uk-input">
			<div class="inx-team-contact-form__input-error"></div>
		</div>
		<?php // /Honeypot. ?>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--name--name">
			<input type="text" name="name" placeholder="<?php echo $template_data['fields']['name']['placeholder']; ?>" class="uk-input"<?php echo $inx_skin_required['name']; ?>>
			<div class="inx-team-contact-form__input-error"></div>
		</div>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--name--phone">
			<input type="text" name="phone" placeholder="<?php echo $template_data['fields']['phone']['placeholder']; ?>" class="uk-input"<?php echo $inx_skin_required['phone']; ?>>
			<div class="inx-team-contact-form__input-error"></div>
		</div>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--name--email">
			<input type="email" name="email" placeholder="<?php echo $template_data['fields']['email']['placeholder']; ?>" class="uk-input"<?php echo $inx_skin_required['email']; ?>>
			<div class="inx-team-contact-form__input-error"></div>
		</div>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--type--full inx-team-contact-form__input--name--message">
			<textarea name="message" rows="4" placeholder="<?php echo $template_data['fields']['message']['placeholder']; ?>" class="uk-textarea"<?php echo $inx_skin_required['message']; ?>><?php echo $inx_skin_message_default_value; ?></textarea>
			<div class="inx-team-contact-form__input-error"></div>
		</div>

		<div class="inx-team-contact-form__input inx-team-contact-form__input--type--full inx-team-contact-form__input--name--consent">
			<div class="inx-team-contact-form__consent-checkbox">
				<label class="inx-switch">
					<input type="checkbox" name="consent"<?php echo $inx_skin_required['consent']; ?>>
					<div class="inx-switch-slider"></div>
				</label>
			</div>

			<div class="inx-team-contact-form__consent-text">
				<?php echo $template_data['consent_text']; ?>
			</div>
		</div>

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
				<span uk-icon="lock"></span> <?php _e( 'Secure submission', 'immonex-kickstart-team' ); ?>
				<?php endif; ?>
				&nbsp;
			</div>
			<div><button class="inx-team-contact-form__submit inx-button uk-button uk-button-primary" disabled><?php _e( 'Send', 'immonex-kickstart-team' ); ?></button></div>
		</div>

		<div class="inx-team-contact-form__spinner uk-overlay-default uk-position-cover">
			<div class="uk-position-center" uk-spinner="ratio: 2"></div>
		</div>
	</form>
</div>
