<?php
/**
 * Default page template for agent CPT single views
 *
 * @package immonex-kickstart-team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<div class="uk-container" role="main">
<?php
while ( have_posts() ) {
	the_post();
	do_action(
		'inx_team_render_single_agent',
		false,
		'',
		array(
			'is_regular_single_page' => true,
			'convert_links'          => true,
		)
	);
}
?>
</div>
<?php
get_footer();
