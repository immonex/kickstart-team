<?php
/**
 * Template for replacing the Kickstart default property contact section
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $immonex_kickstart;

$inx_skin_title_level = isset( $immonex_kickstart ) ?
	$immonex_kickstart->heading_base_level + 1 :
	2;

if ( 'auto' === strtolower( $template_data['default_contact_section_title'] ) ) {
	switch ( $template_data['agent_gender'] ) {
		case 'm':
			$inx_skin_title = _x( 'Your contact with us', 'male', 'immonex-kickstart-team' );
			break;
		case 'f':
			$inx_skin_title = _x( 'Your contact with us', 'female', 'immonex-kickstart-team' );
			break;
		default:
			$inx_skin_title = _x( 'Your contact with us', 'gender neutral', 'immonex-kickstart-team' );
	}
} else {
	$inx_skin_title = $template_data['default_contact_section_title'];
}

$inx_skin_photo = isset( $template_data['elements']['photo'] ) ?
	$template_data['elements']['photo'] :
	false;
?>
<div class="inx-container inx-single-property__section inx-team-single-agent inx-team-single-agent--type--single uk-margin-large-bottom">
	<?php
	if ( $inx_skin_title ) {
		echo wp_sprintf(
			'<h%2$d class="inx-single-property__section-title uk-heading-divider">%1$s</h%2$d>',
			$inx_skin_title,
			$inx_skin_title_level
		);
	}
	?>

	<div class="uk-flex uk-flex-wrap">
		<div class="inx-team-single-agent__photo-wrap uk-width-1-3@s uk-width-1-4@m uk-width-2-6@l">
			<div class="inx-team-single-agent__photo inx-squared-image"
				<?php
				if ( ! empty( $inx_skin_photo['value'] ) ) {
					echo wp_sprintf(
						'style="background-image:url(%s)"',
						esc_url( $inx_skin_photo['value']['url'] )
					);
				}
				?>
			>
				<?php
				if ( empty( $inx_skin_photo['value'] ) ) :
					?>
				<div class="inx-team-single-agent__photo-placeholder"><span uk-icon="icon: user; ratio: 8"></span></div>
					<?php
				endif;

				if ( $template_data['is_demo'] ) :
					?>
				<div class="inx-team-single-agent__labels uk-position-top-right">
					<div class="inx-team-label inx-team-label--type--demo">Demo</div>
				</div>
					<?php
				endif;
				?>
			</div>
		</div>

		<div class="uk-width-expand@s">
			<?php
			if ( ! empty( $template_data['elements']['full_name_incl_title']['value'] ) ) {
				$inx_skin_name = wp_sprintf(
					'<h%2$d class="inx-team-single-agent__name uk-margin-remove-top uk-margin-small-bottom">%1$s</h%2$d>',
					$template_data['elements']['full_name_incl_title']['value'],
					$inx_skin_title_level + 1
				);

				if (
					'none' !== $template_data['link_type']
					&& $template_data['url']
				) {
					if ( 'external' === $template_data['link_type'] ) {
						$inx_skin_name = wp_sprintf(
							'<a href="%s" target="_blank">%s</a>',
							$template_data['url'],
							$inx_skin_name
						);
					} else {
						$inx_skin_name = wp_sprintf(
							'<a href="%s">%s</a>',
							$template_data['url'],
							$inx_skin_name
						);
					}
				}

				echo $inx_skin_name;
			}
			?>

			<?php if ( ! empty( $template_data['elements']['position_incl_company']['value'] ) ) : ?>
			<div class="inx-team-single-agent__position uk-margin-bottom">
				<?php echo $template_data['elements']['position_incl_company']['value']['link']; ?>
			</div>
			<?php endif; ?>

			<?php
			$inx_skin_displayed_elements = array( 'photo', 'full_name_incl_title', 'position_incl_company' );
			$inx_skin_displayed_values   = array();

			foreach ( $template_data['elements'] as $inx_skin_element_key => $inx_skin_element ) :
				if (
					! in_array( $inx_skin_element_key, $inx_skin_displayed_elements, true )
					&& ! empty( $inx_skin_element['value'] )
					&& (
						! empty( $template_data['show_all_elements'] )
						|| (
							! empty( $inx_skin_element['default_show'] )
							&& in_array( $template_data['type'], $inx_skin_element['default_show'], true )
						)
					)
				) :
					if ( ! empty( $inx_skin_element['value']['link'] ) ) {
						$inx_skin_value = $inx_skin_element['value']['link'];
					} elseif ( ! empty( $inx_skin_element['value']['raw'] ) ) {
						$inx_skin_value = $inx_skin_element['value']['raw'];
					} else {
						$inx_skin_value = $inx_skin_element['value'];
					}

					if ( 'do_action:' === substr( $inx_skin_value, 0, 10 ) ) {
						$inx_skin_action = substr( $inx_skin_value, 10 );
						// @codingStandardsIgnoreLine
						do_action(
							// @codingStandardsIgnoreLine
							$inx_skin_action,
							'',
							array(
								'origin_post_id'     => $template_data['agent_id'],
								'contact_form_scope' => $template_data['contact_form_scope'],
								'is_preview'         => $template_data['is_preview'],
							)
						);
						continue;
					}

					if (
						! $inx_skin_value
						|| in_array( $inx_skin_value, $inx_skin_displayed_values, true )
					) {
						continue;
					}

					$inx_skin_element_type         = str_replace( '_', '-', $inx_skin_element_key );
					$inx_skin_displayed_elements[] = $inx_skin_element_key;
					$inx_skin_displayed_values[]   = $inx_skin_value;
					?>
			<div class="inx-team-single-agent__element inx-team-single-agent__element--type--<?php echo $inx_skin_element_type; ?>">
					<?php if ( $inx_skin_element['icon'] ) : ?>
				<div class="inx-team-single-agent__element-icon"><?php echo $inx_skin_element['icon']; ?></div>
					<?php endif; ?>

				<div class="inx-team-single-agent__element-value"><?php echo $inx_skin_value; ?></div>
			</div>
					<?php
				endif;
			endforeach;
			?>
		</div>
	</div>
</div>
