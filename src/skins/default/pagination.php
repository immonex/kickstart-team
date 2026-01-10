<?php
/**
 * Template for agent/agency list pagination
 *
 * @package immonex\KickstartTeam
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$inx_skin_pagination_args = array(
	'prev_text' => __( 'Previous page', 'immonex-kickstart-team' ),
	'next_text' => __( 'Next page', 'immonex-kickstart-team' ),
	'mid_size'  => 2,
);
$inx_skin_pagination      = get_the_posts_pagination( $inx_skin_pagination_args );
$inx_skin_cpt_class       = ! empty( $template_data['cpt'] ) ? " inx-pagination--cpt--{$template_data['cpt']}" : '';

if ( ! empty( $inx_skin_pagination ) ) :
	?>
<div class="inx-pagination<?php echo $inx_skin_cpt_class; ?> uk-margin-large-top">
	<?php echo $inx_skin_pagination; ?>
</div>
	<?php
endif;
