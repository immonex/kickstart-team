<?php
/**
 * Default main template for agency list views
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="inx-team-agency-list uk-grid-match" uk-grid>
	<?php
	do_action( 'inx_team_before_render_agency_list', have_posts() );

	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
			?>

	<div class="inx-team-agency-list__item-wrap uk-width-1-2@m">
			<?php
			do_action( 'inx_team_before_render_agency_list_item' );
			do_action(
				'inx_team_render_single_agency',
				false,
				'agency-list/item',
				array(
					'convert_links' => true,
				)
			);
			do_action( 'inx_team_after_render_agency_list_item' );
			?>
	</div>

			<?php
		endwhile;
	else :
		?>

	<div class="inx-team-agent-list__no-agencies">
		<p><?php echo __( 'No agencies found.', 'immonex-kickstart-team' ); ?></p>
	</div>

		<?php
	endif;

	do_action( 'inx_team_after_render_agency_list', have_posts() );
	?>
</div>
