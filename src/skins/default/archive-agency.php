<?php
/**
 * Default archive template for the agency custom post type
 *
 * @package immonex\KickstartTeam
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

<div class="inx-team-agency-archive" role="main">
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

	<div class="inx-team-agency-archive__main-content inx-container">
		<?php
		do_action(
			'inx_team_render_agency_list',
			array(
				'is_regular_archive_page' => true,
				'inx-order'               => 'title ASC',
			)
		);
		do_action(
			'inx_team_render_pagination',
			array(
				'is_regular_archive_page' => true,
				'cpt'                     => 'agency',
			)
		);
		?>
	</div>
</div>

<?php
get_footer();
