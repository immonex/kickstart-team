<?php
/**
 * Default main template for agency single views
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

$inx_skin_logo = isset( $template_data['elements']['logo'] ) ?
	$template_data['elements']['logo'] :
	false;
?>
<article class="inx-team-single-agency inx-team-single-agency--type--single inx-container">
	<div class="uk-flex uk-flex-wrap uk-margin-large-bottom">
		<div class="uk-width-1-3@s uk-width-1-4@m uk-width-2-6@l">
			<div class="inx-team-single-agency__logo">
				<?php
				if ( ! empty( $inx_skin_logo['value'] ) ) :
					echo $inx_skin_logo['value']['tag'];
				else :
					?>
				<div class="inx-team-single-agency__logo-placeholder"><span uk-icon="icon: home; ratio: 8"></span></div>
					<?php
				endif;

				if ( $template_data['is_demo'] ) :
					?>
				<div class="inx-team-single-agency__labels uk-position-top-left">
					<div class="inx-team-label inx-team-label--type--demo">Demo</div>
				</div>
					<?php
				endif;
				?>
			</div>
		</div>
		<div class="uk-width-expand@s">
			<?php
			if ( ! empty( $template_data['elements']['company']['value'] ) ) {
				echo wp_sprintf(
					'<h%2$d class="inx-team-single-agency__company uk-margin-small-bottom">%1$s</h%2$d>',
					$template_data['elements']['company']['value']['raw'],
					$inx_skin_heading_level
				);
			}
			?>

			<?php if ( ! empty( $template_data['elements']['about']['value'] ) ) : ?>
			<div class="inx-team-single-agency__about uk-margin-top"><?php echo $template_data['elements']['about']['value']; ?></div>
			<?php endif; ?>

			<?php
			$inx_skin_displayed_values = array();

			foreach ( $template_data['elements'] as $inx_skin_element_key => $inx_skin_element ) :
				if (
					! empty( $inx_skin_element['default_show'] )
					&& in_array( 'single_agency_page', $inx_skin_element['default_show'], true )
					&& ! empty( $inx_skin_element['value'] )
				) :
					if (
						$template_data['convert_links']
						&& ! empty( $inx_skin_element['value']['link'] )
					) {
						$inx_skin_value = $inx_skin_element['value']['link'];
					} elseif ( is_array( $inx_skin_element['value'] ) ) {
						$inx_skin_value = $inx_skin_element['value']['raw'];
					} else {
						$inx_skin_value = $inx_skin_element['value'];
					}

					if ( 'do_action:' === substr( $inx_skin_value, 0, 10 ) ) {
						$inx_skin_action = substr( $inx_skin_value, 10 );
						// @codingStandardsIgnoreLine
						do_action( $inx_skin_action, '', array( 'origin_post_id' => $template_data['agency_id'] ) );
						continue;
					}

					if (
						! $inx_skin_value
						|| in_array( $inx_skin_value, $inx_skin_displayed_values, true )
					) {
						continue;
					}

					$inx_skin_element_type       = str_replace( '_', '-', $inx_skin_element_key );
					$inx_skin_displayed_values[] = $inx_skin_value;
					?>
			<div class="inx-team-single-agency__element inx-team-single-agency__element--type--<?php echo $inx_skin_element_type; ?>">
					<?php if ( $inx_skin_element['icon'] ) : ?>
				<div class="inx-team-single-agency__element-icon"><?php echo $inx_skin_element['icon']; ?></div>
					<?php endif; ?>

				<div class="inx-team-single-agency__element-value"><?php echo $inx_skin_value; ?></div>
			</div>
					<?php
				endif;
			endforeach;
			?>
		</div>
	</div>

	<?php if ( ! empty( $template_data['agent_count'] ) ) : ?>
	<div class="inx-team-single-agency__agents uk-margin-large-bottom">
		<?php
		echo wp_sprintf(
			'<h%2$d class="uk-margin-small-bottom">%1$s</h%2$d>',
			__( 'Our Team', 'immonex-kickstart-team' ),
			$inx_skin_heading_level + 1
		);

		do_action(
			'inx_team_render_agent_list',
			array(
				'inx-agency'            => $template_data['agency_id'],
				'inx-ignore-pagination' => true,
			)
		);
		?>
	</div>
	<?php endif; ?>

	<?php if ( ! empty( $template_data['property_count'] ) ) : ?>
	<div class="inx-team-single-agency__properties uk-margin-large-bottom">
		<?php
		echo wp_sprintf(
			'<h%2$d class="uk-margin-small-bottom">%1$s</h%2$d>',
			__( 'Our Offers', 'immonex-kickstart-team' ),
			$inx_skin_heading_level + 1
		);

		do_action(
			'inx_render_property_list',
			array(
				'inx-agency' => $template_data['agency_id'],
			)
		);

		do_action( 'inx_render_pagination' );
		?>
	</div>
	<?php endif; ?>
</article>
