<?php
/**
 * Template for agency single views in property details
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article class="inx-team-single-agency inx-team-single-agency--type--widget">
	<?php if ( ! empty( $template_data['title'] ) ) : ?>
	<div class="inx-team-single-agency__title">
		<?php
		if ( isset( $template_data['before_title'] ) ) {
			echo $template_data['before_title'];
		}

		echo $template_data['title'];

		if ( isset( $template_data['after_title'] ) ) {
			echo $template_data['after_title'];
		}
		?>
	</div>
	<?php endif; ?>

	<?php
	if ( ! empty( $template_data['elements'] ) ) :
		$inx_skin_displayed_values      = array();
		$inx_skin_current_section_order = false;

		foreach ( $template_data['elements'] as $inx_skin_element_key => $inx_skin_element ) :
			$inx_skin_element_type = str_replace( '_', '-', $inx_skin_element_key );

			if ( $inx_skin_element['section_order'] !== $inx_skin_current_section_order ) :
				if ( false !== $inx_skin_current_section_order ) :
					?>
	</div><!-- .inx-team-single-agency__section -->
					<?php
				endif;

				$inx_skin_current_section_order = $inx_skin_element['section_order'];
				?>
	<div class="inx-team-single-agency__section">
				<?php
			endif;

			if ( 'logo' === $inx_skin_element_key ) {
				if ( ! empty( $inx_skin_element['value']['tag'] ) ) {
					$inx_skin_logo_inner_content = $inx_skin_element['value']['tag'];
				} else {
					$inx_skin_logo_inner_content = '<div class="inx-team-single-agency__logo-placeholder">'
						. '<span uk-icon="icon: home; ratio: 8"></span></div>';
				}

				if ( $template_data['is_demo'] ) {
					$inx_skin_logo_inner_content .= PHP_EOL . '<div class="inx-team-single-agency__labels uk-position-top-right">'
						. '<div class="inx-team-label inx-team-label--type--demo">Demo</div>'
						. '</div>';
				}

				$inx_skin_value = wp_sprintf(
					'<div class="inx-team-single-agency__logo">%s</div>',
					$inx_skin_logo_inner_content
				);
			} elseif (
				'company' === $inx_skin_element_key
				&& 'none' !== $template_data['link_type']
				&& ! empty( $inx_skin_element['value']['link'] )
			) {
				$inx_skin_value = $inx_skin_element['value']['link'];
			} elseif ( ! empty( $inx_skin_element['value']['link'] ) ) {
				$inx_skin_value = $inx_skin_element['value']['link'];
			} elseif ( ! empty( $inx_skin_element['value']['raw'] ) ) {
				$inx_skin_value = $inx_skin_element['value']['raw'];
			} else {
				$inx_skin_value = $inx_skin_element['value'];
			}

			if ( 'do_action:' === substr( $inx_skin_value, 0, 10 ) ) {
				$inx_skin_action = substr( $inx_skin_value, 10 );
				do_action(
					// @codingStandardsIgnoreLine
					$inx_skin_action,
					'',
					array(
						'origin_post_id'     => $template_data['agency_id'],
						'contact_form_scope' => $template_data['contact_form_scope'],
						'is_preview'         => $template_data['is_preview'],
					)
				);
				continue;
			}

			if (
				'none' !== $template_data['link_type']
				&& 'logo' === $inx_skin_element_key
				&& $template_data['url']
				&& ! empty( $inx_skin_element['value']['tag'] )
			) {
				if ( 'external' === $template_data['link_type'] ) {
					$inx_skin_value = wp_sprintf(
						'<a href="%s" target="_blank">%s</a>',
						$template_data['url'],
						$inx_skin_value
					);
				} else {
					$inx_skin_value = wp_sprintf(
						'<a href="%s">%s</a>',
						$template_data['url'],
						$inx_skin_value
					);
				}
			}

			if (
				! $inx_skin_value
				|| in_array( $inx_skin_value, $inx_skin_displayed_values, true )
			) {
				continue;
			}

			$inx_skin_displayed_values[] = $inx_skin_value;
			?>
		<div class="inx-team-single-agency__element inx-team-single-agency__element--type--<?php echo $inx_skin_element_type; ?>">
			<?php
			if ( $inx_skin_element['icon'] ) :
				?>
			<div class="inx-team-single-agency__element-icon"><?php echo $inx_skin_element['icon']; ?></div>
				<?php
			endif;
			?>

			<div class="inx-team-single-agency__element-value"><?php echo $inx_skin_value; ?></div>
		</div>
			<?php
		endforeach;

		if ( false !== $inx_skin_current_section_order ) :
			?>
	</div><!-- .inx-team-single-agent__section -->
			<?php
		endif;
	endif;
	?>
</article>
