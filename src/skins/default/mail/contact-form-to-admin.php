<?php
/**
 * Default mail template for admin mails via unified contact form
 *
 * @package immonex-kickstart-team
 */

if ( $template_data['prerendered']['property_title_ext_id'] ) {
	echo $template_data['prerendered']['property_title_ext_id'] . PHP_EOL;
	echo $template_data['prerendered']['property_url'] . PHP_EOL . PHP_EOL;
}

echo $template_data['prerendered']['merged_form_data'];
