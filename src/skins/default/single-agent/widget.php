<?php
/**
 * Template for agent single views in property details
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

switch ( $template_data['agent_gender'] ) {
	case 'm':
		$inx_skin_title = _x( 'Your contact', 'male', 'immonex-kickstart-team' );
		break;
	case 'f':
		$inx_skin_title = _x( 'Your contact', 'female', 'immonex-kickstart-team' );
		break;
	default:
		$inx_skin_title = _x( 'Your contact', 'gender neutral', 'immonex-kickstart-team' );
}


$inx_skin_link_type = false;
if (
	! empty( $template_data['link_type'] )
	&& 'none' !== $template_data['link_type']
) {
	$inx_skin_link_type = $template_data['link_type'];
}
?>
<article class="inx-team-single-agent inx-team-single-agent--type--widget">
	<?php if ( ! empty( $template_data['title'] ) ) : ?>
	<div>
		<?php
		if ( isset( $template_data['before_title'] ) ) {
			echo $template_data['before_title'];
		}

		echo strtolower( $template_data['title'] ) === 'auto' ? $inx_skin_title : $template_data['title'];

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
	</div><!-- .inx-team-single-agent__section -->
					<?php
				endif;

				$inx_skin_current_section_order = $inx_skin_element['section_order'];
				?>
	<div class="inx-team-single-agent__section">
				<?php
			endif;

			if ( 'photo' === $inx_skin_element_key ) {
				$inx_skin_photo_inner_content = '';
				if ( empty( $inx_skin_element['value'] ) ) {
					$inx_skin_photo_inner_content = '<div class="inx-team-single-agent__photo-placeholder"><span uk-icon="icon: user; ratio: 8"></span></div>';
				}

				if ( $template_data['is_demo'] ) {
					$inx_skin_photo_inner_content .= PHP_EOL
						. '<div class="inx-team-single-agent__labels uk-position-top-right">'
						. '<div class="inx-team-label inx-team-label--type--demo">Demo</div>'
						. '</div>';
				}

				$inx_skin_value = wp_sprintf(
					'<div class="inx-team-single-agent__photo inx-squared-image"%s>%s</div>',
					! empty( $inx_skin_element['value'] ) ? ' style="background-image:url(' . $inx_skin_element['value']['url'] . ')"' : '',
					$inx_skin_photo_inner_content
				);
			} elseif ( ! empty( $inx_skin_element['value']['link'] ) ) {
				$inx_skin_value = $inx_skin_element['value']['link'];
			} elseif ( is_array( $inx_skin_element['value'] ) ) {
				$inx_skin_value = $inx_skin_element['value']['raw'];
			} else {
				$inx_skin_value = $inx_skin_element['value'];
			}

			if ( 'do_action:' === substr( $inx_skin_value, 0, 10 ) ) {
				$inx_skin_action = substr( $inx_skin_value, 10 );
				// @codingStandardsIgnoreLine
				do_action( $inx_skin_action, '', array( 'origin_post_id' => $template_data['agent_id'] ) );
				continue;
			}

			if (
				'none' !== $template_data['link_type']
				&& in_array( $inx_skin_element_key, array( 'photo', 'full_name', 'full_name_incl_title' ), true )
				&& $template_data['url']
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
		<div class="inx-team-single-agent__element inx-team-single-agent__element--type--<?php echo $inx_skin_element_type; ?>">
			<?php
			if ( $inx_skin_element['icon'] ) :
				?>
			<div class="inx-team-single-agent__element-icon"><?php echo $inx_skin_element['icon']; ?></div>
				<?php
			endif;
			?>

			<div class="inx-team-single-agent__element-value"><?php echo $inx_skin_value; ?></div>
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
