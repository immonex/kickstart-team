<?php
/**
 * Default archive template for the agent custom post type
 *
 * @package immonex-kickstart-team
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $immonex_kickstart;

$inx_skin_heading_level = isset( $immonex_kickstart ) ?
	$immonex_kickstart->heading_base_level + 1 :
	2;

get_header();
?>

<div class="inx-team-agent-archive" role="main">
	<header class="inx-page-header inx-container">
		<?php
		the_archive_title(
			'<h' . $inx_skin_heading_level . ' class="inx-page-title">',
			'</h' . $inx_skin_heading_level . '>'
		);
		the_archive_description(
			'<div class="inx-taxonomy-description">',
			'</div>'
		);
		?>
	</header>

	<div class="inx-team-agent-archive__main-content inx-container">
		<?php
		do_action(
			'inx_team_render_agent_list',
			array(
				'is_regular_archive_page' => true,
			)
		);
		?>
	</div>
</div>

<?php
get_footer();
