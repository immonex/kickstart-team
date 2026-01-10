<?php
/**
 * Default main template for agent list views
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="inx-team-agent-list">
	<?php
	global $wp_query;

	do_action( 'inx_team_before_render_agent_list', have_posts() );

	if ( have_posts() ) :
		?>
	<div class="uk-grid-match" uk-grid>
		<?php
		while ( have_posts() ) :
			the_post();
			?>

		<div class="inx-team-agent-list__item-wrap uk-width-1-2@m">
			<?php
			do_action( 'inx_team_before_render_agent_list_item' );
			do_action(
				'inx_team_render_single_agent',
				false,
				'agent-list/item',
				array(
					'convert_links' => true,
				)
			);
			do_action( 'inx_team_after_render_agent_list_item' );
			?>
		</div>

			<?php
		endwhile;
		?>
	</div>

		<?php
		if (
			empty( $template_data['is_regular_archive_page'] )
			&& ! empty( $template_data['inx-limit-page'] )
			&& $wp_query->found_posts > $template_data['inx-limit-page']
			&& ! empty( $template_data['enable_agent_archive'] )
		) :
			$inx_skin_archive_url = get_post_type_archive_link( 'inx_agent' );

			if ( $inx_skin_archive_url ) :
				foreach ( [ 'inx-author', 'inx-demo', 'inx-agency', 'inx-order' ] as $inx_skin_query_var_name ) {
					if ( ! empty( $template_data[ $inx_skin_query_var_name ] ) ) {
						$inx_skin_archive_url = add_query_arg(
							$inx_skin_query_var_name,
							$template_data[ $inx_skin_query_var_name ],
							$inx_skin_archive_url
						);
					}
				}
				?>
	<div class="inx-team-agent-list__archive-link uk-margin-top">
		<a href="<?php echo esc_url( $inx_skin_archive_url ); ?>"><?php esc_html_e( 'All agents', 'immonex-kickstart-team' ); ?> &#10095;</a>
	</div>

				<?php
			endif;
		endif;
	else :
		?>

	<div class="inx-team-agent-list__no-agents">
		<p><?php echo __( 'No agents found.', 'immonex-kickstart-team' ); ?></p>
	</div>

		<?php
	endif;

	do_action( 'inx_team_after_render_agent_list', have_posts() );
	?>
</div>
