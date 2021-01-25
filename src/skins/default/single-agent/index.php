<?php
/**
 * Default main template for agent single views
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

$inx_skin_photo = isset( $template_data['elements']['photo'] ) ?
	$template_data['elements']['photo'] :
	false;
?>
<article class="inx-team-single-agent inx-team-single-agent--type--single inx-container">
	<div class="uk-flex uk-flex-wrap uk-margin-large-bottom">
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

		<div class="inx-team-single-agent__contact-data uk-width-expand@s">
			<?php
			if ( ! empty( $template_data['elements']['full_name_incl_title']['value'] ) ) {
				echo wp_sprintf(
					'<h%2$d class="uk-margin-small-bottom">%1$s</h%2$d>',
					$template_data['elements']['full_name_incl_title']['value'],
					$inx_skin_heading_level
				);
			}
			?>

			<?php if ( ! empty( $template_data['elements']['position_incl_company']['value'] ) ) : ?>
			<div class="inx-team-single-agent__position uk-margin-bottom">
				<?php echo $template_data['elements']['position_incl_company']['value']['link']; ?>
			</div>
			<?php endif; ?>

			<?php if ( ! empty( $template_data['content'] ) ) : ?>
			<div class="inx-team-single-agent__bio uk-margin-bottom"><?php echo $template_data['content']; ?></div>
			<?php endif; ?>

			<?php
			$inx_skin_displayed_values = array();

			foreach ( $template_data['elements'] as $inx_skin_element_key => $inx_skin_element ) :
				if (
					! empty( $inx_skin_element['default_show'] )
					&& in_array( 'single_agent_page', $inx_skin_element['default_show'] )
					&& ! empty( $inx_skin_element['value'] )
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
						do_action( $inx_skin_action, '', array( 'origin_post_id' => $template_data['agent_id'] ) );
						continue;
					}

					if (
						! $inx_skin_value
						|| in_array( $inx_skin_value, $inx_skin_displayed_values )
					) {
						continue;
					}

					$inx_skin_element_type       = str_replace( '_', '-', $inx_skin_element_key );
					$inx_skin_displayed_values[] = $inx_skin_value;
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

	<?php if ( ! empty( $template_data['property_count'] ) ) : ?>
	<div class="inx-team-single-agent__properties uk-margin-large-bottom">
		<?php
		echo wp_sprintf(
			'<h%2$d class="uk-margin-small-bottom">%1$s</h%2$d>',
			__( 'My Offers', 'immonex-kickstart-team' ),
			$inx_skin_heading_level + 1
		);

		do_action(
			'inx_render_property_list',
			array(
				'inx-agent' => $template_data['agent_id'],
			)
		);

		do_action( 'inx_render_pagination' );
		?>
	</div>
	<?php endif; ?>

	<?php
	$inx_skin_agency = $template_data['agency_id'] && $template_data['is_public_agency'] ?
		get_post( $template_data['agency_id'] ) :
		false;

	if ( $inx_skin_agency ) :
		$inx_skin_agency_excerpt = get_the_excerpt( $inx_skin_agency );
		$inx_skin_agency_url     = get_permalink( $template_data['agency_id'] );
		?>
	<div class="inx-team-single-agent__footer inx-quiet-bg uk-padding-small uk-margin-large-bottom">
		<div class="inx-team-single-agent__agency-logo uk-padding-small">
			<a href="<?php echo $inx_skin_agency_url; ?>">
				<?php echo get_the_post_thumbnail( $inx_skin_agency->ID, 'thumbnail' ); ?>
			</a>
		</div>

		<div class="inx-team-single-agent__agency-info uk-padding-small">
			<?php if ( $inx_skin_agency_excerpt ) : ?>
			<p>
				<?php echo $inx_skin_agency_excerpt; ?>
			</p>
			<?php endif; ?>

			<a href="<?php echo $inx_skin_agency_url; ?>">
				<?php echo __( 'More about', 'immonex-kickstart-team' ) . ' ' . $inx_skin_agency->post_title; ?>
			</a>
		</div>
	</div>
	<?php endif; ?>
</article>
