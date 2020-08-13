<?php
/**
 * Default mail template for admin mails via unified contact form
 *
 * @package immonex-kickstart-team
 */

if ( count( $template_data['form_data'] ) > 0 ) {
	if ( ! function_exists( 'inx_skin_get_caption' ) ) {
		/**
		 * Get the most suitable caption for the given field.
		 *
		 * @since 1.0.0
		 *
		 * @param string $inx_skin_field Field key (name).
		 *
		 * @return string Caption.
		 */
		function inx_skin_get_caption( $inx_skin_field ) {
			$caption = '';
			if ( ! empty( $inx_skin_field['caption_mail'] ) ) {
				$caption = $field['caption_mail'];
			} elseif ( ! empty( $field['caption'] ) ) {
				$caption = $field['caption'];
			}

			return $caption;
		} // inx_skin_get_caption
	}

	if ( ! function_exists( 'inx_skin_get_max_caption_length' ) ) {
		/**
		 * Get the maximum field caption length.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $form_data Form data.
		 *
		 * @return int Max. caption length.
		 */
		function inx_skin_get_max_caption_length( $form_data ) {
			$max_caption_length = 4;

			foreach ( $form_data as $field_name => $field ) {
				$caption = inx_skin_get_caption( $field );

				if (
					$caption
					&& ! in_array( $field['type'], array( 'textarea', 'checkbox' ) )
				) {
					$max_caption_length = strlen( $caption ) + 2;
				}
			}

			return $max_caption_length;
		} // inx_skin_get_max_caption_length
	}

	$inx_skin_max_caption_length = inx_skin_get_max_caption_length( $template_data['form_data'] );
	$inx_skin_fields_inserted    = 0;

	if ( ! empty( $template_data['property'] ) ) {
		$inx_skin_property = $template_data['property'];

		echo wp_sprintf(
			'%s%s' . PHP_EOL,
			$inx_skin_property['title'],
			! empty( $inx_skin_property['external_id'] ) ? ' (' . $inx_skin_property['external_id'] . ')' : ''
		);
		echo $inx_skin_property['url'] . PHP_EOL . PHP_EOL;
	}

	foreach ( $template_data['form_data'] as $inx_skin_field_name => $inx_skin_field ) {
		if (
			'checkbox' === $inx_skin_field['type']
			|| empty( $inx_skin_field['value'] )
		) {
			continue;
		}

		$inx_skin_caption = inx_skin_get_caption( $inx_skin_field );

		if ( 'textarea' === $inx_skin_field['type'] ) {
			$inx_skin_divider = str_repeat( '-', strlen( $inx_skin_field['caption'] ) + 1 ) . PHP_EOL;

			if ( $inx_skin_fields_inserted > 0 ) {
				echo PHP_EOL;
			}
			if ( $inx_skin_caption ) {
				echo $inx_skin_caption . ':' . PHP_EOL;
			}
			echo $inx_skin_divider;
			echo $inx_skin_field['value'] . PHP_EOL;
			echo $inx_skin_divider;
		} else {
			if ( $inx_skin_caption ) {
				echo str_pad( $inx_skin_caption . ':', $inx_skin_max_caption_length );
			}
			echo $inx_skin_field['value'] . PHP_EOL;
		}

		$inx_skin_fields_inserted++;
	}
}
