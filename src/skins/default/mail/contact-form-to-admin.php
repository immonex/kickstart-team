<?php
/**
 * Default FALLBACK mail template for admin mails via unified contact form
 *
 * @package immonex\KickstartTeam
 */

if ( $template_data['prerendered']['property_title_ext_id_url'] ) {
	echo $template_data['prerendered']['property_title_ext_id_url'] . PHP_EOL . PHP_EOL;
}

echo $template_data['prerendered']['merged_form_data'];
