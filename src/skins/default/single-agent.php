<?php
/**
 * Default page template for agent CPT single views
 *
 * @package immonex\KickstartTeam
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

	if ( ! post_password_required() ) {
		do_action( 'inx_team_before_render_single_agent' );
		do_action(
			'inx_team_render_single_agent',
			false,
			'',
			array(
				'is_regular_single_page' => true,
				'convert_links'          => true,
			)
		);
		do_action( 'inx_team_after_render_single_agent' );
	} else {
		echo get_the_password_form();
	}
}
?>
</div>
<?php
get_footer();
