<?php
/**
 * Default mail template for receipt confirmations of messages sent
 * via unified contact form
 *
 * @package immonex\KickstartTeam
 */

echo __( 'Good day!', 'immonex-kickstart-team' );
echo PHP_EOL . PHP_EOL;

if ( ! empty( $template_data['property'] ) ) {
	echo __( 'Thanks for your inquiry on the following property:', 'immonex-kickstart-team' );
	echo PHP_EOL . PHP_EOL;

	echo $template_data['prerendered']['property_title_ext_id'] . PHP_EOL;
	echo $template_data['prerendered']['property_url'];
} else {
	echo __( 'Thanks for your message!', 'immonex-kickstart-team' );
}

echo PHP_EOL . PHP_EOL;

if (
	! empty( $template_data['sender_info'] )
	&& 'agent' === $template_data['sender_info']['type']
) {
	echo __( 'I will get in touch with you as soon as possible.', 'immonex-kickstart-team' );
	echo PHP_EOL . PHP_EOL;
	echo __( 'Best regards,', 'immonex-kickstart-team' );

	if (
		! empty( $template_data['sender_info']['name'] )
		|| ! empty( $template_data['sender_info']['company'] )
	) {
		echo PHP_EOL . PHP_EOL;
	}

	if ( ! empty( $template_data['sender_info']['name'] ) ) {
		echo $template_data['sender_info']['name'] . PHP_EOL;
	}

	if ( ! empty( $template_data['sender_info']['company'] ) ) {
		echo $template_data['sender_info']['company'] . PHP_EOL;
	}
} else {
	echo __( 'We will get in touch with you as soon as possible.', 'immonex-kickstart-team' );
	echo PHP_EOL . PHP_EOL;
	echo __( 'Best regards,', 'immonex-kickstart-team' );
	echo PHP_EOL . PHP_EOL;
	echo __( 'The team of', 'immonex-kickstart-team' ) . ' ';
	echo ! empty( $template_data['sender_info']['company'] ) ?
		$template_data['sender_info']['company'] :
		$template_data['prerendered']['site_title'];
	echo PHP_EOL;
}
