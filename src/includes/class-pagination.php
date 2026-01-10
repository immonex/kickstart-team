<?php
/**
 * Class Pagination
 *
 * @package immonex\KickstartTeam
 */

namespace immonex\Kickstart\Team;

/**
 * Pagination rendering.
 */
class Pagination {

	/**
	 * Default pagination template file
	 */
	const DEFAULT_PAGINATION_TEMPLATE = 'pagination';

	/**
	 * Plugin options and other component configuration data
	 *
	 * @var mixed[]
	 */
	private $config;

	/**
	 * Helper/Utility objects
	 *
	 * @var object[]
	 */
	private $utils;

	/**
	 * Constructor
	 *
	 * @since 1.8.0
	 *
	 * @param mixed[]  $config Plugin options and other component configuration data.
	 * @param object[] $utils  Helper/Utility objects.
	 */
	public function __construct( $config, $utils ) {
		$this->config = $config;
		$this->utils  = $utils;

		/**
		 * Plugin-specific actions and filters
		 */

		add_action( 'inx_team_render_pagination', [ $this, 'render' ] );
	} // __construct

	/**
	 * Render and output pagination (PHP template).
	 *
	 * @since 1.8.0
	 *
	 * @param mixed[] $atts Rendering attributes.
	 */
	public function render( $atts = [] ) {
		$template      = ! empty( $atts['template'] ) ?
			$atts['template'] :
			self::DEFAULT_PAGINATION_TEMPLATE;
		$template_data = array_merge(
			$this->config,
			$atts
		);
		// Internal filter.
		$output = apply_filters(
			'inx_team_rendered_pagination_template_contents',
			$this->utils['template']->render_php_template( $template, $template_data ),
			$template,
			$template_data,
			$atts
		);

		echo $output;
	} // render

} // Pagination
