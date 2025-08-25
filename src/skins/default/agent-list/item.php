<?php
/**
 * Example agent list item template
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $immonex_kickstart;

$inx_skin_photo = isset( $template_data['elements']['photo'] ) ?
	$template_data['elements']['photo'] :
	false;
?>
<article class="inx-team-agent-list-item inx-team-agent-list-item--type--card<?php echo ! $template_data['is_public'] ? ' inx-team-agent-list-item--no-footer' : ''; ?> uk-card uk-card-default">
	<div class="uk-flex uk-flex-wrap">
		<div class="inx-team-agent-list-item__photo">
			<?php
			if ( $template_data['url'] ) :
				?>
			<a href="<?php echo $template_data['url']; ?>" tabindex="-1" aria-hidden="true">
				<?php
			endif;

			if ( ! empty( $inx_skin_photo['value']['url'] ) ) {
				echo wp_sprintf(
					'<div class="inx-squared-image uk-box-shadow-medium" style="background-image:url(%s)"></div>',
					esc_url( $inx_skin_photo['value']['url'] )
				);
			} else {
				echo '<div class="inx-team-agent-list-item__photo-placeholder"><span uk-icon="icon: user; ratio: 5"></span></div>';
			}

			if ( $template_data['url'] ) :
				?>
			</a>
				<?php
			endif;

			if ( $template_data['is_demo'] ) :
				?>
			<div class="inx-team-agent-list-item__labels uk-position-top-right">
				<div class="inx-team-label inx-team-label--type--demo">Demo</div>
			</div>
				<?php
			endif;
			?>
		</div><!-- inx-team-agent-list-item__photo -->

		<div class="inx-team-agent-list-item__body uk-width-expand">
			<?php
			if ( ! empty( $template_data['elements']['full_name_incl_title']['value'] ) ) :
				?>
			<div class="inx-team-agent-list-item__name">
				<?php
				if ( $template_data['url'] ) {
					echo wp_sprintf(
						'<a href="%s">%s</a>',
						$template_data['url'],
						$template_data['elements']['full_name_incl_title']['value']
					);
				} else {
					echo $template_data['elements']['full_name_incl_title']['value'];
				}
				?>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_data['elements']['position_incl_company']['value']['link'] ) ) : ?>
			<div class="inx-team-agent-list-item__position"><?php echo $template_data['elements']['position_incl_company']['value']['link']; ?></div>
			<?php endif; ?>

			<?php
			if ( ! empty( $template_data['elements'] ) ) :
				$inx_skin_displayed_values      = array();
				$inx_skin_current_section_order = false;
				?>
			<div class="inx-team-agent-list-item__var-elements uk-margin-top">
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
				</div><!-- .inx-team-agent-list-item__section -->
							<?php
						endif;

						$inx_skin_current_section_order = $inx_skin_element['section_order'];
						?>
				<div class="inx-team-agent-list-item__section">
						<?php
					endif;

					if (
						! empty( $template_data['convert_links'] )
						&& ! empty( $inx_skin_element['value']['link_internal'] )
					) {
						$inx_skin_value = $inx_skin_element['value']['link_internal'];
					} elseif (
						! empty( $template_data['convert_links'] )
						&& ! empty( $inx_skin_element['value']['link'] )
					) {
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
					<div class="inx-team-agent-list-item__element inx-team-agent-list-item__element--type--<?php echo $inx_skin_element_type; ?>">
					<?php
					if ( $inx_skin_element['icon'] ) :
						?>
						<div class="inx-team-agent-list-item__element-icon"><?php echo $inx_skin_element['icon']; ?></div>
						<?php
					endif;
					?>

						<div class="inx-team-agent-list-item__element-value"><?php echo $inx_skin_value; ?></div>
					</div><!-- .inx-team-agent-list-item__element -->
					<?php
				endforeach;

				if ( false !== $inx_skin_current_section_order ) :
					?>
				</div><!-- .inx-team-agent-list-item__section -->
				<?php endif; ?>
			</div><!-- .inx-team-agent-list-item__var-elements -->
			<?php endif; ?>
		</div><!-- .inx-team-agent-list-item__body -->
	</div><!-- .uk-flex -->

	<?php if ( $template_data['is_public'] ) : ?>
	<div class="inx-team-agent-list-item__footer">
		<?php
		if ( (int) $template_data['property_count'] > 0 ) {
			$inx_skin_link_text = __( 'My Offers', 'immonex-kickstart-team' );
		} else {
			$inx_skin_link_text = __( 'About Me', 'immonex-kickstart-team' );
		}
		?>
		<a class="inx-link inx-gradient--type--action inx-inverse" href="<?php echo get_permalink(); ?>"><?php echo $inx_skin_link_text; ?></a>
	</div><!-- .inx-team-agent-list-item__footer -->
	<?php endif; ?>
</article>
