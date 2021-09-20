<?php
/**
 * Example agency list item template
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $immonex_kickstart;

$inx_skin_logo = isset( $template_data['elements']['logo'] ) ?
	$template_data['elements']['logo'] :
	false;
?>
<article class="inx-team-agency-list-item inx-team-agency-list-item--type--card uk-card uk-card-default">
	<div class="uk-flex uk-flex-wrap">
		<div class="inx-team-agency-list-item__logo">
			<div class="inx-team-agency-list-item__logo-wrap">
			<?php
			if ( $template_data['url'] ) :
				?>
			<a href="<?php echo $template_data['url']; ?>">
				<?php
			endif;
			echo ! empty( $inx_skin_logo['value'] ) ?
				$inx_skin_logo['value']['tag'] :
				'<span uk-icon="icon: home; ratio: 6"></span>';

			if ( $template_data['url'] ) :
				?>
			</a>
				<?php
			endif;
			?>
			</div><!-- .inx-team-agency-list-item__logo-wrap -->
		</div><!-- .inx-team-agency-list-item__logo -->

		<div class="inx-team-agency-list-item__body uk-width-expand">
			<div class="inx-team-agency-list-item__company">
				<?php echo $template_data['elements']['company']['value']['link']; ?>
			</div>

			<?php if ( ! empty( $template_data['elements']['city']['value'] ) ) : ?>
			<div class="inx-team-agent-list-item__city"><?php echo $template_data['elements']['city']['value']; ?></div>
			<?php endif; ?>

			<?php
			if ( ! empty( $template_data['elements'] ) ) :
				$inx_skin_displayed_values      = array();
				$inx_skin_current_section_order = false;
				?>
			<div class="inx-team-agency-list-item__var-elements uk-margin-top">
				<?php
				foreach ( $template_data['elements'] as $inx_skin_element_key => $inx_skin_element ) :
					if (
						empty( $inx_skin_element['default_show'] )
						|| ! in_array( 'list_item', $inx_skin_element['default_show'], true )
						|| empty( $inx_skin_element['value'] )
					) {
						continue;
					}

					$inx_skin_element_type = str_replace( '_', '-', $inx_skin_element_key );

					if ( $inx_skin_element['section_order'] !== $inx_skin_current_section_order ) :
						if ( false !== $inx_skin_current_section_order ) :
							?>
				</div><!-- .inx-team-agency-list-item__section -->
							<?php
						endif;

						$inx_skin_current_section_order = $inx_skin_element['section_order'];
						?>
				<div class="inx-team-agency-list-item__section">
						<?php
					endif;

					if ( ! empty( $inx_skin_element['value']['link'] ) ) {
						$inx_skin_value = $inx_skin_element['value']['link'];
					} elseif ( is_array( $inx_skin_element['value'] ) ) {
						$inx_skin_value = $inx_skin_element['value']['raw'];
					} else {
						$inx_skin_value = $inx_skin_element['value'];
					}

					if (
						! $inx_skin_value
						|| in_array( $inx_skin_value, $inx_skin_displayed_values, true )
					) {
						continue;
					}

					$inx_skin_displayed_values[] = $inx_skin_value;
					?>
					<div class="inx-team-agency-list-item__element inx-team-agency-list-item__element--type--<?php echo $inx_skin_element_type; ?>">
					<?php
					if ( $inx_skin_element['icon'] ) :
						?>
						<div class="inx-team-agency-list-item__element-icon"><?php echo $inx_skin_element['icon']; ?></div>
						<?php
					endif;
					?>

						<div class="inx-team-agency-list-item__element-value"><?php echo $inx_skin_value; ?></div>
					</div><!-- .inx-team-agency-list-item__element -->
					<?php
				endforeach;

				if ( false !== $inx_skin_current_section_order ) :
					?>
				</div><!-- .inx-team-agency-list-item__section -->
				<?php endif; ?>
			</div><!-- .inx-team-agency-list-item__var-elements -->
			<?php endif; ?>
		</div><!-- .inx-team-agency-list-item__body -->
	</div><!-- .uk-flex -->

	<?php if ( $template_data['is_public'] ) : ?>
	<div class="inx-team-agency-list-item__footer">
		<?php $inx_skin_link_text = __( 'Team & Offers', 'immonex-kickstart-team' ); ?>
		<a class="inx-link inx-gradient--type--action inx-inverse" href="<?php echo get_permalink(); ?>"><?php echo $inx_skin_link_text; ?></a>
	</div><!-- .inx-team-agency-list-item__footer -->
	<?php endif; ?>

	<?php if ( $template_data['is_demo'] ) : ?>
	<div class="inx-team-agency-list-item__labels uk-position-top-left">
		<div class="inx-team-label inx-team-label--type--demo">Demo</div>
	</div>
	<?php endif; ?>
</article>
